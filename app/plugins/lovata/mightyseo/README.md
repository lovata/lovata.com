# Usage

You can fill Seo fields for **CMS pages** or **[Static pages](http://octobercms.com/plugin/rainlab-pages)** in backend.
Available SEO fields:
  * Seo title, description, keywords
  * Content for H1 title of the page
  * Page description
  * Canonical URL
  * Content for "robots" meta tag

If you have pages with **:slug** parameter in the project, then you can set common values of SEO fields.
You can add **integration with your model** and override common values of SEO fields for specific elements of model.
For example:
  * Common SEO title for product page is "Best flower shop"
  * SEO title for product "Roses" is "Best flower shop: roses"

## Integration with models

Plugin makes it easy to add integration of SEO fields for your models.
You need to add list of model classes in **app.php** config file.

```
    'seo_models' => [
        \Lovata\Shopaholic\Models\Product::class,
        \Lovata\Shopaholic\Models\Category::class,
    ],
 ```
If you use [Shopaholic](http://octobercms.com/plugin/lovata-shopaholic) plugins, or you have your own classes for caching with extend from [ElementItem](https://github.com/lovata/oc-toolbox-plugin/wiki/ElementItem) class, then you need to add list of *Item classes to **app.php** config file.
```
    'seo_items' => [
        \Lovata\Shopaholic\Classes\Item\ProductItem::class,
        \Lovata\Shopaholic\Classes\Item\CategoryItem::class,
    ],
```

After you add model classes in app.php config file, all SEO fields will be available for filling in backend.

## Usage Twig templates in SEO fields

You can transfer data to the SEO template and use Twig syntax for data rendering.

**For example**: you using [Shopaholic](http://octobercms.com/plugin/lovata-shopaholic) plugin. You need render SEO meta tags for product page.
You need set default SEO title in format: **"Category name - Product name"**.
But manager must have the ability for individual products to set their own SEO title.

To solve this problem, you need to do:
  1. Create [placeholder](http://octobercms.com/docs/markup/tag-placeholder) in your layout.
  ```twig
  {% placeholder seo_tags default %}
      {% component 'SeoToolbox' %}
  {% endplaceholder %}
  ```
  2. Overwrite placeholder for product page
  ```twig
  {% set obProduct = ProductPage.get() %}
  {% set arSEOParams = {'product': obProduct} %}
  
  {% put seo_tags %}
      {% component 'SeoToolbox' model=obProduct params=arSEOParams %}
  {% endput %}
  ```
  3. Create SEO title for product page
  ```twig
  {{ product.category.name }} - {{ product.name }}
  ```
In this example, we get the product object and transfer it to the SEO template.

## SeoToolbox component (Basic usage)

layout code
```twig
<html>
    <head>
        {# Render metric code after head tag #}
        {{ SeoToolbox.getHeadBegin() }}
        ...
        {# Render SEO meta tags (title, description, keywords, canonical URL, robots meta tag) #}
        {% component 'SeoToolbox' %}
        ...
        {# Render metric code before /head tag #}
        {{ SeoToolbox.getHeadEnd() }}
    </head>
    <body>
        {# Render metric code after body tag #}
        {{ SeoToolbox.getBodyBegin() }}
        ...
        {# Render page title #}
        <h1>{{ SeoToolbox.getPageTitle() }}</h1>
        ...
        {# Render SEO text block #}
        <div>
            {{ SeoToolbox.getPageDescription()|raw }} 
        <div>
        ...
        {# Render metric code before /body tag #}
        {{ SeoToolbox.getBodyEnd() }}
    </body>
</html
```

## SeoToolbox component (Advanced usage)

layout code
```twig
<html>
    <head>
        {# Render metric code after head tag #}
        {{ SeoToolbox.getHeadBegin() }}
        ...
        {% placeholder seo_tags default %}
            {# Render SEO meta tags (title, description, keywords, canonical URL, robots meta tag) #}
            {% component 'SeoToolbox' %}
        {% endplaceholder %}
        ...
        {# Render metric code before /head tag #}
        {{ SeoToolbox.getHeadEnd() }}
    </head>
    <body>
        {# Render metric code after body tag #}
        {{ SeoToolbox.getBodyBegin() }}
        ...
        {# Render page title #}
        <h1>{{ SeoToolbox.getPageTitle() }}</h1>
        ...
        {# Render SEO text block #}
        <div>
            {{ SeoToolbox.getPageDescription()|raw }} 
        <div>
        ...
        {# Render metric code before /body tag #}
        {{ SeoToolbox.getBodyEnd() }}
    </body>
</html
```

Product page code
```twig
{# Get product object #}
{% set obProduct = ProductPage.get() %}

{# Prepare array with data for trasfering to SEO template #}
{% set arSEOParams = {'product': obProduct} %}

{# Overwrite seo_tags placeholder #}
{% put seo_tags %}
    {% component 'SeoToolbox' model=obProduct params=arSEOParams %}
{% endput %}
...

{# Render page title #}
<h1>{{ SeoToolbox.getPageTitle(obProduct, arSEOParams) }}</h1>
...
{# Render SEO text block #}
<div>
    {{ SeoToolbox.getPageDescription(obProduct, arSEOParams)|raw }} 
<div>
```

## Seo template

You can set small text values and re-use them in different templates.
For example: You have "Best flower shop" text that will be repeated in each SEO description.
You can create SEO template with key "shop_name" and value "Best flower shop".
You can put this SEO template with using seo_template("key") twig function.
```twig
{{ product.category.name }} - {{ product.name }} | {{ seo_template('shop_name') }}
```

## License

© 2018, [LOVATA Group, LLC](https://github.com/lovata) under Commercial License.

Developed by [Andrey Kharanenka](https://github.com/kharanenka).
