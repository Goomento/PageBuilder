
# Goomento **Page Builder**, a free drag and drop toolkit that supports to create custom pages, landing pages, blocks and store designs without writing a line of code

**Goomento Page Builder** is a *Free Magento 2 Page Builder Extension*, allows you to set up your own website in any industry easily by simple dragging and dropping manipulation. Notably, it can reuse your previous templates and sections to customize and redesign with your new creativity. All this process has an absence of coding involvement and configures instantly.

_This module is inspired by Elementor for WordPress but Goomento for Magento_

## 1. How to install Magento 2 Goomento Page Builder

### Install via composer (recommend)

Run the following command in Magento 2 root folder:

```
composer require goomento/module-page-builder
php bin/magento module:enable Goomento_PageBuilder
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
For more functionalities (Eg: Slider, Banner, Audio, Products ...), check this out 
**[Goomento_BuilderWidgets](https://github.com/Goomento/BuilderWidgets)** or run this command to install:

```
composer require goomento/module-builder-widgets
```

## 2. Configuration

By default,  Goomento Page Builder disabled on storefront, to enable the module go to

    Stores > Settings > Configuration > Goomento > Page Builder > General > Active

Change to **Yes** to enable module on storefront.

## 3. Features

Drag and Drop visual editor

Section / Pages / Templates control

Import / Export

Undo / Redo / Duplicate and more

History / Revision management

Reuse as page / template

Widgets hub / management

## 4. Design concept

Goomento Page Builder will overwrite the main content of these entities:

- Cms Page
- Cms Block
- Catalog Product (_-- Under develop --_)
- Catalog Category (_-- Under develop --_)

## 5. How to use

- Use default instruction on Cms Page/ Block Grid / Form to create a landing page, section or template

- Add Widget with name "Page Builder Content" and set "Page Builders" for it.

- Add snippet `{{block class="PageBuilderRenderer" content_id="your-content-id"}}` into any content

- On template **.phtml** add this code


    `<?= $block->getLayout()
            ->getBlock("pagebuilder.content.renderer")
            ->setContentId("your-content-id")
            ->toHtml(); ?>`

## 6. Create your own widgets / template

_-- Update soon --_

## 7. Version compatible

**Magento 2 Community**: 2.3.x, 2.4.x

**Magento 2 Enterprise**: -- Not tested yet --

## 8. Changelog

What's news? See here [CHANGELOG.md](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md).

## 9. Open an issue and Contribution

Feel free to open an Issue, Fork and Create Pull Request (PR) on GitHub

For opening an issue, click [here](https://github.com/Goomento/PageBuilder/issues).

## 10. Screen capture

![Configuration](https://i.imgur.com/2NStubU.png)

![Manage](https://i.imgur.com/RbXWlCx.png)

![Editor](https://i.imgur.com/oMzVXXf.png)

![Widget](https://i.imgur.com/RLSw6iV.png)

![Media](https://i.imgur.com/kZAMNEU.png)

![Color picker](https://i.imgur.com/Zs1MZwc.png)

![Responsive](https://i.imgur.com/sSBWWdv.png)

![CMS config](https://i.imgur.com/4GCLcVx.png)
