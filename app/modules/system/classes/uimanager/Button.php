<?php namespace System\Classes\UiManager;

use Html;
use Backend;
use System\Classes\UiElement;

/**
 * Button
 *
 * @method Button label(string $label) label for the button
 * @method Button linkUrl(string $linkUrl) linkUrl will use an anchor button
 * @method Button cssClass(string $cssClass) cssClass for the button
 * @method Button replaceCssClass(string $replaceCssClass) replaceCssClass defaults for the button
 * @method Button hotkey(...$hotkey) hotkey patterns
 * @method Button type(string $type) type of button
 * @method Button attributes(array $attributes) attributes in HTML
 * @method Button primary(bool $primary) primary button
 * @method Button outline(bool $outline) outline button
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Button extends UiElement
{
    /**
     * __construct
     */
    public function __construct($label = 'Button', $linkOrConfig = null, $config = [])
    {
        $this->label($label);

        if (is_array($linkOrConfig)) {
            $config = $linkOrConfig;
        }
        elseif ($linkOrConfig !== null) {
            $this->linkTo($linkOrConfig);
        }

        parent::__construct($config);
    }

    /**
     * initDefaultValues override method
     */
    protected function initDefaultValues()
    {
        $this->secondary();
    }

    /**
     * render the element
     */
    public function render(): callable
    {
        return function() { ?>

            <?php if ($this->linkUrl): ?>

                <a href="<?= $this->linkUrl ?>"
                    <?= Html::attributes($this->buildAttributes()) ?>
                >
                    <?= $this->label ?>
                </a>

            <?php else: ?>

                <button <?= Html::attributes($this->buildAttributes()) ?>>
                    <?= $this->label ?>
                </button>

            <?php endif ?>

        <?php };
    }

    /**
     * buildAttributes
     */
    protected function buildAttributes(array $attr = []): array
    {
        $attr['type'] = $this->type === 'primary' ? 'submit' : 'button';

        if ($this->hotkey) {
            $attr['data-hotkey'] = implode(',', $this->hotkey);
        }

        $attr['class'] = $this->buildCssClass();

        return $attr;
    }

    /**
     * buildCssClass
     */
    protected function buildCssClass(): string
    {
        if ($this->replaceCssClass !== null) {
            return $this->replaceCssClass;
        }

        $css = [];

        $css[] = 'btn';

        if ($this->outline) {
            $css[] = 'btn-outline-'.$this->type;
        }
        else {
            $css[] = 'btn-'.$this->type;
        }

        $css[] = $this->cssClass;

        return implode(' ', $css);
    }

    /**
     * linkTo
     */
    public function linkTo(string $linkUrl, bool $isRaw = false): static
    {
        $this->linkUrl = $isRaw ? $linkUrl : Backend::url($linkUrl);

        return $this;
    }

    /**
     * primary
     */
    public function primary(): static
    {
        $this->type('primary');
        return $this;
    }

    /**
     * secondary
     */
    public function secondary(): static
    {
        $this->type('secondary');
        return $this;
    }

    /**
     * success
     */
    public function success(): static
    {
        $this->type('success');
        return $this;
    }

    /**
     * danger
     */
    public function danger(): static
    {
        $this->type('danger');
        return $this;
    }

    /**
     * warning
     */
    public function warning(): static
    {
        $this->type('warning');
        return $this;
    }

    /**
     * info
     */
    public function info(): static
    {
        $this->type('info');
        return $this;
    }
}
