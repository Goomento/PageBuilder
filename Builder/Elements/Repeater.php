<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Elements;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Repeater
 * @package Goomento\PageBuilder\Builder\Elements
 */
class Repeater extends \Goomento\PageBuilder\Builder\Base\Element
{

    /**
     * Repeater counter.
     *
     * Holds the Repeater counter data. Default is `0`.
     *
     *
     * @var int Repeater counter.
     */
    private static $counter = 0;

    /**
     * Repeater constructor.
     *
     * Initializing SagoTheme repeater element.
     *
     *
     * @param array      $data Optional. Element data. Default is an empty array.
     * @param array|null $args Optional. Element default arguments. Default is null.
     *
     */
    public function __construct(array $data = [], array $args = null)
    {
        self::$counter++;

        parent::__construct($data, $args);
    }

    /**
     * Get repeater name.
     *
     * Retrieve the repeater name.
     *
     *
     * @return string Repeater name.
     */
    public function getName()
    {
        return 'repeater-' . self::$counter;
    }

    /**
     * Get repeater type.
     *
     * Retrieve the repeater type.
     *
     *
     * @return string Repeater type.
     */
    public static function getType()
    {
        return 'repeater';
    }

    /**
     * Add new repeater control to stack.
     *
     * Register a repeater control to allow the user to set/update data.
     *
     * This method should be used inside `_register_controls()`.
     *
     *
     * @param string $id      Repeater control ID.
     * @param array  $args    Repeater control arguments.
     * @param array  $options Optional. Repeater control options. Default is an
     *                        empty array.
     *
     * @return bool True if repeater control added, False otherwise.
     */
    public function addControl($id, array $args, $options = [])
    {
        $current_tab = $this->getCurrentTab();

        if (null !== $current_tab) {
            $args = array_merge($args, $current_tab);
        }
        /** @var Controls $controlsManager */
        $controlsManager = StaticObjectManager::get(Controls::class);
        return $controlsManager->addControlToStack($this, $id, $args, $options);
    }

    /**
     * Get default child type.
     *
     * Retrieve the repeater child type based on element data.
     *
     * Note that repeater does not support children, therefore it returns false.
     *
     *
     * @param array $element_data Element ID.
     *
     * @return false Repeater default child type or False if type not found.
     */
    protected function _getDefaultChildType(array $element_data)
    {
        return false;
    }
}
