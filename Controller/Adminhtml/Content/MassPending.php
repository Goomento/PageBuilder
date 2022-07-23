<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class MassPending extends AbstractMassAction
{
    use TraitContent;

    /**
     * @param AbstractCollection $collection
     * @return void
     */
    protected function massAction(AbstractCollection $collection) : void
    {
        $count = 0;
        /** @var ContentInterface $content */
        foreach ($collection->getItems() as $content) {
            if ($content && $content->getId()) {
                $content->setStatus(ContentInterface::STATUS_PENDING);
                $this->contentManagement->saveBuildableContent($content);
                $count++;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('You have set %1 out of %2 content(s) to pending', $count, $collection->count())
        );
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
                __('Something went wrong when set content status')
            );
        }

        return false;
    }
}
