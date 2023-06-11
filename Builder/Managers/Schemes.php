<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Base\AbstractSchema;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\ColorPicker;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Builder\Modules\Ajax;
use Goomento\PageBuilder\Builder\Css\GlobalCss;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;

class Schemes
{

    use TraitComponentsLoader;

    /**
     * @var \Goomento\PageBuilder\Builder\Base\AbstractSchema[]
     */
    protected $registeredSchemes = [];

    /**
     * @var AbstractSchema[]|null
     */
    private static $enabledSchemes;

    /**
     *
     * @var string[]
     */
    private static $schemesTypes = [
        Color::NAME,
        Typography::NAME,
        ColorPicker::NAME
    ];

    /**
     * @param $id
     * @return bool
     */
    public function unregisterScheme($id)
    {
        return $this->removeComponent($id);
    }

    /**
     *
     * @return AbstractSchema[]
     */
    public function getRegisteredSchemes()
    {
        return $this->getComponents();
    }

    /**
     * @return array
     */
    public function getRegisteredSchemesData()
    {
        $data = [];

        foreach ($this->getRegisteredSchemes() as $scheme) {
            $data[ $scheme::NAME ] = [
                'title' => $scheme->getTitle(),
                'disabled_title' => $scheme->getDisabledTitle(),
                'items' => $scheme->getScheme(),
            ];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getSchemesDefaults()
    {
        $data = [];

        foreach ($this->getRegisteredSchemes() as $scheme) {
            $data[ $scheme::NAME ] = [
                'title' => $scheme->getTitle(),
                'items' => $scheme->getDefaultScheme(),
            ];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getSystemSchemes()
    {
        $data = [];

        foreach ($this->getRegisteredSchemes() as $scheme) {
            $data[ $scheme::NAME ] = $scheme->getSystemSchemes();
        }

        return $data;
    }

    /**
     * @param $id
     * @return false|\Goomento\PageBuilder\Builder\Base\AbstractSchema
     */
    public function getScheme($id)
    {
        return $this->getComponent($id);
    }

    /**
     * @param $schemeType
     * @param $schemeValue
     * @return false
     */
    public function getSchemeValue($schemeType, $schemeValue)
    {
        /** @var \Goomento\PageBuilder\Builder\Base\AbstractSchema $scheme */
        $scheme = $this->getScheme($schemeType);

        if (!$scheme) {
            return false;
        }

        return $scheme->getSchemeValue()[ $schemeValue ];
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function ajaxApplyScheme($data)
    {
        if (!AuthorizationHelper::isCurrentUserCan('manage_global_config')) {
            throw new \Goomento\PageBuilder\Exception\BuilderException(
                'Sorry, you need permissions to view this content'
            );
        }

        if (!isset($data['scheme_name'])) {
            return false;
        }

        /** @var AbstractSchema $schemeObj */
        $schemeObj = $this->getScheme($data['scheme_name']);

        if (!$schemeObj) {
            return false;
        }

        $posted = json_decode($data['data'], true);

        $schemeObj->saveScheme($posted);
        $globalCss = new GlobalCss;
        $globalCss->update();

        return true;
    }

    /**
     * Print template
     * @return void
     */
    public function printSchemesTemplates()
    {
        foreach ($this->getRegisteredSchemes() as $scheme) {
            $scheme->printTemplate();
        }
    }

    /**
     * @param Ajax $ajax
     * @throws \Exception
     */
    public function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('apply_scheme', [ $this, 'ajaxApplyScheme' ]);
    }

    /**
     * @return mixed
     */
    public static function getEnabledSchemes()
    {
        if (null === self::$enabledSchemes) {
            $enabledSchemes = [];

            foreach (self::$schemesTypes as $schemesType) {
                $enabledSchemes[] = $schemesType;
            }
            $enabledSchemes = HooksHelper::applyFilters('pagebuilder/schemes/enabled_schemes', $enabledSchemes)->getResult();

            self::$enabledSchemes = $enabledSchemes;
        }
        return self::$enabledSchemes;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);

        $this->setComponent([
            Color::NAME => Color::class,
            Typography::NAME => Typography::class,
            ColorPicker::NAME => ColorPicker::class,
        ]);
    }
}
