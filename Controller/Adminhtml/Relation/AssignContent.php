<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Relation;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Helper\StaticAccessToken;
use Goomento\PageBuilder\Model\ContentRelation;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AssignContent
 * @package Goomento\PageBuilder\Controller\Adminhtml\Relation
 */
class AssignContent extends AbstractAction
{
    /**
     * @var ContentManagementInterface
     */
    private $contentManagement;
    /**
     * @var ContentRelation
     */
    private $contentRelationMapping;

    /**
     * AssignContent constructor.
     * @param Context $context
     * @param ContentManagementInterface $contentManagement
     * @param ContentRelation $contentRelationMapping
     */
    public function __construct(
        Context $context,
        ContentManagementInterface $contentManagement,
        ContentRelation $contentRelationMapping
    )
    {
        $this->contentManagement = $contentManagement;
        $this->contentRelationMapping = $contentRelationMapping;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        try {
            $type = $this->getRequest()->getParam('type');
            $relationId = $this->getRequest()->getParam('id');
            $relationData = $this->contentRelationMapping->getRelationData($type);
            if (empty($relationId)) {
                throw new LocalizedException(
                    __('The entity must be specify')
                );
            }
            $repository = $this->contentRelationMapping->getRepositoryByType($type);
            /** @var \Magento\Cms\Model\Page|\Magento\Cms\Model\Block $relationObject */
            $relationObject = $repository->getById($relationId);
            $contentData = [
                'title' => $relationData['label'] . ': #' . $relationObject->getId() . ' ' . $relationObject->getTitle(),
                'type' => $relationData['pagebuilder_type'],
                'status' => ContentInterface::STATUS_PUBLISHED,
            ];

            $contentData['store_id'] = $relationObject->getStores();
            $content = $this->contentManagement->createContent($contentData);
            $this->contentRelationMapping->setRelation(
                $content->getId(),
                $type,
                $relationId
            );
            $backUrl = $this->contentRelationMapping->getRelationEditableUrl($type, $relationId);
            return $redirect->setUrl($this->getUrl('pagebuilder/content/editor', [
                'content_id' => $content->getId(),
                'back_url' => StaticAccessToken::encrypt($backUrl),
            ]));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when create the Page Builder. Please try again.')
            );
        }

        return $redirect->setRefererUrl();
    }
}
