<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Content\Edit;

use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ExportButton extends AbstractGenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!AuthorizationHelper::isCurrentUserCan($this->getContentType() . '_export')) {
            return [];
        }

        if ($this->getContent()) {
            return [
                'label' => __('Export'),
                'on_click' => sprintf("location.href = '%s';", $this->getExportUrl()),
                'class' => 'export',
                'sort_order' => 20
            ];
        } else {
            return [];
        }
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getExportUrl()
    {
        return $this->getUrl('*/*/export', [
            'content_id' => $this->getContentId(),
            'type' => $this->getContentType()
        ]);
    }
}
