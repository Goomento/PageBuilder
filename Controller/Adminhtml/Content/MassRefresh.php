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

class MassRefresh extends AbstractMassAction
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
                $this->contentManagement->refreshBuildableContentAssets($content);
                $count++;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('You cleaned %1 out of %2 content(s) cache', $count, $collection->count())
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
                __('Something went wrong when display content(s)')
            );
        }

        return false;
    }
}
