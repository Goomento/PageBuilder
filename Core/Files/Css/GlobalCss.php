<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Files\Css;

use Goomento\PageBuilder\Builder\Frontend;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticConfig;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use tubalmartin\CssMin\Minifier as CSSmin;

/**
 * Class GlobalCss
 * @package Goomento\PageBuilder\Core\Files\Css
 */
class GlobalCss extends Base
{

    /**
     * SagoTheme global CSS file handler ID.
     */
    const FILE_HANDLER_ID = 'goomento-global';

    const META_KEY = 'global_css';

    /**
     * Get CSS file name.
     *
     * Retrieve the CSS file name.
     *
     *
     * @return string CSS file name.
     */
    public function getName()
    {
        return 'global';
    }

    /**
     * Get file handle ID.
     *
     * Retrieve the handle ID for the global post CSS file.
     *
     *
     * @return string CSS file handle ID.
     */
    protected function getFileHandleId()
    {
        return self::FILE_HANDLER_ID;
    }

    /**
     * GlobalCss constructor.
     * @param string $file_name
     */
    public function __construct($file_name = 'global.css')
    {
        parent::__construct($file_name);
    }

    /**
     * Render CSS.
     *
     * Parse the CSS for all the widgets and all the scheme controls.
     *
     */
    protected function renderCss()
    {
        $this->renderSchemesCss();
    }

    /**
     * @return false
     */
    protected function useExternalFile()
    {
        return true;
    }

    /**
     * Get inline dependency.
     *
     *
     *
     * @return string Name of the stylesheet.
     */
    protected function getInlineDependency()
    {
        return 'goomento-frontend';
    }

    /**
     * Render schemes CSS.
     *
     * Parse the CSS for all the widgets and all the scheme controls.
     *
     */
    private function renderSchemesCss()
    {
        /** @var Widgets $widgetsManager */
        $widgetsManager = StaticObjectManager::get(Widgets::class);
        foreach ($widgetsManager->getWidgetTypes() as $widget) {
            $scheme_controls = $widget->getSchemeControls();

            foreach ($scheme_controls as $control) {
                $this->addControlRules(
                    $control,
                    $widget->getControls(),
                    function ($control) {
                        /** @var Schemes $schemeManager */
                        $schemeManager = StaticObjectManager::get(Schemes::class);
                        $scheme_value = $schemeManager->getSchemeValue($control['scheme']['type'], $control['scheme']['value']);

                        if (empty($scheme_value)) {
                            return null;
                        }

                        if (! empty($control['scheme']['key'])) {
                            $scheme_value = $scheme_value[ $control['scheme']['key'] ];
                        }

                        if (empty($scheme_value)) {
                            return null;
                        }

                        return $scheme_value;
                    },
                    [ '{{WRAPPER}}' ],
                    [ '.gmt-widget-' . $widget->getName() ]
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function enqueue()
    {
        parent::enqueue();
        /** @var Frontend $frontend */
        $frontend = StaticObjectManager::get(Frontend::class);
        /** @var Schemes $schemeManager */
        $schemeManager = StaticObjectManager::get(Schemes::class);
        $schemeValues = $schemeManager->getScheme(Typography::getType())->getSchemeValue();
        foreach ($schemeValues as $schemeValue) {
            $frontend->enqueueFont($schemeValue['font_family']);
        }
    }

    /**
     * Parse CSS.
     *
     * Parsing the CSS file.
     *
     */
    protected function parseContent()
    {
        $this->renderCss();

        $name = $this->getName();

        $customCss = (string) StaticConfig::getOption('custom_css');

        if (trim( $customCss)) {
            $this->stylesheet_obj->addRawCss(
                $customCss
            );
        }

        /**
         * Parse CSS file.
         *
         * Fires when CSS file is parsed on SagoTheme.
         *
         * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
         *
         *
         * @param Base $this The current CSS file.
         */
        Hooks::doAction("pagebuilder/css-file/{$name}/parse", $this);

        return $this->stylesheet_obj->__toString();
    }
}
