<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

/**
 * Class Boolean
 * @package Goomento\PageBuilder\Model\Config\Source
 */
class Boolean extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('No'), 'value' => self::VALUE_NO],
                ['label' => __('Yes'), 'value' => self::VALUE_YES],
            ];
        }
        return $this->_options;
    }
}
