<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Css;

use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Modules\Frontend;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class GlobalCss extends AbstractCss
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
    const NAME = 'global';

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
        $widgetsManager = ObjectManagerHelper::get(Widgets::class);
        foreach ($widgetsManager->getWidgetTypes() as $widget) {
            $scheme_controls = $widget->getSchemeControls();

            foreach ($scheme_controls as $control) {
                $this->addControlRules(
                    $control,
                    $widget->getControls(),
                    function ($control) {
                        if (!empty($control['scheme'])) {
                            $schema = $control['scheme'];
                            if (!empty($schema['key'])) {
                                return sprintf('var(--gmt-%s-%s-%s)', $schema['type'], $schema['value'], $schema['key']);
                            } else {
                                return sprintf('var(--gmt-%s-%s)', $schema['type'], $schema['value']);
                            }
                        }

                        return null;
                    },
                    [ '{{WRAPPER}}' ],
                    [ '.gmt-widget-' . $widget->getName() ]
                );
            }
        }

        /** @var Schemes $schemesManager */
        $schemesManager = ObjectManagerHelper::get(Schemes::class);
        foreach ($schemesManager->getRegisteredSchemes() as $schemaName => $schemaObject) {
            foreach ($schemaObject->getScheme() as $key => $value) {
                if (is_array($value['value'])) {
                    foreach ($value['value'] as $valueK => $valueV) {
                        $this->stylesheetObj->addVariable('--gmt-' . $schemaName . '-' . $key . '-' . $valueK, $valueV);
                    }
                } else {
                    $this->stylesheetObj->addVariable('--gmt-' . $schemaName . '-' . $key, $value['value']);
                }
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
        $frontend = ObjectManagerHelper::get(Frontend::class);
        /** @var Schemes $schemeManager */
        $schemeManager = ObjectManagerHelper::get(Schemes::class);
        /** @var Typography $typography */
        $typography = $schemeManager->getScheme(Typography::NAME);
        $schemeValues = $typography->getSchemeValue();
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

        if (trim($customCss = (string) ConfigHelper::getOption('custom_css'))) {
            $this->stylesheetObj->addRawCss($customCss);
        }

        /**
         * Parse CSS file.
         *
         * Fires when CSS file is parsed on SagoTheme.
         *
         * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
         *
         *
         * @param AbstractCss $this The current CSS file.
         */
        HooksHelper::doAction("pagebuilder/css-file/{$name}/parse", $this);

        return $this->stylesheetObj->__toString();
    }
}
