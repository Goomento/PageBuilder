<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Model\ContentRegistry;
use Goomento\PageBuilder\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Content extends Template implements BlockInterface
{
    const CONTENT_ID = ContentInterface::CONTENT_ID;
    const IDENTIFIER = ContentInterface::IDENTIFIER;

    const BLOCK_CONTENT_KEY = 'pagebuilder_content_html';
    const BLOCK_CONTENT_RENDER_ORDER = 2021;

    /**
     * @var null|ContentInterface
     */
    private $content = null;

    /**
     * @var null|bool
     */
    private $validated = null;

    /**
     * @inheritdoc
     */
    protected $_template = 'Goomento_PageBuilder::content.phtml';
    /**
     * @var FilterProvider
     */
    protected $filterProvider;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var string
     */
    private $html;
    /**
     * @var ContentRegistry
     */
    protected $contentRegistry;
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var string
     */
    private $currentContentId;

    /**
     * Content constructor.
     * @param Template\Context $context
     * @param Data $dataHelper
     * @param FilterProvider $filterProvider
     * @param ContentRegistry $contentRegistry
     * @param Logger $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        FilterProvider $filterProvider,
        ContentRegistry $contentRegistry,
        Logger $logger,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->filterProvider = $filterProvider;
        $this->contentRegistry = $contentRegistry;
        $this->logger = $logger;
    }

    /**
     * @param $html
     * @return mixed|string
     * @throws Exception
     */
    public function applyDefaultFilter($html)
    {
        if (trim($html)) {
            $html = $this->filterProvider->getPageFilter()->filter($html);
        }
        return $html;
    }

    /**
     * @param int $id
     * @return Content
     */
    public function setContentId($id)
    {
        $this->setData(self::CONTENT_ID, $id);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getContentId()
    {
        return (int) $this->getData(self::CONTENT_ID);
    }

    /**
     * @param string $identifier
     * @return Content
     */
    public function setIdentifier(string $identifier)
    {
        $this->setData(self::IDENTIFIER, $identifier);
        return $this;
    }

    /**
     * @param ContentInterface $content
     * @return Content
     */
    public function setContent(ContentInterface $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return ContentInterface|null
     */
    public function getContent()
    {
        if ($this->content === null) {
            $content = null;
            if (!$content && $this->getContentId()) {
                $content = $this->contentRegistry->getById(
                    (int) $this->getContentId()
                );
            }
            if (!$content && $this->getIdentifier()) {
                $content = $this->contentRegistry->getByIdentifier(
                    (string) $this->getIdentifier()
                );
            }

            $this->content = false;
            if ($content instanceof ContentInterface && $content->getId()) {
                $this->content = $content;
            }
        }

        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @return bool
     * @throws NoSuchEntityException|LocalizedException
     */
    protected function isValidContent()
    {
        if ($this->validated === null) {
            $this->validated = false;
            if ($content = $this->getContent()) {
                $this->setContentId($content->getId());
                if ($content->getIsActive() && $this->isContentInStore($content)) {
                    $this->validated = true;
                }
            }
        }

        return (bool) $this->validated;
    }

    /**
     * @param ContentInterface $content
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isContentInStore(ContentInterface $content)
    {
        $storeIds = $content->getStoreIds();
        if (!empty($storeIds)) {
            return in_array(0, $storeIds) || in_array($this->_storeManager->getStore()->getId(), $storeIds);
        }

        return false;
    }

    /**
     * Use for re-hook
     * @return mixed
     */
    public function getCurrentContentId()
    {
        return $this->currentContentId;
    }

    /**
     * @param string|null $html
     * @return string
     * @throws Exception
     */
    public function getContentHtml(string $html = '')
    {
        $html = (string) $html;
        if ($this->isValidContent()) {
            try {
                HooksHelper::addFilter(
                    'pagebuilder/content/html',
                    [$this, 'applyDefaultFilter'],
                    self::BLOCK_CONTENT_RENDER_ORDER
                );

                $currentProcessingContentId = HooksHelper::applyFilters('pagebuilder/current/content_id');

                if ($currentProcessingContentId) {
                    HooksHelper::removeFilter('pagebuilder/current/content_id');
                }

                HooksHelper::addFilter(
                    'pagebuilder/current/content_id',
                    [$this, 'getContentId'],
                    self::BLOCK_CONTENT_RENDER_ORDER
                );

                ThemeHelper::registerContentToPage($this->getContent());

                /**
                 * Get HTML content
                 */
                $html = HooksHelper::applyFilters('pagebuilder/content/html', $html);

                if ($currentProcessingContentId) {
                    $this->currentContentId = $currentProcessingContentId;
                    HooksHelper::addFilter(
                        'pagebuilder/current/content_id',
                        [$this, 'getCurrentContentId'],
                        self::BLOCK_CONTENT_RENDER_ORDER
                    );
                }
            } catch (Exception $e) {
                $this->logger->error($e);
                if ($this->isAllowedFallback()) {
                    $html = $this->fallback();
                } else {
                    throw $e;
                }
            }
        }

        return $html;
    }

    /**
     * Whether display error or not
     *
     * @return bool
     */
    protected function isAllowedFallback()
    {
        return $this->dataHelper->getRenderFallback() !== 'nothing';
    }

    /**
     * @return string
     */
    protected function fallback()
    {
        $fallback = $this->dataHelper->getRenderFallback();
        switch ($fallback) {
            case 'empty':
            default:
                $html = '';
        }

        return $html;
    }

    /**
     * @inheridoc
     */
    public function getCacheKey()
    {
        $content = $this->getContent();
        if ($content instanceof ContentInterface) {
            $contentId = (int) $content->getId();
            return self::BLOCK_CONTENT_KEY . '_' . $contentId;
        }

        return self::BLOCK_CONTENT_KEY;
    }

    /**
     * @inheritDoc
     */
    public function toHtml()
    {
        if ($this->html === null) {
            $this->html = parent::toHtml();
        }
        return $this->html;
    }

    /**
     * Get Block html by magic call
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->toHtml();
    }
}
