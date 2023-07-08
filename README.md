![Goomento - Free Magento Page Builder Extension](https://i.imgur.com/jVUNmot.gif)

# Goomento Magento page builder extension that allows you to create Magento content in just drag-and-drop to canvas and view your website as you build it.

You can build and customize every part of the Magento website visually at the builder editor by adding text, images, videos, animations, CSS and more, all with just a few clicks without writing a single line of code.

Magento store owners can view changes in Magento with 100% accuracy, drag and drop to configure, share content between websites and redesign with new creativity

### Table of contents

[Installation](#install-goomento)

[Why Goomento - Magento Page Builder](https://goomento.com/blog/post/goomento-page-builder-vs-magento-page-builder)

[Version Compatible](#version-compatible)

[Themes Compatible](#themes-compatible)

[Demo](https://goomento.com/goomento-the-free-magento-page-builder-extension)

[Setup](#setup)

[Custom Templates - Frontend Tasks](https://github.com/Goomento/PageBuilder/wiki/Custom-Theme-Frontend-Tasks)

[Change Log](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md)

[List Of Widgets](https://github.com/Goomento/PageBuilder/wiki/Magento-Page-Builder-Widgets)

[User Guide & DevDoc](https://github.com/Goomento/PageBuilder/wiki/)

[Troubleshooting](https://github.com/Goomento/PageBuilder/wiki/Troubleshooting)

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

- To create Magento Landing Pages at `Magento Backend > Goomento > Pages & Landing Pages`

- To add Blocks, Pages to layout, using the `your-content-identifier` 
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
- To import the template, go to `Magento Backend > Goomento > Importor`

## Demo site

Editor: [https://goomento.com](https://goomento.com/goomento-the-free-magento-page-builder-extension)

## Version Compatible

| Magento Version         | 2.3.x | 2.4.0 - 2.4.5-p3 | 2.4.6, 2.4.6-p1 |
|:------------------------|:------|:-----------------|:----------------|
| Community Edition (CE)  | ✅     | ✅                | ✅               |
| Enterprise Edition (EE) | ✅     | ✅                | ✅               |

## Themes Compatible

> Currently, Goomento doesn't fully cooperate with headless/ PWA solutions such as Venia and Hyvä. 
Other themes such as Luma, Porto, Fastest ... are the best fit. We will soon adapt to all kind of themes.

| Theme Name   | Compatible |
|:-------------|:-----------|
| Blank + Luma | ✅          |
| Hyvä         | ❌          |
| PWA Themes   | ❌          |
| Porto        | ✅          |
| Fastest      | ✅          |
| Market       | ✅          |
| Other Themes | ☑️         |