<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;

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

            ThemeHelper::registerContentToPage($content);

            /**
             * Start to hook the live editor.
             */
            HooksHelper::doAction('pagebuilder/editor/index', $content);

            return $resultPage;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when entering the Page Builder.')
            );
        }

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
