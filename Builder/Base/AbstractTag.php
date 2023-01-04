<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Helper\StateHelper;

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
        // Magento2.Functions.DiscouragedFunction.Discouraged
        ob_start();

        $return = StateHelper::emulateFrontend(function () {
            return $this->render();
        });

        $value = ob_get_clean();

        return $value ?: $return;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return 'ui';
    }

    /**
     * @return array
     */
    public function getEditorConfig()
    {
        $config = parent::getEditorConfig();

        $config['wrapped_tag'] = $this::WRAPPED_TAG;

        return $config;
    }
}
