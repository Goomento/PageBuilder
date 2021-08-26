<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Schemes\Base;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\ColorPicker;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Core\Files\Css\GlobalCss;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticAuthorization;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Schemes
 * @package Goomento\PageBuilder\Builder\Managers
 */
class Schemes
{
    protected $_registered_schemes = [];
    private static $_enabled_schemes;

    private static $_schemes_types = [
        'color' => 'Scheme_Color',
        'typography' => 'Scheme_Typography',
        'color-picker' => 'Scheme_Color_Picker',
    ];

    /**
     * @param $id
     * @return bool
     */
    public function unregisterScheme($id)
    {
        if (! isset($this->_registered_schemes[ $id ])) {
            return false;
        }
        unset($this->_registered_schemes[ $id ]);
        return true;
    }

    /**
     * @return array
     */
    public function getRegisteredSchemes()
    {
        return $this->_registered_schemes;
    }

    /**
     * @return array
     */
    public function getRegisteredSchemesData()
    {
        $data = [];

        foreach ($this->getRegisteredSchemes() as $scheme) {
            $data[ $scheme::getType() ] = [
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
            $data[ $scheme::getType() ] = [
                'title' => $scheme->getTitle(),
                'items' => $scheme->getDefaultScheme(),
            ];
        }

        return $data;
    }
    public function getSystemSchemes()
    {
        $data = [];

        foreach ($this->getRegisteredSchemes() as $scheme) {
            $data[ $scheme::getType() ] = $scheme->getSystemSchemes();
        }

        return $data;
    }

    /**
     * @param $id
     * @return false|Base
     */
    public function getScheme($id)
    {
        $schemes = $this->getRegisteredSchemes();

        if (! isset($schemes[ $id ])) {
            return false;
        }

        return $schemes[ $id ];
    }

    /**
     * @param $scheme_type
     * @param $scheme_value
     * @return false
     */
    public function getSchemeValue($scheme_type, $scheme_value)
    {
        /** @var Base $scheme */
        $scheme = $this->getScheme($scheme_type);

        if (! $scheme) {
            return false;
        }

        return $scheme->getSchemeValue()[ $scheme_value ];
    }

    /**
     * @param $data
     * @return bool
     * @throws LocalizedException
     */
    public function ajaxApplyScheme($data)
    {
        if (!StaticAuthorization::isCurrentUserCan('manage_global_config')) {
            throw new LocalizedException(
                __('Sorry, you need permissions to view this content')
            );
        }

        if (! isset($data['scheme_name'])) {
            return false;
        }

        /** @var Base $scheme_obj */
        $scheme_obj = $this->getScheme($data['scheme_name']);

        if (! $scheme_obj) {
            return false;
        }

        $posted = json_decode($data['data'], true);

        $scheme_obj->saveScheme($posted);
        /** @var GlobalCss $globalCss */
        $globalCss = StaticObjectManager::create(GlobalCss::class);
        $globalCss->update();

        return true;
    }

    /**
     *
     */
    public function printSchemesTemplates()
    {
        foreach ($this->getRegisteredSchemes() as $scheme) {
            $scheme->printTemplate();
        }
    }

    /**
     * @param Ajax $ajax
     * @throws \Magento\Framework\Exception\LocalizedException
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
        if (null === self::$_enabled_schemes) {
            $enabled_schemes = [];

            foreach (self::$_schemes_types as $schemes_type => $scheme_class) {
                $enabled_schemes[] = $schemes_type;
            }
            $enabled_schemes = Hooks::applyFilters('pagebuilder/schemes/enabled_schemes', $enabled_schemes);

            self::$_enabled_schemes = $enabled_schemes;
        }
        return self::$_enabled_schemes;
    }

    /**
     * Schemes constructor.
     * @param Color $color
     * @param Typography $typography
     * @param ColorPicker $colorPicker
     */
    public function __construct(
        Color $color,
        Typography $typography,
        ColorPicker $colorPicker
    ) {
        $this->_registered_schemes[ $color::getType() ] = $color;
        $this->_registered_schemes[ $typography::getType() ] = $typography;
        $this->_registered_schemes[ $colorPicker::getType() ] = $colorPicker;

        Hooks::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
    }
}
