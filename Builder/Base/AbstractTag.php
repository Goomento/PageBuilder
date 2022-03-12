<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

abstract class AbstractTag extends AbstractBaseTag
{
    const WRAPPED_TAG = false;

    /**
     *
     * @param array $options
     *
     * @return string
     */
    public function getContent(array $options = [])
    {
        ob_start();

        $return = $this->render();

        $value = ob_get_clean();

        return $value ?: $return;
    }


    final public function getContentType()
    {
        return 'ui';
    }


    public function getEditorConfig()
    {
        $config = parent::getEditorConfig();

        $config['wrapped_tag'] = $this::WRAPPED_TAG;

        return $config;
    }
}
