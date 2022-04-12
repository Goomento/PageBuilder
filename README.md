![Goomento Page Builder - Drag and Drop](https://i.imgur.com/spx4d9u.png)

# Goomento **Page Builder**, a free drag and drop toolkit that supports to create custom pages, landing pages, blocks and store designs without writing a line of code

**Goomento Page Builder** is a *Free Magento 2 Page Builder Extension*, allows you to set up your own website in any industry easily by simple dragging and dropping manipulation. Notably, it can reuse your previous templates and sections to customize and redesign with your new creativity. All this process has an absence of coding involvement and configures instantly.

_This module is inspired by Elementor for WordPress let say Elementor for Magento, precisely, It's Goomento for Magento_

## 1. How to install Magento 2 Goomento Page Builder

### Install via composer (recommend)

Run the following command in Magento 2 root folder:

```bash
composer require goomento/module-page-builder
php bin/magento module:enable Goomento_PageBuilder
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
## 2. Configuration

2.1 To display Goomento Page Builder content on storefront, enable module in

    Stores > Settings > Configuration > Goomento > Page Builder > General > Active

Change to **Yes** to enable module on storefront.

2.2 Import Sample templates

From Magento Admin menu, open

    Goomento > Builder Actions > Importer > Import Sample templates

Choose template, then Import it.

## 3. Features

- Easy to install, setup and use by Non-developer and Developer
- Drag and Drop visual editor
- Section, Pages, Templates control
- Import, Export content
- Undo, Redo, Duplicate and more ...
- History, Revision management
- Reuse as page, template
- Widgets hub management
- Adaptability in any store
- High speed optimize
- Responsive layout
- Add custom CSS

## 4. Design concept

4.1 Goomento Page Builder will overwrite the main content of these Magento entities:

- Cms Page
- Cms Block
- Default text Editor by Magento, Eg: Wysiwyg, Magento Page Builder (In develop)

4.2 Goomento Page Builder is using [Font Awesome Free 5.9.0](https://fontawesome.com/) for Icon displaying.

## 5. Document and User Guide

See [Repository for Document and Module Sample](https://github.com/Goomento/DocBuilder).

## 6. Troubleshoot

**The Page Builder did not display on storefront**

- Make sure that Goomento Page Builder module was enabled, click [here](https://github.com/Goomento/PageBuilder#2-configuration)
- Make sure Page Builder status is `Published` and `Store view` is matching with current storefront
- For Magento entity, (Eg: Product, Category ...) make sure `Page Builder Content` was selected and `Active` turned to `Yes`
- Flush/ Clean Magento Cache

**Visual editor did not load**

- Visual editor may crash for the first load, it's due to the timeout of loading resources from CDN,
try to reload your browser, It'll go away

**Missing style on storefront**

Goomento stored CSS files in `pub/media/goomento/css`, those files responsible for each content styling, so you can check 

- Folder `pub/media/goomento/css` is writable
- Try to generate the new style, go to `Goomento > Global.Css > Save and Refesh` - Will refresh all global and contents styling
- Use different `CSS Print Method` in `Stores > Settings > Configuration > Goomento > Page Builder > Editor > Style > CSS Print Method` then
choose `Internal` - Will use inline CSS instead
- Flush/ Clean Magento Cache

Something else? [Open an issue](#9-open-an-issue-and-contribution)

## 7. Version compatible

magento Community Edition (CE): 2.3.x, 2.4.0 - 2.4.3

Magento Enterprise Edition (EE): 2.3.x, 2.4.0 - 2.4.3

## 8. Changelog

What's news? See here [CHANGELOG.md](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md).

## 9. Open an issue and Contribution

Feel free to Open an Issue, Fork and Create Pull Request (PR) on GitHub

For opening an issue, click [here](https://github.com/Goomento/PageBuilder/issues/new).

## 10. Screenshots

![Goomento Page Builder Interface](https://i.imgur.com/hiRyX5Y.gif)
One click to use

![Goomento Page Builder History Management](https://i.imgur.com/cpxv7Kn.gif)
History/ Revision Management

![Goomento Page Builder Editing](https://i.imgur.com/rj10Ncs.gif)
Easy to use

![Goomento Page Builder Responsive](https://i.imgur.com/abT8OtO.gif)
Responsive control
