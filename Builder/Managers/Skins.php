<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Base\Skin;
use Goomento\PageBuilder\Builder\Base\Widget;

/**
 * Class Skins
 * @package Goomento\PageBuilder\Builder\Managers
 */
class Skins
{

    /**
     * Registered Skins.
     *
     * Holds the list of all the registered skins for all the widgets.
     *
     *
     * @var array Registered skins.
     */
    private $_skins = [];

    /**
     * Add new skin.
     *
     * Register a single new skin for a widget.
     *
     *
     * @param Widget $widget SagoTheme widget.
     * @param Skin $skin SagoTheme skin.
     *
     * @return true True if skin added.
     */
    public function addSkin(Widget $widget, Skin $skin)
    {
        $widget_name = $widget->getName();

        if (! isset($this->_skins[ $widget_name ])) {
            $this->_skins[ $widget_name ] = [];
        }

        $this->_skins[ $widget_name ][ $skin->getId() ] = $skin;

        return true;
    }

    /**
     * Remove a skin.
     *
     * Unregister an existing skin from a widget.
     *
     *
     * @param Widget $widget SagoTheme widget.
     * @param string $skin_id SagoTheme skin ID.
     * @throws \Exception
     */
    public function removeSkin(Widget $widget, $skin_id)
    {
        $widget_name = $widget->getName();

        if (! isset($this->_skins[ $widget_name ][ $skin_id ])) {
            throw new \Exception('Cannot remove not-exists skin.');
        }

        unset($this->_skins[ $widget_name ][ $skin_id ]);

        return true;
    }

    /**
     * Get skins.
     *
     * Retrieve all the skins assigned for a specific widget.
     *
     *
     * @param Widget $widget SagoTheme widget.
     *
     * @return false|array Skins if the widget has skins, False otherwise.
     */
    public function getSkins(Widget $widget)
    {
        $widget_name = $widget->getName();

        if (! isset($this->_skins[ $widget_name ])) {
            return false;
        }

        return $this->_skins[ $widget_name ];
    }

    /**
     * Skins manager constructor.
     *
     * Initializing SagoTheme skins manager by requiring the skin base class.
     *
     */
    public function __construct()
    {
    }
}
