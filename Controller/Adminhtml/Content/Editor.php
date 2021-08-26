<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Helper\Hooks;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Editor
 * @package Goomento\PageBuilder\Controller\Adminhtml\Content
 */
class Editor extends AbstractContent implements HttpGetActionInterface
{
    use TraitContent;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $content = $this->getContent(true);
            $this->getRequest()->setParam('type', $content->getType());
            if (!$this->_authorization->isAllowed($this->getContentResourceName('view'))) {
                throw new LocalizedException(
                    __('Sorry, you need permissions to view this content.')
                );
            }

            $resultPage = $this->pageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(
                $content->getTitle()
            );

            /**
             * Start to hook the live editor.
             */
            Hooks::doAction('pagebuilder/editor/index');

            return $resultPage;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when entering the Page Builder.')
            );
        }

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
