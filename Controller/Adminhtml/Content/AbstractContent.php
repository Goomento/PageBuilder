<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\Core\Model\Registry;
use Goomento\PageBuilder\Helper\AdminUser;
use Goomento\PageBuilder\Model\ContentFactory;
use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Goomento\PageBuilder\Traits\TraitHttpContentAction;
use Goomento\PageBuilder\Logger\Logger;

abstract class AbstractContent extends AbstractAction
{
    use TraitHttpContentAction;

    /**
     * @var ContentDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var ContentFactory
     */
    protected $contentFactory;

    /**
     * @var AdminUser
     */
    protected $userHelper;

    /**
     * @var ContentInterface|null
     */
    protected $content = null;

    /**
     * @var Logger|mixed
     */
    protected $logger;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var BuildableContentManagementInterface
     */
    protected $buildableContentManagement;

    /**
     * @var ContentRegistryInterface|mixed
     */
    protected $contentRegistry;

    /**
     * AbstractContent constructor.
     * @param Context $context
     * @param ContentDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param AdminUser $userHelper
     * @param PageFactory $pageFactory
     * @param Registry $registry
     * @param ContentFactory|null $contentFactory
     * @param ContentRegistryInterface|null $contentRegistry
     * @param BuildableContentManagementInterface|null $contentManagement
     * @param Logger|null $logger
     */
    public function __construct(
        Context                             $context,
        ContentDataProcessor                $dataProcessor,
        DataPersistorInterface              $dataPersistor,
        AdminUser                           $userHelper,
        PageFactory                         $pageFactory,
        Registry                            $registry,
        ContentRegistryInterface            $contentRegistry = null,
        BuildableContentManagementInterface $contentManagement = null,
        Logger                              $logger = null
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->userHelper = $userHelper;
        $this->pageFactory = $pageFactory;
        $this->registry = $registry;
        $objectManager = ObjectManager::getInstance();
        $this->buildableContentManagement = $contentManagement ?: $objectManager->get(BuildableContentManagementInterface::class);
        $this->contentRegistry = $contentRegistry ?: $objectManager->get(ContentRegistryInterface::class);
        $this->logger = $logger ?: $objectManager->get(Logger::class);

        parent::__construct($context);
    }
}
