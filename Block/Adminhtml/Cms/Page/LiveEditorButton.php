<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Cms\Page;

use Goomento\PageBuilder\Model\ContentRelation;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class LiveEditorButton
 * @package Goomento\PageBuilder\Block\Adminhtml\Cms\Page
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
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->request = $context->getRequest();
        $this->url = $context->getUrlBuilder();
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $pageId = $this->request->getParam('page_id');
        $button = [];
        if (!empty($pageId)) {
            $button = [
                'label' => __('Page Builder'),
                'class' => 'gmt-pagebuilder',
                'on_click' => sprintf("location.href = '%s';", $this->getCreateEditorUrl()),
                'sort_order' => 20,
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
            'id' => $this->request->getParam('page_id'),
            'type' => ContentRelation::TYPE_CMS_PAGE
        ]);
    }
}
