<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Css;

use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class GlobalCss extends AbstractCss
{
    /**
     * Meta key
     */
    const META_KEY = 'global_css';

    /**
     * Goomento global CSS file handler ID.
     */
    const FILE_HANDLER_ID = 'goomento-global';

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
     * @param string $fileName
     */
    public function __construct($fileName = 'pagebuilder-global.css')
    {
        parent::__construct($fileName);
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
        $widgetsManager = ObjectManagerHelper::getWidgetsManager();
        foreach ($widgetsManager->getWidgetTypes() as $widget) {
            $schemeControls = $widget->getSchemeControls();

            foreach ($schemeControls as $control) {
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

        $schemesManager = ObjectManagerHelper::getSchemasManager();
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
        $schemeManager = ObjectManagerHelper::getSchemasManager();
        /** @var Typography $typography */
        $typography = $schemeManager->getScheme(Typography::NAME);
        $schemeValues = $typography->getSchemeValue();

        if (!empty($schemeValues)) {
            $frontend = ObjectManagerHelper::getFrontend();
            foreach ($schemeValues as $schemeValue) {
                $frontend->enqueueFont($schemeValue['font_family']);
            }
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

        $customCss = (string) ConfigHelper::getValue('custom_css');

        if (trim($customCss) !== '') {
            $this->stylesheetObj->addRawCss($customCss);
        }

        /**
         * Parse CSS file.
         *
         * Fires when CSS file is parsed on Goomento.
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
