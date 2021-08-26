<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\DynamicTags;

/**
 * Class DataTag
 * @package Goomento\PageBuilder\Core\DynamicTags
 */
abstract class DataTag extends BaseTag
{

    /**
     * @abstract
     *
     * @param array $options
     */
    abstract protected function getValue(array $options = []);


    final public function getContentType()
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
