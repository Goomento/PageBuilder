<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Cms\Page;

use Goomento\PageBuilder\Model\ContentRelation;
use Goomento\PageBuilder\Helper\StaticEncryptor;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page;
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
     * @var PageRepositoryInterface
     */
    private $pageRepository;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Page
     */
    private $page;
    /**
     * @var ContentRelation
     */
    private $contentRelation;

    /**
     * @param Context $context
     * @param ContentRelation $contentRelation
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        Context $context,
        ContentRelation $contentRelation,
        PageRepositoryInterface $pageRepository
    ) {
        $this->request = $context->getRequest();
        $this->url = $context->getUrlBuilder();
        $this->contentRelation = $contentRelation;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $pageId = $this->request->getParam('page_id');
        $button = [];
        if (!empty($pageId)) {
            $this->page = $this->pageRepository->getById($pageId);
            if ($this->page->getData('pagebuilder_content_id')) {
                $button = [
                    'label' => __('Page Builder'),
                    'class' => 'gmt-pagebuilder',
                    'on_click' => sprintf("location.href = '%s';", $this->getLiveEditorUrl(
                        (int) $this->page->getData('pagebuilder_content_id'))
                    ),
                    'sort_order' => 20,
                ];
            } else {
                $button = [
                    'label' => __('Page Builder'),
                    'class' => 'gmt-pagebuilder',
                    'on_click' => sprintf("location.href = '%s';", $this->getCreateEditorUrl()),
                    'sort_order' => 20,
                ];
            }
        }
        return $button;
    }

    /**
     * @param $contentId
     * @return string
     * @throws \Exception
     */
    private function getLiveEditorUrl($contentId)
    {
        $backUrl = $this->contentRelation->getRelationEditableUrl(
            ContentRelation::TYPE_CMS_PAGE,
            $this->page->getId()
        );
        return $this->url->getUrl('pagebuilder/content/editor', [
            'content_id' => $contentId,
            'back_url' => StaticEncryptor::encrypt($backUrl)
        ]);
    }

    /**
     * @return string
     */
    private function getCreateEditorUrl()
    {
        return $this->url->getUrl('pagebuilder/relation/assignContent', [
            'id' => $this->request->getParam('page_id'),
            'type' => ContentRelation::TYPE_CMS_PAGE
        ]);
    }
}
