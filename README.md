<img alt="Goomento - The Magento Page Builder Extension" src="https://i.imgur.com/zstn8jK.gif" width="800"/>

# Goomento - The Free Magento Page Builder Extension, allows you to create unique Magento websites, landing pages using advanced animations, custom CSS, responsive designs, and more, without a line of code.

Goomento is a _Free Magento Page Builder Extension_ that allows you to efficiently set up your website by simply 
dragging and dropping manipulation. Notably, it can reuse your previous content from current or other websites to 
customize and redesign with your new creativity. All this process has an absence of coding involvement and configures instantly.

Goomento is built for **designers**, **developers** and **marketers**, who want to optimize the process of creating and managing the Magento store.
### Table of contents

[Installation](#install-goomento)

[Version Compatible](#version-compatible)

[Demo](#demo-site)

[Why Us](#why-us)

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

## Why Us 

###### What You See Is What You Get

**What appears on the editor is what you get in the storefront**. Drag/drop it and see the changes in the editor right away before publishing it without any concern for output.

###### More Controls More Efficients

Every aspect of element is controllable by the editor panel, very readable, and efficient.
Therefore, **you don't need to know coding or experience to edit, just click and see** !!!

You can control: Layout such as width and height, Background, Color, Border, Typography, Shadow, CSS Id-classes, Padding, Margin, Z-Index
, Order, Animation such as hover or while loading, Responsive such as show-hide in tablet and mobile devices ....

###### Control Your Site Responsive

Enjoy the visually-appealing experience of creating, **checking responsiveness while editing of pages in one place**, what you see is what you get on storefront.

###### Revision And History Control

Goomento recorded your actions and versions of your page. Therefore, **can undo/redo or revert the page** to someday in the past.

###### Reduce Cost

With Goomento, you **spend less time on the frontend development** process and focus on creation of design, meaning you can save thousands of dollars for long developers hours to build your storefront.

###### Import, Export For Sharing Or Backup

Goomento gives you flexible access to **freely import, export the content to/from your website**. You can share these exported files,
or save it to backup, resources like images and files will be automatically downloaded while importing.

###### Fast And Compatible To Any Stores

Goomento uses its own CSS and JS, which are **optimized for use individually and impact to its self**, and other elements outside Goomento will keep the same.

###### +40 Free Built-in Widgets

Each website is built with a combination of widgets. You can **drag/drop, move, sharpen, overlap ... +40 free widgets** 
to create your unique landing pages or the whole website.

To create your own widget, check this out [Document And User Guide](https://github.com/Goomento/DocBuilder)

###### Add Custom Css In Page

Goomento allows you to get a more personalized look by **adding custom CSS to every element**, such as: widget, column, section, whole page, or all pages.

## Free Built-in Widgets

###### Basic pack:

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

###### General pack:

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
- Popup (Set section as a popup/modal)
- Facebook Like + Comment
- Facebook Pages + Post + Video
- Navigation (Menu)

###### Product pack:

- Add To Cart Button
- Product List
- Product Slider (Carousel)
- Pricing Table

###### Magento pack:
- Recently Viewed Products
- Recently Compared Products
- New Products
- Orders And Returns

## Troubleshoot

**The Page Builder did not display on storefront**

- Make sure that Goomento Page Builder module was enabled, in `Goomento > Configuration > General > Active`
- Make sure Page Builder was `Enabled` and `Store view` is matching with current storefront
- Enable the Debug Mode to see what happened, in `Goomento > Configuration > Editor > Debug Mode` choose `Yes`
- Flush/ Clean Magento Cache

**Visual editor did not load**

- Visual editor may crash for the first load, it's due to the timeout of loading resources from CDN,
try to reload your browser, It'll go away

**Missing style on storefront**

Goomento stored CSS files in `pub/media/goomento/css`, those files responsible for each content styling, so you can check 

- Folder `pub/media/goomento/css` is writable
- Try to generate the new style, go to `Goomento > Management > Global.Css > Save and Refesh` - Will refresh all global and contents styling
- Use different `CSS Print Method` in `Goomento > Configuration > Editor > Style > Use Inline Css` then
choose `Yes` - Will use inline CSS instead
- Flush/ Clean Magento Cache

Something else? [Open An Issue](https://github.com/Goomento/PageBuilder/issues/new) or [Contact Us](https://goomento.com/contact/)

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
