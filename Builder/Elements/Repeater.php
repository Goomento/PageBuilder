<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Elements;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Repeater extends AbstractElement
{
    const NAME = 'repeater';

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
     * Initializing Goomento repeater element.
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
        return self::NAME . '-' . self::$counter;
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
     * @param array $options Optional. Repeater control options. Default is an
     *                        empty array.
     *
     * @return bool True if repeater control added, False otherwise.
     */
    public function addControl(string $id, array $args, array $options = [])
    {
        $currentTab = $this->getCurrentTab();

        if (null !== $currentTab) {
            $args = array_merge($args, $currentTab);
        }

        return ObjectManagerHelper::getControlsManager()->addControlToStack($this, $id, $args, $options);
    }

    /**
     * Get default child type.
     *
     * Retrieve the repeater child type based on element data.
     *
     * Note that repeater does not support children, therefore it returns false.
     *
     *
     * @param array $elementData Element ID.
     *
     * @return false Repeater default child type or False if type not found.
     */
    protected function _getDefaultChildType(array $elementData)
    {
        return false;
    }
}
