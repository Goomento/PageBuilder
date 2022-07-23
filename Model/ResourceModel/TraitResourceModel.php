<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Magento\Framework\DataObject;

trait TraitResourceModel
{
    /**
     * @param DataObject $object
     * @return DataObject
     */
    public function unserializeData(DataObject $object)
    {
        foreach ($this->_serializableFields as $field => $parameters) {
            list($serializeDefault, $unserializeDefault) = $parameters;
            $this->_unserializeField($object, $field, $unserializeDefault);
        }
        return $object;
    }

    /**
     * @param DataObject $object
     * @return DataObject
     */
    public function serializeData(DataObject $object)
    {
        foreach ($this->_serializableFields as $field => $parameters) {
            list($serializeDefault, $unserializeDefault) = $parameters;
            $this->_serializeField($object, $field, $serializeDefault, isset($parameters[2]));
        }
        return $object;
    }
}
