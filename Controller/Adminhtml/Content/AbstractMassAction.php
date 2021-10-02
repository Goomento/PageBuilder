<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Exception;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractMassAction
 * @package Goomento\PageBuilder\Controller\Adminhtml\Content
 */
abstract class AbstractMassAction extends Action implements HttpPostActionInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ContentRepositoryInterface
     */
    protected $contentRepository;

    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ContentManagementInterface
     */
    protected $contentManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ContentRepositoryInterface $contentRepository
     * @param ContentManagementInterface $contentManagement
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ContentRepositoryInterface $contentRepository,
        ContentManagementInterface $contentManagement,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->contentRepository = $contentRepository;
        $this->contentManagement = $contentManagement;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            /** @var AbstractCollection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->massAction($collection);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when processing mass action.')
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererUrl();
    }

    /**
     * @param AbstractCollection $collection
     * @return void
     * @throws LocalizedException
     */
    abstract protected function massAction(AbstractCollection $collection) : void;
}
