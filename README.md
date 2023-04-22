![Goomento - Free Magento Page Builder Extension](https://i.imgur.com/jVUNmot.gif)

# Goomento Magento page builder extension that allows you to create Magento content in just drag-and-drop to canvas and view your website as you build it.

You can build and customize every part of the Magento website visually at the builder editor by adding text, images, videos, animations, CSS and more, all with just a few clicks without writing a single line of code.

Magento store owners can view changes in Magento with 100% accuracy, drag and drop to configure, share content between websites and redesign with new creativity
### Table of contents

[Installation](#install-goomento)

[Version Compatible](#version-compatible)

[Demo](https://goomento.com/goomento-the-free-magento-page-builder-extension)

[Setup](#setup)

[Why Us](https://goomento.com/blog/post/goomento-page-builder-vs-magento-page-builder)

[Widgets](https://goomento.com/blog/post/goomento-widget-list)

[Custom Templates](#custom-templates)

[Change Log](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md)

[Document And User Guide](https://goomento.com/blog/category/user-guide)

[Troubleshoot](https://goomento.com/blog/post/troubleshooting)

[Open An Issue And Contribution](#open-an-issue-and-contribution)

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

## Custom Templates

Goomento also allows to make a taylor styling of widget, hence will be a good fit to your theme,
to do that, create directories inside your theme files that will contain the custom resources with the following structure.

```
app/design/frontend/<Vendor>/
├── <theme>/
│   ├── Goomento_PageBuilder/
│   │   ├── templates
│   │   │   ├── widgets
│   │   │   │   ├── <widget.phtml>
│   │   │── web
│   │   │   ├── css
│   │   │   │   ├── widgets
│   │   │   │   │   ├── <widget.less>
│   │   │   ├── js
│   │   │   │   ├── widgets
│   │   │   │   │   ├── <widget.js>
```

- `<widget.phtml>` is `.phtml` file - which copied from [templates directory](https://github.com/Goomento/PageBuilder/tree/master/view/frontend/templates/widgets).

- `<widget.less>` is `.less` file - which copied from [css directory](https://github.com/Goomento/PageBuilder/tree/master/view/frontend/web/css/widgets).

- `<widget.js>` is `.js` file - which copied from [js directory](https://github.com/Goomento/PageBuilder/tree/master/view/frontend/web/js/widgets).

- For configurable of widget, check out this [widget directory](https://github.com/Goomento/PageBuilder/tree/master/Builder/Widgets)

## Version Compatible

Magento Community Edition (CE): 2.3.x, 2.4.0 - 2.4.5*, 2.4.6

Magento Enterprise Edition (EE): 2.3.x, 2.4.0 - 2.4.5*, 2.4.6

## Open An Issue And Contribution

Feel free to Open an Issue, Fork and Create Pull Request (PR) on GitHub

For opening an issue, click [here](https://github.com/Goomento/PageBuilder/issues/new).
