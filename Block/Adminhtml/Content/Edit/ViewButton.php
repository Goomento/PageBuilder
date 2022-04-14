<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Content\Edit;

use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ViewButton extends AbstractGenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        if (!AuthorizationHelper::isCurrentUserCan($this->getContentType() . '_view')) {
            return [];
        }

        $data = [];
        if ($this->getContentId()) {
            $data = [
                'label' => __('Preview'),
                'class' => 'preview',
                'on_click' => sprintf("window.open('%s', '_blank').focus();", $this->getViewUrl()),
                'sort_order' => 5,
            ];
        }
        return $data;
    }

    /**
     * Url to send view page.
     *
     * @return string
     */
    public function getViewUrl()
    {
        return UrlBuilderHelper::getContentViewUrl($this->getContent());
    }
}
