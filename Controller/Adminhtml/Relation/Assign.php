<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Relation;

use Exception;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Model\ContentRelation;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Assign extends AbstractAction
{
    /**
     * @var ContentManagementInterface
     */
    private $contentManagement;
    /**
     * @var ContentRelation
     */
    private $contentRelation;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * AssignContent constructor.
     * @param Context $context
     * @param ContentManagementInterface $contentManagement
     * @param ContentRelation $contentRelationMapping
     * @param ContentRegistryInterface $contentRegistry
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        ContentManagementInterface $contentManagement,
        ContentRelation $contentRelationMapping,
        ContentRegistryInterface $contentRegistry,
        Logger $logger
    )
    {
        $this->contentManagement = $contentManagement;
        $this->contentRelation = $contentRelationMapping;
        $this->contentRegistry = $contentRegistry;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        try {
            $entityType = (string) $this->getRequest()->getParam('type');
            $entityId = (int) $this->getRequest()->getParam('id');
            $storeId = (int) $this->getRequest()->getParam('store_id');
            if (empty($entityId) || empty($entityType)) {
                throw new LocalizedException(
                    __('The entity must be specify')
                );
            }
            $this->contentRelation->setStoreId($storeId);
            $relation = $this->contentRelation->getRelation($entityType, $entityId);
            if (
                isset($relation[ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID]) &&
                $relation[ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID]
            ) {
                $content = null;
                try {
                    $content = $this->contentRegistry->getById((int) $relation[ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID]);
                } catch (\Exception $e) {}
                if ($content instanceof ContentInterface && $content->getId()) {
                    $backUrl = $this->contentRelation->getEntityEditableUrl($entityType, $entityId);
                    return $redirect->setUrl($this->getUrl('pagebuilder/content/editor', [
                        'content_id' => $content->getId(),
                        'back_url' => EncryptorHelper::encrypt(
                            !empty($backUrl) ? $backUrl : $this->_redirect->getRefererUrl()
                        ),
                    ]));
                }
            }
            $relationObject = $this->contentRelation->getEntityObject($entityType, $entityId);
            $contentData = [];
            $contentData = $this->contentRelation->prepareContent($entityType, $relationObject, $contentData);
            $contentData['status'] = ContentInterface::STATUS_PUBLISHED;

            $content = $this->contentManagement->createContent($contentData);
            $this->contentRelation->setRelation(
                (int) $content->getId(),
                $entityType,
                $entityId,
                0,
                ['store_id' => $storeId]
            );
            $backUrl = $this->contentRelation->getEntityEditableUrl($entityType, $entityId);
            return $redirect->setUrl($this->getUrl('pagebuilder/content/editor', [
                'content_id' => $content->getId(),
                'back_url' => EncryptorHelper::encrypt(!empty($backUrl) ? $backUrl : $this->_redirect->getRefererUrl()),
            ]));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when create the Page Builder. Please try again.')
            );
        }

        return $redirect->setRefererUrl();
    }
}
