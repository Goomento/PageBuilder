<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Goomento\Core\Traits\TraitHttpAction;
use Goomento\Core\Traits\TraitHttpExecutable;
use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Block\Adminhtml\Component\Wysiwyg;
use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\PageBuilder\Helper\BuildableContent;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Model\Config\Source\PageList;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

class BuilderAssistance extends AbstractAction
{
    use TraitHttpExecutable;
    use TraitHttpAction;

    /**
     * @var PageList
     */
    private $pageList;
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;
    /**
     * @var StoreManagerInterface|StoreManager
     */
    private $storeManager;
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var BuildableContent
     */
    private $buildableContentHelper;

    /**
     *
     * @param Action\Context $context
     * @param ContentRegistryInterface $contentRegistry
     * @param LayoutFactory $layoutFactory
     * @param StoreManagerInterface $storeManager
     * @param Data $dataHelper
     * @param PageList $pageList
     * @param BuildableContent $buildableContentHelper
     */
    public function __construct(
        Action\Context $context,
        ContentRegistryInterface $contentRegistry,
        LayoutFactory $layoutFactory,
        StoreManagerInterface $storeManager,
        Data $dataHelper,
        PageList $pageList,
        BuildableContent $buildableContentHelper
    ) {
        $this->buildableContentHelper = $buildableContentHelper;
        $this->pageList = $pageList;
        $this->contentRegistry = $contentRegistry;
        $this->layoutFactory = $layoutFactory;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Create the Content with default HTML
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function executePost()
    {
        $data = $this->getParams();
        $action = $data['action'] ?? null;
        $result = [];
        if (!empty($action)) {
            switch ($action) {
                case 'create':
                    $contentData = [
                        'title' => (string) ($data['title'] ?? __('Block %1', EncryptorHelper::randomString())),
                        'status' => BuildableContentInterface::STATUS_PENDING,
                        'type' => ContentInterface::TYPE_SECTION,
                        'elements' => [],
                        'settings' => [],
                    ];

                    if (!empty($data['html'])) {
                        $contentData = BuildableContent::getContentElementsWithHtml($data['html'], $contentData);
                    }
                    $content = $this->buildableContentHelper->createContent($contentData);

                    $result = [
                        'label' => BuildableContentHelper::getContentLabel($content),
                        'value' => $content->getIdentifier(),
                    ];
                    break;
                case 'wysiwyg':
                    $result['html'] = $this->getWysiwygBlock($data)->toHtml();
                    break;
                default:
                    break;
            }
        }

        return $this->setResponseData($result)->sendResponse();
    }

    /**
     * @param array $data
     * @return BlockInterface
     * @throws NoSuchEntityException
     */
    private function getWysiwygBlock(array $data)
    {
        $storeId = $data['store_id'] ?? 0;
        $elementId = $data['element_id'] ?? EncryptorHelper::randomString();
        $this->storeManager->setCurrentStore($storeId);
        $storeMediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $this->layoutFactory->create()->createBlock(
            Wysiwyg::class,
            '',
            [
                'data' => [
                    'element_id' => $elementId,
                    'document_base_url' => $storeMediaUrl,
                    'store_id' => $storeId,
                ]
            ]
        );
    }

    /**
     * @return mixed
     */
    protected function executeGet()
    {
        $data = $this->getParams();
        $action = $data['action'] ?? null;
        $result = [];
        if (!empty($action)) {
            switch ($action) {
                case 'edit':
                    $identifier = $data['identifier'];
                    $content = $this->contentRegistry->getByIdentifier($identifier);
                    if ($content) {
                        $result['href'] = UrlBuilderHelper::getLiveEditorUrl($content);
                        $result['open_in'] = (string) $this->dataHelper->getBuilderConfig('builder_assistance/open_in');
                    }
                    break;
                case 'list':
                    $result = $this->pageList->toOptionArray();
                    break;
                default:
                    break;
            }
        }
        return $this->setResponseData(
            $result
        )->sendResponse();
    }

    /**
     * @return array
     */
    private function getParams()
    {
        if ($this->getRequest()->getPostvalue()) {
            $data = $this->getRequest()->getPostvalue();
        } else {
            $data = $this->getRequest()->getParams();
        }

        return EscaperHelper::filter((array) $data);
    }
}
