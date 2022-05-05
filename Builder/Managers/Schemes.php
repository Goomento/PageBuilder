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
    protected $_registered_schemes = [];

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

    protected $components = [
        Color::NAME => Color::class,
        Typography::NAME => Typography::class,
        ColorPicker::NAME => ColorPicker::class,
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
     * @param $scheme_type
     * @param $scheme_value
     * @return false
     */
    public function getSchemeValue($scheme_type, $scheme_value)
    {
        /** @var \Goomento\PageBuilder\Builder\Base\AbstractSchema $scheme */
        $scheme = $this->getScheme($scheme_type);

        if (!$scheme) {
            return false;
        }

        return $scheme->getSchemeValue()[ $scheme_value ];
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function ajaxApplyScheme($data)
    {
        if (!AuthorizationHelper::isCurrentUserCan('manage_global_config')) {
            throw new \Exception(
                'Sorry, you need permissions to view this content'
            );
        }

        if (!isset($data['scheme_name'])) {
            return false;
        }

        /** @var AbstractSchema $scheme_obj */
        $scheme_obj = $this->getScheme($data['scheme_name']);

        if (!$scheme_obj) {
            return false;
        }

        $posted = json_decode($data['data'], true);

        $scheme_obj->saveScheme($posted);
        $globalCss = new GlobalCss;
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
            $enabled_schemes = [];

            foreach (self::$schemesTypes as $schemes_type) {
                $enabled_schemes[] = $schemes_type;
            }
            $enabled_schemes = HooksHelper::applyFilters('pagebuilder/schemes/enabled_schemes', $enabled_schemes);

            self::$enabledSchemes = $enabled_schemes;
        }
        return self::$enabledSchemes;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
    }
}
