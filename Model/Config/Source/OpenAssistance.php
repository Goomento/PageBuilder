<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class OpenAssistance implements OptionSourceInterface
{
    const SAME_TAB = 'same_tab';

    const NEW_TAB = 'new_tab';

    const IFRAME = 'iframe';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SAME_TAB,
                'label' => __('Same window tab'),
            ],
            [
                'value' => self::NEW_TAB,
                'label' => __('New window tab'),
            ],
            [
                'value' => self::IFRAME,
                'label' => __('Iframe'),
            ],
        ];
    }
}
