<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Goomento\PageBuilder\Traits\TraitHttpPage;

class Grid extends Action implements HttpGetActionInterface
{
    use TraitHttpPage;
    use TraitContent;

    const DATA_KEY = 'pagebuilder_content';

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $dataPersistor = $this->_objectManager->get(DataPersistorInterface::class);
            $dataPersistor->clear(static::DATA_KEY);
            return $this->renderPage();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when display contents.')
            );
        }

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        try {
            return $this->_authorization->isAllowed(
                $this->getContentResourceName('view')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when display content(s)')
            );
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getPageConfig()
    {
        $title = $this->getContentType();
        if ($title === ContentInterface::TYPE_PAGE) {
            $title = __('Pages & Landing Pages');
        } else {
            $title = (string) __(ucfirst($title) . 's');
        }
        return [
            'active_menu' => 'Goomento_PageBuilder::' . $this->getContentType(),
            'title' => $title,
            'handler' => $this->getContentLayoutName('grid')
        ];
    }
}
