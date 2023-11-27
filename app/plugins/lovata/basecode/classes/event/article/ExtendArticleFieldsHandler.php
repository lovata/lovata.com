<?php namespace Lovata\BaseCode\Classes\Event\Article;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;
use Lovata\GoodNews\Controllers\Articles;
use Lovata\GoodNews\Models\Article;

/**
 * Class ExtendArticleFieldsHandler
 * @package Lovata\BaseCode\Classes\Event\Article
 * @author Anton Kuleshov, a.kuleshov@lovata.com, LOVATA Group
 */
class ExtendArticleFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $obWidget->addTabFields([
            'post_class' => [
                'label' => 'lovata.basecode::lang.field.post_class',
                'type'  => 'dropdown',
                'tab'   => 'lovata.toolbox::lang.tab.settings',
                'span'  => 'left',
                'options' => [
                    '' => '-',
                    '_blog' => 'Blog',
                    '_in' => 'Linkindin',
                    '_ins' => 'Instagram',
                    '_fb' => 'Facebook',
                    '_yt' => 'YuoTube',
                    '_clutch' => 'Clutch'
                ]
            ]
        ]);

        $obWidget->addTabFields([
            'link' => [
                'label' => 'lovata.basecode::lang.field.link',
                'type'  => 'text',
                'tab'   => 'lovata.toolbox::lang.tab.settings',
                'span'  => 'left'
            ]
        ]);
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Article::class;
    }

    /**
     * @return string
     */
    protected function getControllerClass(): string
    {
        return Articles::class;
    }
}