<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RenderFallback implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'use_origin',
                'label' => __('Use Origin Content'),
            ],
            [
                'value' => 'empty',
                'label' => __('Show Nothing'),
            ],
            [
                'value' => 'nothing',
                'label' => __('Do Nothing'),
            ],
        ];
    }
}
