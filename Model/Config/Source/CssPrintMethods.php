<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CssPrintMethods
 * @package Goomento\PageBuilder\Model\Config\Source
 */
class CssPrintMethods implements OptionSourceInterface
{
    const INTERNAL = 'internal';
    const EXTERNAL = 'external';

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::INTERNAL,
                'label' => __('Internal')
            ],
            [
                'value' => self::EXTERNAL,
                'label' => __('External')
            ],
        ];
    }
}
