<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Catalog\Product;

use Goomento\PageBuilder\Model\ContentRelation;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Class LiveEditorButton
 * @package Goomento\PageBuilder\Block\Adminhtml\Catalog\Product
 */
class LiveEditorButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * LiveEditorButton constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        $this->url = $context->getUrlBuilder();
        $this->request = $context->getRequest();
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $productId = $this->request->getParam('id');
        $button = [];
        if (!empty($productId)) {
            $button = [
                'label' => __('Page Builder'),
                'class' => 'gmt-pagebuilder',
                'on_click' => sprintf("location.href = '%s';", $this->getCreateEditorUrl()),
                'sort_order' => 5,
            ];
        }

        return $button;
    }

    /**
     * @return string
     */
    private function getCreateEditorUrl()
    {
        return $this->url->getUrl('pagebuilder/relation/assign', [
            'id' => $this->request->getParam('id'),
            'store_id' => $this->request->getParam('store') ?: 0,
            'type' => ContentRelation::TYPE_CATALOG_PRODUCT
        ]);
    }
}
