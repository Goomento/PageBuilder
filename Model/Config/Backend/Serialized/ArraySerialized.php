<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Backend\Serialized;

class ArraySerialized extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * Fix the issue for old version of Magento
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value)) {
            if (!array_key_exists('__empty', $value)) {
                $result = [];
                foreach ($value as $index => $row) {
                    foreach ($row as $key => $item) {
                        $result[$key] = $item;
                    }
                }
                $this->setValue($result);
            }
        }
        return parent::beforeSave();
    }
}
