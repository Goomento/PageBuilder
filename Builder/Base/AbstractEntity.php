<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

/**
 * The very first object of Page Builder
 */
abstract class AbstractEntity
{
    /**
     * Shared type
     */
    const TYPE = 'base';

    /**
     * Shared name
     */
    const NAME = 'entity';

    /**
     * Shared enabled/disabled value, able to on/off entity
     */
    const ENABLED = true;

    /**
     * Get object name
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Get object type
     */
    public function getType()
    {
        return static::TYPE;
    }
}
