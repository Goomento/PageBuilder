<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

use Goomento\Core\Traits\TraitHttpExecutable;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class AbstractManage
 * @package Goomento\PageBuilder\Controller\Adminhtml\Manage
 */
abstract class AbstractManage extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    use TraitHttpExecutable;

    /**
     * @inheritdoc
     */
    public const ADMIN_RESOURCE = 'Goomento_PageBuilder::manage';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Import constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function executeGet()
    {
        $resultPage = $this->resultPageFactory->create();
        $pageConfig = static::getPageConfig();
        $resultPage->setActiveMenu($pageConfig['active_menu'] ?? static::ADMIN_RESOURCE);
        if (isset($pageConfig['breadcrumb']) && $pageConfig['breadcrumb']) {
            foreach ($pageConfig['breadcrumb'] as $args) {
                $resultPage->addBreadcrumb(...$args);
            }
        }

        $resultPage->getConfig()->getTitle()
            ->prepend($pageConfig['title']);

        return $resultPage;
    }

    /**
     * @return array
     */
    abstract protected static function getPageConfig();
}
