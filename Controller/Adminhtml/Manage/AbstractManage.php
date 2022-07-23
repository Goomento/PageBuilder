<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Model\Config;
use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Traits\TraitHttpPost;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\PageFactory;

abstract class AbstractManage extends AbstractAction implements HttpPostActionInterface
{
    use TraitHttpPost;

    /**
     * @inheritdoc
     */
    public const ADMIN_RESOURCE = 'Goomento_PageBuilder::manage';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var BuildableContentManagementInterface
     */
    protected $contentManagement;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Import constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $config
     * @param BuildableContentManagementInterface $contentManagement
     * @param DataPersistorInterface $dataPersistor
     * @param Logger $logger
     */
    public function __construct(
        Action\Context                      $context,
        PageFactory                         $resultPageFactory,
        Config                              $config,
        BuildableContentManagementInterface $contentManagement,
        DataPersistorInterface              $dataPersistor,
        Logger                              $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
        $this->contentManagement = $contentManagement;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        parent::__construct($context);
    }
}
