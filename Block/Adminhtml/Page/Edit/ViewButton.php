<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Page\Edit;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Block\Adminhtml\Content\Edit\AbstractGenericButton;
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
            $content = $this->getContent();
            if ($content->getStatus() === ContentInterface::STATUS_PUBLISHED) {
                $data = [
                    'label' => __('View'),
                    'class' => 'preview',
                    'on_click' => sprintf("window.open('%s', '_blank').focus();", $this->getViewUrl($content)),
                    'sort_order' => 10,
                ];
            }
        }
        return $data;
    }

    /**
     * Url to send view page.
     *
     * @return string
     */
    public function getViewUrl(ContentInterface $content)
    {
        return UrlBuilderHelper::getPublishedContentUrl($this->getContent());
    }
}
