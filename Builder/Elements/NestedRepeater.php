<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Elements;

/**
 * Class NestedRepeater
 * @package Goomento\PageBuilder\Builder\Elements
 */
class NestedRepeater extends Repeater
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
        return 'nested_repeater-' . self::$counter;
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
        return 'nested_repeater';
    }
}
