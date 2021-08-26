<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractExport
 * @package Goomento\PageBuilder\Controller\Adminhtml\Content
 */
class Export extends AbstractContent implements HttpGetActionInterface
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

            if (empty($content->getElements())) {
                throw new LocalizedException(
                    __('Template content was empty')
                );
            }

            $this->contentManagement->exportContent($content);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when exporting template.')
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererUrl();
    }
}
