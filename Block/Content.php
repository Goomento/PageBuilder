<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Goomento\PageBuilder\Model\ContentHtmlProcessor;
use Goomento\PageBuilder\Model\ContentRegistry;
use Goomento\PageBuilder\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Content extends Template implements BlockInterface
{
    const CONTENT_ID = ContentInterface::CONTENT_ID;
    const IDENTIFIER = ContentInterface::IDENTIFIER;

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
     * @var ContentHtmlProcessor
     */
    private $contentHtmlProcessor;

    /**
     * Content constructor.
     * @param Template\Context $context
     * @param Data $dataHelper
     * @param FilterProvider $filterProvider
     * @param ContentRegistry $contentRegistry
     * @param Logger $logger
     * @param ContentHtmlProcessor $contentHtmlProcessor
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        FilterProvider $filterProvider,
        ContentRegistry $contentRegistry,
        Logger $logger,
        ContentHtmlProcessor $contentHtmlProcessor,
        array $data = []
    )
    {
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->filterProvider = $filterProvider;
        $this->contentRegistry = $contentRegistry;
        $this->contentHtmlProcessor = $contentHtmlProcessor;

        parent::__construct($context, $data);
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
     * @param BuildableContentInterface $content
     * @return Content
     */
    public function setContent(BuildableContentInterface $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return BuildableContentInterface|null
     */
    public function getBuildableContent()
    {
        if ($this->content === null) {
            $content = null;
            if ($this->getContentId()) {
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
            if ($content instanceof BuildableContentInterface && $content->getId()) {
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
            if ($content = $this->getBuildableContent()) {
                $this->setContentId($content->getId());
                if ($content->getIsActive() && $this->isContentInStore($content)) {
                    $this->validated = true;
                }
            }
        }

        return (bool) $this->validated;
    }

    /**
     * @param BuildableContentInterface $content
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isContentInStore(BuildableContentInterface $content)
    {
        $storeIds = $content->getOriginContent()->getStoreIds();
        if (!empty($storeIds)) {
            return in_array(0, $storeIds) || in_array($this->_storeManager->getStore()->getId(), $storeIds);
        }

        return false;
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getContentHtml()
    {
        $html = '';

        Profiler::start('PAGEBUILDER:BLOCK');

        $this->isValidContent();

        $this->logger->debug(sprintf('Render content: %s', $this->getContentId()));

        if ($this->isValidContent()) {
            try {
                ThemeHelper::registerContentToPage($this->getBuildableContent());

                Profiler::start('PAGEBUILDER:RENDER');
                $html = $this->contentHtmlProcessor->getHtml( $this->getBuildableContent() );
                Profiler::stop('PAGEBUILDER:RENDER');

                $this->logger->debug(sprintf('Render content: %s EMPTY', empty($html) ? 'IS' : 'IS NOT'));

                Profiler::start('PAGEBUILDER:CMS_FILTER');
                $html = $this->filterProvider->getPageFilter()->filter($html);
                Profiler::stop('PAGEBUILDER:CMS_FILTER');

            } catch (Exception $e) {
                $this->logger->error($e);
                if ($this->dataHelper->isDebugMode()) {
                    throw $e;
                }
            }
        }

        Profiler::stop('PAGEBUILDER:BLOCK');

        return $html;
    }

    /**
     * @inheridoc
     */
    public function getCacheKey()
    {
        $key = parent::getCacheKey();
        $content = $this->getBuildableContent();
        if ($content instanceof BuildableContentInterface) {
            return $key . '_' . $content->getUniqueIdentity();
        }

        return $key;
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
