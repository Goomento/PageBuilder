<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class BaseIconFont
 * @package Goomento\PageBuilder\Builder\Controls
 */
abstract class BaseIconFont
{

    /**
     * Get Icon type.
     *
     * Retrieve the icon type.
     *
     * @abstract
     */
    abstract public function getType();

    /**
     * Enqueue Icon scripts and styles.
     *
     * Used to register and enqueue custom scripts and styles used by the Icon.
     *
     */
    abstract public function enqueue();

    /**
     * get_css_prefix
     * @return string
     */
    abstract public function getCssPrefix();

    abstract public function getIcons();

    public function __construct()
    {
    }
}
