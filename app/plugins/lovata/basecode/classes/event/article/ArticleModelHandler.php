<?php namespace Lovata\BaseCode\Classes\Event\Article;

use Lovata\GoodNews\Models\Article;

class ArticleModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Article::extend(function ($obArticle) {
            /** @var Article $obArticle*/
            $obArticle->fillable[] = 'link';
            $obArticle->fillable[] = 'post_class';
            $obArticle->addCachedField(['link', 'post_class']);
        });
    }
}
