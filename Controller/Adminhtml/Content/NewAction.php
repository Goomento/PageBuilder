<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;

class NewAction extends Action implements HttpGetActionInterface
{
    use TraitContent;

    /**
     * @var Forward
     */
    protected $resultForwardFactory;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
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
                __('Something went wrong when creating content(s)')
            );
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
