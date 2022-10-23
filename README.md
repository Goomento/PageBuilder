![Goomento - Free Magento Page Builder Extension](https://i.imgur.com/jVUNmot.gif)

# Goomento - The Free Magento Page Builder Extension, allows you to create unique Magento websites, landing pages using advanced animations, custom CSS, responsive designs, and more, without a line of code.

Goomento is a _Free Magento Page Builder Extension_ that allows you to efficiently set up your website by simply 
dragging and dropping manipulation. Notably, it can reuse your previous content from current or other websites to 
customize and redesign with your new creativity. All this process has an absence of coding involvement and configures instantly.

Goomento is built for **designers**, **developers** and **marketers**, who want to optimize the process of creating and managing the Magento store.

### Table of contents

[Installation](#install-goomento)

[Version Compatible](#version-compatible)

[Demo](https://goomento.com/goomento-the-free-magento-page-builder-extension)

[Setup](#setup)

[Why Us](https://github.com/Goomento/PageBuilder/wiki/Why-Goomento)

[Free Built-in Widgets](https://github.com/Goomento/PageBuilder/wiki/Free-Built-in-Widgets)

[How To Use](https://github.com/Goomento/DocBuilder/blob/master/Guide/HOW_TO_USE.md)

[Custom Templates](#custom-templates)

[Change Log](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md)

[Document And User Guide](https://github.com/Goomento/DocBuilder)

[Troubleshoot](https://github.com/Goomento/PageBuilder/wiki/Troubleshoot)

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

1. Create Landing Pages, Blocks, Templates in the admin area `Goomento > Pages & Landing Pages`.

2. Add Blocks, Pages to layout, using the same `identifier` as in the admin area to the layout `.xml` file.

```xml
<block class="PageBuilderRenderer" name="unique-block-name">
    <arguments>
        <argument name="identifier" xsi:type="string">home-page-identifier</argument>
    </arguments>
</block>
```

3. Add Blocks, Pages to template, use this snippet in the template `.phtml` file

```php
<?= $block->getLayout()
    ->getBlock('PageBuilderRenderer')
    ->setIdentifier('home-page-identifier')
    ->toHtml(); ?>
```

## Demo site

Storefront: [https://goomento.com](https://goomento.com/)

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

- For configurable of widget, check out this [Goomento/PageBuilder/Builder/Widgets](https://github.com/Goomento/PageBuilder/tree/master/Builder/Widgets)

## Version Compatible

Magento Community Edition (CE): 2.3.x, 2.4.0 - 2.4.5

Magento Enterprise Edition (EE): 2.3.x, 2.4.0 - 2.4.5

## Open An Issue And Contribution

Feel free to Open an Issue, Fork and Create Pull Request (PR) on GitHub

For opening an issue, click [here](https://github.com/Goomento/PageBuilder/issues/new).
