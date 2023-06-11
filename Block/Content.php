<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block;

use Exception;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Goomento\PageBuilder\Model\ContentDataProcessor;
use Goomento\PageBuilder\Model\ContentRegistry;
use Goomento\PageBuilder\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Content extends Template implements BlockInterface, IdentityInterface
{
    /**
     * @var ContentInterface[]
     */
    protected $buildableContents = [];
    /**
     * @var bool[]
     */
    protected $validate = [];
    /**
     * @var string[]
     */
    protected $html = [];
    /**
     * @var FilterProvider
     */
    protected $filterProvider;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ContentRegistry
     */
    protected $contentRegistry;
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var ContentDataProcessor
     */
    private $contentDataProcessor;
    /**
     * Current content Id
     *
     * @var int|null
     */
    private $currentContentId;
    /**
     * Current identifier Id
     *
     * @var string|null
     */
    private $currentIdentifier;

    /**
     * identifier-id mapping
     *
     * @var string[]
     */
    private $identifierIdMapping = [];

    /**
     * Content constructor.
     * @param Template\Context $context
     * @param Data $dataHelper
     * @param FilterProvider $filterProvider
     * @param ContentRegistry $contentRegistry
     * @param Logger $logger
     * @param ContentDataProcessor $contentHtmlProcessor
     * @param array $data
     */
    public function __construct(
        Template\Context         $context,
        Data                     $dataHelper,
        FilterProvider           $filterProvider,
        ContentRegistryInterface $contentRegistry,
        ContentDataProcessor     $contentHtmlProcessor,
        Logger                   $logger,
        array                    $data = []
    ) {
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->filterProvider = $filterProvider;
        $this->contentRegistry = $contentRegistry;
        $this->contentDataProcessor = $contentHtmlProcessor;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        if ($this->hasData('identifier')) {
            $this->setIdentifier((string) $this->getData('identifier'));
            $this->unsetData('identifier');
        }

        if ($this->hasData('content_id')) {
            $this->setContentId((int) $this->getData('content_id'));
            $this->unsetData('content_id');
        }

        parent::_construct();
    }

    /**
     * @param $contentId
     * @return Content
     */
    public function setContentId($contentId)
    {
        $this->currentIdentifier = null;
        $this->currentContentId = $contentId;
        $content = $this->loadCurrentBuildableContent();
        if ($content && $content->getId()) {
            $this->currentIdentifier = (string) $content->getIdentifier();
        }
        return $this;
    }

    /**
     * Published method for setting content identifier
     *
     * @param string $identifier
     * @return Content
     */
    public function setIdentifier(string $identifier)
    {
        $this->currentContentId = null;
        $this->currentIdentifier = $identifier;
        $content = $this->loadCurrentBuildableContent();
        if ($content && $content->getId()) {
            $this->currentContentId = $content->getId();
        }
        return $this;
    }

    /**
     * @param BuildableContentInterface $buildableContent
     * @return Content
     */
    public function setBuildableContent(BuildableContentInterface $buildableContent)
    {
        $this->buildableContents[$buildableContent->getId()] = $buildableContent;
        $this->identifierIdMapping[(string) $buildableContent->getData('identifier')] = $buildableContent->getId();
        return $this;
    }

    /**
     * @return BuildableContentInterface|null
     */
    protected function loadCurrentBuildableContent()
    {
        $content = null;
        if ($this->currentContentId) {
            $content = $this->buildableContents[$this->currentContentId] ?? null;
            if (!$content) {
                $content = $this->contentRegistry->getById(
                    (int) $this->currentContentId
                );
                if ($content && $content->getId()) {
                    $this->setBuildableContent($content);
                }
            }
        }

        if ($this->currentIdentifier) {
            $contentId = $this->identifierIdMapping[$this->currentIdentifier] ?? null;
            $content = $contentId && isset($this->buildableContents[$contentId])
                ? $this->buildableContents[$contentId] : null;
            if (!$content) {
                $content = $this->contentRegistry->getByIdentifier(
                    (string) $this->currentIdentifier
                );
                if ($content && $content->getId()) {
                    $this->setBuildableContent($content);
                }
            }
        }

        return $content;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException|LocalizedException
     */
    protected function isValidCurrentContent() : bool
    {
        if (!isset($this->validate[$this->currentContentId])) {
            $this->validate[$this->currentContentId] = false;
            // This content can set
            if ($content = $this->loadCurrentBuildableContent()) {
                if ($this->checkValidContent($content)) {
                    $this->validate[$this->currentContentId] = true;
                }
            }
        }

        return (bool) $this->validate[$this->currentContentId];
    }

    /**
     * @param BuildableContentInterface $content
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function checkValidContent(BuildableContentInterface $content) : bool
    {
        return $content->getIsActive() && $this->isContentInStore($content);
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
    protected function _toHtml()
    {
        $html = '';

        Profiler::start('PAGEBUILDER:BLOCK');

        $this->logger->debug(sprintf('Render content: %s', $this->currentContentId));

        if ($this->isValidCurrentContent()) {
            $this->logger->debug(sprintf('Render content: %s', $this->currentContentId));
            try {
                ThemeHelper::registerContentToPage($this->loadCurrentBuildableContent());

                Profiler::start('PAGEBUILDER:RENDER');
                $html = $this->contentDataProcessor->getHtml($this->loadCurrentBuildableContent());
                Profiler::stop('PAGEBUILDER:RENDER');

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
     * @inheritDoc
     */
    public function toHtml()
    {
        $html = $this->html[$this->currentContentId] ?? null;
        if ($html === null) {
            $this->html[$this->currentContentId] = parent::toHtml();
        }
        return $this->html[$this->currentContentId];
    }

    /**
     * @inheridoc
     */
    public function getCacheKey()
    {
        $keys = [parent::getCacheKey()];
        $content = $this->loadCurrentBuildableContent();
        if ($content && $content->getId()) {
            $keys[] = $content->getUniqueIdentity();
        } else {
            if ($this->currentContentId) {
                $keys[] = 'id_' . $this->currentContentId;
            }
            if ($this->currentIdentifier) {
                $keys[] = 'key_' . $this->currentIdentifier;
            }
        }

        return implode('_', $keys);
    }

    /**
     * Get Block html by magic call
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        $keys = [];
        if ($this->currentContentId) {
            $keys[] = 'pagebuilder_content_' . $this->currentContentId;
        }
        if ($this->currentIdentifier) {
            $keys[] = 'pagebuilder_content_' . $this->currentIdentifier;
        }

        return $keys;
    }
}
