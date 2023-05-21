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

[Custom Templates](#custom-templates)

[Change Log](https://github.com/Goomento/PageBuilder/blob/master/CHANGELOG.md)

[List Of Widgets](#list-of-widgets)

[User Guide](https://github.com/Goomento/PageBuilder/wiki/User-Guide:-Add-Landing-Page)

[Developer Guide](https://github.com/Goomento/PageBuilder/wiki/Developer:-Create-Widget)

[Troubleshooting](https://github.com/Goomento/PageBuilder/wiki/Troubleshooting)

[Open An Issue And Contribution](https://github.com/Goomento/PageBuilder/issues/new)

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

## List Of Widgets

### Basic pack:
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

### General pack:

- Accordion
- Tabs
- Toggles
- Alert
- Audio
- Countdown
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

### Product pack:

- Add To Cart Button
- Product List
- Product Slider (Carousel)
- Pricing Table

### Magento pack:
- Recently Viewed Products
- Recently Compared Products
- New Products
- Orders And Returns

### Form builder pack:
- [Form Builder](https://goomento.com/magento-form-builder)
- [Multistep Form Builder](https://goomento.com/magento-form-builder)
