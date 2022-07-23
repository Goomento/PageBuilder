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

class MassPublish extends MassPending
{
    use TraitContent;

    /**
     * @param AbstractCollection $collection
     * @return void
     * @throws LocalizedException
     */
    protected function massAction(AbstractCollection $collection) : void
    {
        $count = 0;
        /** @var ContentInterface $content */
        foreach ($collection->getItems() as $content) {
            if ($content && $content->getId()) {
                $content->setStatus(ContentInterface::STATUS_PUBLISHED);
                $this->contentManagement->saveBuildableContent($content);
                $count++;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('You have published %1 out of %2 content(s)', $count, $collection->count())
        );
    }
}
