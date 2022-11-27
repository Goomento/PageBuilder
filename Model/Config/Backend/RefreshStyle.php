<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Backend;

use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class RefreshStyle extends Value
{
    /**
     * @var BuildableContentManagementInterface
     */
    private $contentManagement;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param BuildableContentManagementInterface $contentManagement
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                             $context,
        Registry                            $registry,
        ScopeConfigInterface                $config,
        TypeListInterface                   $cacheTypeList,
        BuildableContentManagementInterface $contentManagement,
        AbstractResource                    $resource = null,
        AbstractDb                          $resourceCollection = null,
        array                               $data = []
    ) {
        $this->contentManagement = $contentManagement;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        $this->contentManagement->refreshGlobalAssets();
        return parent::afterSave();
    }
}
