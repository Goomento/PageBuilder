<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\Exception\LocalizedException;

class Edit extends AbstractContent implements HttpGetActionInterface
{
    use TraitHttpPage;
    use TraitContent;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $contentId = (int) $this->getRequest()->getParam('content_id');
            $this->registry->register('pagebuilder_content', $this->getContent((bool) $contentId));
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

        return $this->resultRedirectFactory->create()->setPath('*/*/grid', [
            'type' => $this->getContentType()
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        try {
            return $this->_authorization->isAllowed(
                $this->getContentResourceName('save')
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
        $type = $this->getContentType();
        $type = (string) __(ucfirst($type));
        return [
            'active_menu' => 'Goomento_PageBuilder::' . $this->getContentType(),
            'editable_title' => "Edit {$type} `%1`",
            'title' => 'New ' . $type,
            'handler' => $this->getContentLayoutName('edit')
        ];
    }
}
