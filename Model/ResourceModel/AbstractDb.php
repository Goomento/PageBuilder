<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class AbstractDb
 * @package Goomento\PageBuilder\Model\ResourceModel
 */
abstract class AbstractDb extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $relation;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param null $connectionName
     * @param array $relation
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        $connectionName = null,
        array $relation = []
    ) {
        parent::__construct($context, $connectionName);
        $this->objectManager = $objectManager;
        $this->relation = $relation;
    }


    /**
     * @param $operation
     * @param $object
     * @param array $argments
     * @throws LocalizedException
     */
    private function processRelation($operation, $object, array $argments = [])
    {
        $operations = $this->relation[$operation] ?? [];
        foreach ($operations as $operationClass) {
            if (is_string($operationClass) && class_exists($operationClass)) {
                $operationClass = $this->objectManager->get($operationClass);
            }
            if (!($operationClass instanceof ExtensionInterface)) {
                throw new LocalizedException(
                    __('Class %1 is not an operation.', $operationClass)
                );
            }
            $operationClass->execute($object, $argments);
        }
    }


    /**
     * @inheritDoc
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->processRelation('read', $object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->processRelation('update', $object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _afterDelete(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->processRelation('delete', $object);
        return $this;
    }
}
