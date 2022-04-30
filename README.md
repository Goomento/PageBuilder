![Goomento - The Magento Page Builder Extension](https://i.imgur.com/zstn8jK.gif)

# Goomento - **The Free Magento Page Builder Extension**, a free drag and drop WYSIWYG editor that supports to create content in your stores.

Goomento is a _Free Magento Page Builder Extension_ that allows you to efficiently set up your website by simply 
dragging and dropping manipulation. Notably, it can reuse your previous content from current or other websites to 
customize and redesign with your new creativity. All this process has an absence of coding involvement and configures instantly.

### Table of contents

[Installation](#install-goomento)

[Version Compatible](#version-compatible)

[Demo](#demo-site)

[Why Goomento](#why-goomento)

[Free Built-in Widgets](#free-built-in-widgets)

[Change Log](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md)

[Document And User Guide](https://github.com/Goomento/DocBuilder)

[Troubleshoot](#troubleshoot)

[Open An Issue And Contribution](#open-an-issue-and-contribution)

## Install Goomento

Run the following command in Magento 2 root folder:

```bash
composer require goomento/module-page-builder
php bin/magento module:enable Goomento_PageBuilder
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Demo site

Storefront: [https://goomento.com](https://goomento.com/)

## Why Goomento 

### What You See Is What You Get

![What You See Is What You Get (WYSIWYG) in the new level](https://i.imgur.com/OTO4Xgb.png)

What appears on the editor is what you get in the storefront, 100% matched. Drag/drop it and see the changes in the editor right away before publishing it without any concern for output.

### More Controls More Efficients

![More Controls More Efficients](https://i.imgur.com/liYvkgH.png)

Every aspect of element is controllable by the editor panel, very readable, and efficient. 
Therefore, you don't need to know coding or experience to update or maintain your website. Instead, let's do it by performing simple actions such as drag and drop.

### Control Your Site Responsive

![Control Your Site Responsive](https://i.imgur.com/xPAFIPP.png)

Enjoy the visually-appealing experience of creating, checking responsiveness while editing of pages in one place, what you see is what you get on storefront.

### Revision And History Control

![Revision And History Control](https://i.imgur.com/3mcgoOO.png)

You made a typo, let undo, redo it or revert the previous version of your page. Don't worry about the consequence. Every action within Goomento has been recorded.

### Reduce Cost

With Goomento, you spend less time on the frontend development process and focus on more important things, meaning you can save thousands of dollars for long developers hours to build your storefront.

### Import, Export For Sharing Or Backup

Goomento gives you flexible access to freely import and export the content to/from your website. You can share these exported files,
or save it to backup, resources like images and files will be automatically downloaded while importing.

### Fast And Compatible To Any Stores

All resources within Goomento have been wrapped and cached. Therefore, Goomento will not mess it up with your current website theme. 
CSS/JS only printed out if there was a content of Goomento on-page. Otherwise, resources will hide all, for no-redundant.

### Free Built-in Widgets And Will Be More On The Way

Widget is the brick of your website. By talking the brick, you can combine, move, sharpen, overlap ... +32 free widgets (and more) to create the desired website.

### Add Custom Css In Page

Goomento allows you to get a more personalized look by adding custom CSS to every element. 
By meaning every, you can add it to widget, column, section, whole page, or all pages.

## Free Built-in Widgets

Basic pack:

- Text
- HTML
- Magento Block
- Section/ Column
- Image
- Icon
- Banner
- Spacer
- Video
- Text Editor
- Google Maps

General pack:

- Accordion
- Tabs
- Toggles
- Alert
- Audio
- Counter
- Divider
- Icon Box
- Icon List
- Image Box
- Progress Bar
- Social Icons
- Star Rating
- Banner Slider (Carousel)
- Image Slider (Carousel)
- Testimonial
- Call To Action (CTA)
- Popup

Product pack:

- Add To Cart Button
- Product List 
- Product Slider (Carousel)

## Troubleshoot

**The Page Builder did not display on storefront**

- Make sure that Goomento Page Builder module was enabled, in `Stores > Settings > Configuration > Goomento > Page Builder > General > Active`
- Make sure Page Builder was `Enabled` and `Store view` is matching with current storefront
- Flush/ Clean Magento Cache

**Visual editor did not load**

- Visual editor may crash for the first load, it's due to the timeout of loading resources from CDN,
try to reload your browser, It'll go away

**Missing style on storefront**

Goomento stored CSS files in `pub/media/goomento/css`, those files responsible for each content styling, so you can check 

- Folder `pub/media/goomento/css` is writable
- Try to generate the new style, go to `Goomento > Management > Global.Css > Save and Refesh` - Will refresh all global and contents styling
- Use different `CSS Print Method` in `Stores > Settings > Configuration > Goomento > Page Builder > Editor > Style > CSS Print Method` then
choose `Internal` - Will use inline CSS instead
- Flush/ Clean Magento Cache

Something else? [Open An Issue](#open-an-issue-and-contribution) or [Contact Us](https://goomento.com/contact/)

## Version Compatible

Magento Community Edition (CE): 2.3.x, 2.4.0 - 2.4.4

Magento Enterprise Edition (EE): 2.3.x, 2.4.0 - 2.4.4

## Open An Issue And Contribution

Feel free to Open an Issue, Fork and Create Pull Request (PR) on GitHub

For opening an issue, click [here](https://github.com/Goomento/PageBuilder/issues/new).

Each issue needs more detail for easily to produce, then please provide

- Goomento version, which is installed on your website. 
- Your Magento Version, can be revealed by running this command `bin/magento --version`.
- Description of issue in detail.
- Browser console log (if capable).
