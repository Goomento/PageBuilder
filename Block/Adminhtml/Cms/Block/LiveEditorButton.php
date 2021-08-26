<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Cms\Block;

use Goomento\PageBuilder\Helper\StaticAccessToken;
use Goomento\PageBuilder\Model\ContentRelation;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Block;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Class LiveEditorButton
 * @package Goomento\PageBuilder\Block\Adminhtml\Cms\Block
 */
class LiveEditorButton implements ButtonProviderInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Block
     */
    private $block;
    /**
     * @var ContentRelation
     */
    private $contentRelation;

    /**
     * LiveEditorButton constructor.
     * @param Context $context
     * @param ContentRelation $contentRelation
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context,
        ContentRelation $contentRelation,
        BlockRepositoryInterface $blockRepository
    )
    {
        $this->url = $context->getUrlBuilder();
        $this->request = $context->getRequest();
        $this->contentRelation = $contentRelation;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $blockId = $this->request->getParam('block_id');
        $button = [];
        if (!empty($blockId)) {
            $this->block = $this->blockRepository->getById($blockId);
            if ($this->block->getData('pagebuilder_content_id')) {
                $button = [
                    'label' => __('Page Builder'),
                    'class' => 'gmt-pagebuilder',
                    'on_click' => sprintf("location.href = '%s';", $this->getLiveEditorUrl(
                        (int) $this->block->getData('pagebuilder_content_id'))
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
            ContentRelation::TYPE_CMS_BLOCK,
            $this->block->getId()
        );
        return $this->url->getUrl('pagebuilder/content/editor', [
            'content_id' => $contentId,
            'back_url' => StaticAccessToken::encrypt($backUrl)
        ]);
    }

    /**
     * @return string
     */
    private function getCreateEditorUrl()
    {
        return $this->url->getUrl('pagebuilder/relation/assignContent', [
            'id' => $this->request->getParam('page_id'),
            'type' => ContentRelation::TYPE_CMS_BLOCK
        ]);
    }
}
