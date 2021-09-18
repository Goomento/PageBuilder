<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Model\ContentRegistry;
use Goomento\PageBuilder\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Content
 * @package Goomento\PageBuilder\Block
 */
class Content extends Template implements BlockInterface
{
    const CONTENT_ID = ContentInterface::CONTENT_ID;
    const IDENTIFIER = ContentInterface::IDENTIFIER;

    const BLOCK_CONTENT_KEY = 'pagebuilder_content_html';
    const BLOCK_CONTENT_RENDER_ORDER = 2021;

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
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (!$this->isValidContent()) {
            $this->setTemplate(null);
        }

        return parent::_toHtml();
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isValidContent()
    {
        $content = null;
        if ($this->getContentId()) {
            $content = $this->contentRegistry->getById(
                (int) $this->getContentId()
            );
        }
        if ($this->getIdentifier()) {
            $content = $this->contentRegistry->getByIdentifier(
                (string) $this->getIdentifier()
            );
        }

        if ($content) {
            $this->setContentId($content->getId());
            if ($content->isPublished() && $this->isContentAllowedInStore($content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ContentInterface $content
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isContentAllowedInStore(ContentInterface $content)
    {
        $stores = (array) $content->getStoreId();
        if (!empty($stores)) {
            $stores = array_flip($stores);
            return isset($stores[0]) || isset($stores[$this->_storeManager->getStore()->getId()]);
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
    public function getContentHtml(?string $html = '')
    {
        $html = (string) $html;
        try {
            Hooks::addFilter(
                'pagebuilder/content/html',
                [$this, 'applyDefaultFilter'],
                self::BLOCK_CONTENT_RENDER_ORDER
            );

            $currentProcessingContentId = Hooks::applyFilters('pagebuilder/current/content_id');

            if ($currentProcessingContentId) {
                Hooks::removeFilter('pagebuilder/current/content_id');
            }

            Hooks::addFilter(
                'pagebuilder/current/content_id',
                [$this, 'getContentId'],
                self::BLOCK_CONTENT_RENDER_ORDER
            );

            /**
             * Get HTML content
             */
            $html = Hooks::applyFilters('pagebuilder/content/html', $html);

            if ($currentProcessingContentId) {
                $this->currentContentId = $currentProcessingContentId;
                Hooks::addFilter(
                    'pagebuilder/current/content_id',
                    [$this, 'getCurrentContentId'],
                    self::BLOCK_CONTENT_RENDER_ORDER
                );
            }
        } catch (Exception $e) {
            $this->logger->error($e);
            if (!Configuration::DEBUG && $this->isAllowedFallback()) {
                $html = $this->fallback();
            } else {
                throw $e;
            }
        }
        return $html;
    }

    /**
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
            case 'use_cache':
                $html = $this->contentRegistry->getById(
                    $this->getContentId()
                )->getContent();
                break;
            case 'use_origin':
                $html = (string) $this->getFallback();
                    break;
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
        $contentId = (int) $this->getContentId();
        return self::BLOCK_CONTENT_KEY . '_' . $contentId;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->html === null) {
            $this->html = parent::toHtml();
        }
        return $this->html;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->toHtml();
    }
}
