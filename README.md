![Goomento - Free Magento Page Builder Extension](https://i.imgur.com/jVUNmot.gif)

# 🚀 Goomento PageBuilder for Magento 2

> A lightweight, blazing-fast Magento 2 Page Builder extension that lets you build fully responsive, professional storefronts with an intuitive drag-and-drop interface.
Whether you’re a Magento developer, agency, or merchant — Goomento helps you create beautiful pages without touching a line of code.


* ✅ **100% visual editor match** — what you see is exactly what shows on the storefront
* ⚡ Super lightweight & blazing-fast Magento 2 Page Builder
* 🧱 Drag-and-drop builder for landing pages, banners, and custom blocks
* 🎨 Full control: custom HTML, CSS, animations & responsive design
* 🔍 SEO-friendly, mobile-first, and optimized for performance
* 🧩 Works out of the box with Luma, Porto, Fastest & more Magento 2 themes
* 💯 Free, open-source, and developer-friendly — no locked features


### Table of contents

[Installation](#install-goomento)

[Demo](https://goomento.com/goomento-the-free-magento-page-builder-extension)

[Troubleshooting](https://github.com/Goomento/PageBuilder/wiki/Troubleshooting)

[Goomento vs Magento Page Builder](https://goomento.com/blog/post/goomento-page-builder-vs-magento-page-builder)

[Goomento - Magento Form Builder](https://goomento.com/magento-form-builder)

[Wiki](https://github.com/Goomento/PageBuilder/wiki/)

[REST API and GraphQL](https://github.com/Goomento/PageBuilderApi)

[Open An Issue](https://github.com/Goomento/PageBuilder/issues/new)

## Install Goomento

Run the following command in Magento 2 root folder:

```bash
composer require goomento/module-page-builder
php bin/magento module:enable Goomento_Core Goomento_PageBuilder
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

Enable these caches in `Production mode` for best performance at `System > Tools > Cache Management`

**Pagebuilder Frontend**: Uses for frontend display, preview HTML ...

**Pagebuilder Backend**: Uses for registered controls data of widgets, system variables ...

## Setup

### Create Landing Page (no coding)

To create Magento Landing Pages go to `Magento Backend > Goomento > Pages & Landing Pages`

> This will create your landing page, which will be accessible via the storefront. No further action required.

### Embed Landing Page, Blocks, Pages to layout (requires coding)

To add Blocks, Pages to layout, using the `your-content-identifier` 
identifier at `Magento Backend > Goomento > Sections` to the layout `.xml` file.

```xml
<block class="PageBuilderRenderer" name="unique-block-name">
    <arguments>
        <argument name="identifier" xsi:type="string">your-content-identifier</argument>
    </arguments>
</block>
```

or template `.phtml` file

```php
<?= $block->getLayout()
    ->getBlock('PageBuilderRenderer')
    ->setIdentifier('your-content-identifier')
    ->toHtml(); ?>
```
- To export the template, click to `Export` button at each page builder content
- To import the template, go to `Magento Backend > Goomento > Importer`

See more at [wiki](https://github.com/Goomento/PageBuilder/wiki/) page

## Version Compatible

| Magento Version         | 2.3.x | 2.4.0 - 2.4.5-p3 | 2.4.6-x | 2.4.7-x | 2.4.8-x |
|:------------------------|:------|:-----------------|:--------|---------|---------|
| Community Edition (CE)  | ✅     | ✅                | ✅       | ✅       | ☑️      |
| Enterprise Edition (EE) | ✅     | ✅                | ✅       | ✅       | ☑️      |

## Themes Compatible

> Currently, Goomento doesn't fully support headless/PWA solutions such as Venia and Hyvä. 
Other themes such as Luma, Porto, Fastest ... are the best fit. We will soon adapt to all kind of themes.

| Theme Name   | Compatible |
|:-------------|:-----------|
| Blank + Luma | ✅          |
| Hyvä         | ❌          |
| PWA Themes   | ❌          |
| Porto        | ✅          |
| Fastest      | ✅          |
| Market       | ✅          |
| Other Themes | ✅          |