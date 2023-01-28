<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Content\Edit;

use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class LiveEditorButton extends AbstractGenericButton implements ButtonProviderInterface
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
                'label' => __('Editor'),
                'class' => 'live_editor',
                'on_click' => sprintf("location.href = '%s';", $this->getLiveEditorUrl()),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Url to send delete requests to.
     *
     * @return string
     */
    public function getLiveEditorUrl()
    {
        $storeId = 0;
        if ($this->getRequest()->getParam('store')) {
            $storeId = (int) $this->getRequest()->getParam('store');
        }

        return $this->getUrl('pagebuilder/content/editor', [
            'content_id' => $this->getContentId(),
            'type' => $this->getContentType(),
            'store' => $storeId
        ]);
    }
}
