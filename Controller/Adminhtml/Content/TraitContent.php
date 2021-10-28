<?php

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Model\Content;
use Magento\Framework\Exception\LocalizedException;

trait TraitContent
{
    /**
     * @return string
     * @throws LocalizedException
     */
    private function getContentType() : string
    {
        $type = (string) $this->getRequest()->getParam('type');
        if (!in_array($type, array_keys(Content::getAvailableTypes()))) {
            throw new LocalizedException(
                __('Invalid content type: %1', $type)
            );
        }

        return $type;
    }

    /**
     * @param string $append
     * @return string
     * @throws LocalizedException
     */
    private function getContentResourceName(string $append = '')
    {
        return sprintf(
            'Goomento_PageBuilder::%s%s',
            $this->getContentType(),
            $append ? '_' . $append : ''
        );
    }

    /**
     * @param string $append
     * @return string
     * @throws LocalizedException
     */
    private function getContentLayoutName(string $append = '')
    {
        return sprintf(
            'pagebuilder_%s%s',
            $this->getContentType(),
            $append ? '_' . $append : ''
        );
    }
}
