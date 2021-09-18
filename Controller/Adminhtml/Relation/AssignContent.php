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
use Goomento\PageBuilder\Helper\StaticEncryptor;
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
    private $contentRelation;

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
        $this->contentRelation = $contentRelationMapping;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        try {
            $type = (string) $this->getRequest()->getParam('type');
            $relationId = (int) $this->getRequest()->getParam('id');
            if (empty($relationId)) {
                throw new LocalizedException(
                    __('The entity must be specify')
                );
            }
            $repository = $this->contentRelation->getRepositoryByType($type);
            /** @var \Magento\Cms\Model\Page|\Magento\Cms\Model\Block $relationObject */
            $relationObject = $repository->getById($relationId);
            $contentData = [];
            $contentData = $this->contentRelation->prepareContent($type, $relationObject, $contentData);
            $contentData['status'] = ContentInterface::STATUS_PUBLISHED;

            $content = $this->contentManagement->createContent($contentData);
            $this->contentRelation->setRelation(
                $content->getId(),
                $type,
                $relationId
            );
            $backUrl = $this->contentRelation->getRelationEditableUrl($type, $relationId);
            return $redirect->setUrl($this->getUrl('pagebuilder/content/editor', [
                'content_id' => $content->getId(),
                'back_url' => StaticEncryptor::encrypt($backUrl),
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
