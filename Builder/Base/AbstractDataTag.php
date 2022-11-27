<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

abstract class AbstractDataTag extends AbstractBaseTag
{
    /**
     *
     * @param array $options
     */
    abstract protected function getValue(array $options = []);

    /**
     * @return string
     */
    public function getContentType()
    {
        return 'plain';
    }

    /**
     *
     * @param array $options
     *
     * @return mixed
     */
    public function getContent(array $options = [])
    {
        return $this->getValue($options);
    }
}
