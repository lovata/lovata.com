<?php namespace Tailor\Behaviors;

use System;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Cms\Classes\Controller;
use Tailor\Classes\Blueprint;
use Tailor\Classes\Blueprint\SingleBlueprint;
use Backend\Classes\ControllerBehavior;
use Tailor\Models\PreviewToken;
use ApplicationException;
use SystemException;

/**
 * PreviewController adds the ability to preview a record based on a CMS page
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class PreviewController extends ControllerBehavior
{
    use \Backend\Traits\FormModelSaver;

    /**
     * @var Page previewPageObj
     */
    protected $previewPageObj;

    /**
     * @var Blueprint activeSource
     */
    protected $activeSource;

    /**
     * @var string componentName
     */
    protected $componentName;

    /**
     * setPreviewPageContext
     */
    public function setPreviewPageContext(string $componentName, Blueprint $activeSource)
    {
        $this->componentName = $componentName;
        $this->activeSource = $activeSource;
    }

    /**
     * onPreview previews the record
     */
    public function onPreview($recordId = null)
    {
        if (!System::hasModule('Cms')) {
            throw new ApplicationException('Cannot preview without the CMS module installed!');
        }

        if (!$page = $this->getPreviewPage()) {
            throw new ApplicationException('There is no page that uses the necessary component');
        }

        // Get model from page action
        $model = $this->controller->formGetModel();

        // Update the record for preview
        $this->performSaveOnModel(
            $model,
            $this->controller->formGetWidget()->getSaveData(),
            $this->controller->formGetWidget()->getSessionKey()
        );

        // Get the URL from the CMS controller
        $controller = new Controller(Theme::getEditTheme());
        $url = $controller->pageUrl($page->getBaseFileName(), [
            'id' => $model->id,
            'slug' => $model->slug,
            'fullslug' => $model->fullslug
        ]);

        // Generate preview token
        $token = PreviewToken::createTokenForUrl($url, [
            'id' => $model->getKey()
        ]);

        // Attach to URL
        $url .= '?' . http_build_query([
            '_preview_token' => $token->token,
        ]);

        return $url;
    }

    /**
     * hasPreviewPage
     */
    public function hasPreviewPage(): bool
    {
        return $this->getPreviewPage() !== null;
    }

    /**
     * getPreviewPage
     */
    protected function getPreviewPage(): ?Page
    {
        if ($this->previewPageObj !== null) {
            return $this->previewPageObj;
        }

        if (!$blueprint = $this->activeSource) {
            throw new SystemException('Missing a blueprint source in the controller.');
        }

        $handleName = $blueprint->handle;
        $componentName = $this->componentName;

        // Find page with component
        $page = Page::whereComponent($componentName, 'handle', $handleName)->first();

        if ($page) {
            return $this->previewPageObj = $page;
        }

        return null;
    }
}
