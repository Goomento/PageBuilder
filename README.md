![Goomento — Free Magento 2 Page Builder Extension with drag-and-drop editor](https://i.imgur.com/jVUNmot.gif)

<div align="center">

# Goomento — Free Drag & Drop Page Builder for Magento 2

**The fastest way to build landing pages, banners, and custom blocks in Magento 2 — no coding required.**

[![Latest Version](https://img.shields.io/packagist/v/goomento/module-page-builder?label=version&color=blue)](https://packagist.org/packages/goomento/module-page-builder)
[![License: OSL-3.0](https://img.shields.io/badge/license-OSL--3.0-brightgreen)](LICENSE.txt)
[![Magento 2](https://img.shields.io/badge/Magento-2.3%20--%202.4.8-orange)](#-version-compatibility)
[![PHP](https://img.shields.io/badge/PHP-7.4%20--%208.4-777bb4)](#-version-compatibility)
[![GitHub Stars](https://img.shields.io/github/stars/Goomento/PageBuilder?style=social)](https://github.com/Goomento/PageBuilder)

[Live Demo](https://goomento.com/goomento-the-free-magento-page-builder-extension) · [Installation](#-installation) · [Documentation](https://github.com/Goomento/PageBuilder/wiki/) · [Report a Bug](https://github.com/Goomento/PageBuilder/issues/new)

</div>

---

Goomento is a lightweight, blazing-fast **Magento 2 page builder extension** that gives merchants, agencies, and developers a true drag-and-drop visual editor for building fully responsive storefront pages — landing pages, CMS pages, banners, and reusable blocks — without touching code.

## ✨ Why Goomento

* ✅ **WYSIWYG editing** — the editor is a 1:1 match with what renders on the live storefront
* ⚡ **Lightweight & fast** — minimal footprint, no bloated dependencies, optimized asset output
* 🧱 **True drag-and-drop builder** — compose landing pages, banners, and custom blocks visually
* 🎨 **Full design control** — custom HTML/CSS, animations, and responsive breakpoints per device
* 🔍 **SEO-friendly by default** — clean semantic markup, mobile-first, and performance-optimized output
* 🧩 **Broad theme compatibility** — works out of the box with Luma, Porto, Fastest, and more
* 🆓 **100% free & open-source** — no premium tiers, no locked-away features, no upsells
* 🛠️ **Developer-friendly** — layout XML integration, REST/GraphQL API, and an extensible builder architecture

## 📚 Table of Contents

1. [Why Goomento](#-why-goomento)
2. [Installation](#-installation)
3. [Getting Started](#-getting-started)
4. [Version Compatibility](#-version-compatibility)
5. [Theme Compatibility](#-theme-compatibility)
6. [Frequently Asked Questions](#-frequently-asked-questions)
7. [Resources & Links](#-resources--links)
8. [Contributing](#-contributing)
9. [License](#-license)

## 🚀 Installation

Install the **Magento 2 page builder module** via Composer from the project root:

```bash
composer require goomento/module-page-builder
php bin/magento module:enable Goomento_Core Goomento_PageBuilder
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

For best performance, enable the following caches in **Production mode** at `System > Tools > Cache Management`:

| Cache Type | Purpose |
|:-----------|:--------|
| **Pagebuilder Frontend** | Frontend rendering and HTML preview output |
| **Pagebuilder Backend** | Registered widget controls, options, and system variables |

## 🎯 Getting Started

### Create a landing page (no coding required)

Go to `Magento Backend > Goomento > Pages & Landing Pages` to create a new page.

> The page is automatically accessible on the storefront once published — no further setup needed.

### Embed a block, section, or page into a layout (for developers)

Grab the `your-content-identifier` value from `Magento Backend > Goomento > Sections`, then reference it from a layout XML file:

```xml
<block class="PageBuilderRenderer" name="unique-block-name">
    <arguments>
        <argument name="identifier" xsi:type="string">your-content-identifier</argument>
    </arguments>
</block>
```

...or directly from a `.phtml` template:

```php
<?= $block->getLayout()
    ->getBlock('PageBuilderRenderer')
    ->setIdentifier('your-content-identifier')
    ->toHtml(); ?>
```

* To export a template, click **Export** on any page builder content item.
* To import a template, go to `Magento Backend > Goomento > Importer`.

See the [wiki](https://github.com/Goomento/PageBuilder/wiki/) for the full guide on widgets, controls, and custom development.

## ✅ Version Compatibility

### Magento

| Magento Version         | 2.3.x | 2.4.0 – 2.4.5-p3 | 2.4.6-x | 2.4.7-x | 2.4.8-x |
|:-------------------------|:-----:|:-----------------:|:-------:|:-------:|:-------:|
| Community Edition (CE)  |  ✅   |         ✅         |   ✅    |   ✅    |   ✅    |
| Enterprise Edition (EE) |  ✅   |         ✅         |   ✅    |   ✅    |   ✅    |

### PHP

| PHP Version | 7.4 | 8.0 | 8.1 | 8.2 | 8.3 | 8.4 |
|:------------|:---:|:---:|:---:|:---:|:---:|:---:|
| Supported   | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  |

## 🎨 Theme Compatibility

> Goomento does not yet fully support headless/PWA storefronts such as Venia or Hyvä. Traditional Luma-based themes are the best fit today, with broader theme support on the roadmap.

| Theme Name   | Compatible |
|:-------------|:----------:|
| Blank + Luma |     ✅     |
| Porto        |     ✅     |
| Fastest      |     ✅     |
| Market       |     ✅     |
| Other themes |     ✅     |
| Hyvä         |     ❌     |
| PWA themes   |     ❌     |

## ❓ Frequently Asked Questions

**Is Goomento really free to use?**
Yes. Goomento is 100% free and open-source under the [OSL-3.0 license](LICENSE.txt) — there are no paid tiers or feature paywalls.

**How is Goomento different from Magento's native Page Builder?**
Goomento is lighter, faster, and offers a broader set of widgets and design controls out of the box. See the full breakdown in [Goomento vs. Magento Page Builder](https://goomento.com/blog/post/goomento-page-builder-vs-magento-page-builder).

**Can I build forms with Goomento?**
Yes — see [Goomento Magento Form Builder](https://goomento.com/magento-form-builder) for drag-and-drop form building.

**Does Goomento expose an API?**
Yes, a REST and GraphQL API is available via the companion [PageBuilderApi module](https://github.com/Goomento/PageBuilderApi).

**Where can I get help if something breaks?**
Check the [Troubleshooting guide](https://github.com/Goomento/PageBuilder/wiki/Troubleshooting) first, then [open an issue](https://github.com/Goomento/PageBuilder/issues/new) if you're still stuck.

## 🔗 Resources & Links

* 🌐 [Live Demo](https://goomento.com/goomento-the-free-magento-page-builder-extension)
* 📖 [Wiki & Documentation](https://github.com/Goomento/PageBuilder/wiki/)
* 🆚 [Goomento vs. Magento Page Builder](https://goomento.com/blog/post/goomento-page-builder-vs-magento-page-builder)
* 📝 [Goomento Form Builder](https://goomento.com/magento-form-builder)
* 🔌 [REST API & GraphQL](https://github.com/Goomento/PageBuilderApi)
* 🛠️ [Troubleshooting Guide](https://github.com/Goomento/PageBuilder/wiki/Troubleshooting)
* 🐛 [Report an Issue](https://github.com/Goomento/PageBuilder/issues/new)

## 🤝 Contributing

Issues and pull requests are welcome. If you're proposing a larger change, please open an issue first to discuss the approach.

## 📄 License

Goomento PageBuilder is released under the [Open Software License 3.0 (OSL-3.0)](LICENSE.txt).
