<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Ui\Component\Listing\Column\Content;

use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class PageActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var Escaper|mixed
     */
    private $escaper;
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param ContentRegistryInterface $contentRegistry
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ContentRegistryInterface $contentRegistry,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->contentRegistry = $contentRegistry;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item = $this->prepareItemData($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return array
     */
    private function prepareItemData(array $item) : array
    {
        if (isset($item['content_id'])) {
            $name = $this->getData('name');

            $type = (string) $item['type'];
            $content = $this->contentRegistry->getById((int) $item['content_id']);

            if (AuthorizationHelper::isCurrentUserCan($type)) {
                if (AuthorizationHelper::isCurrentUserCan($content->getRoleName('save'))) {
                    $item[$name]['editor'] = [
                        'href' => UrlBuilderHelper::getLiveEditorUrl($content),
                        'label' => __('Editor')
                    ];
                    $item[$name]['edit'] = [
                        'href' => UrlBuilderHelper::getContentEditUrl($content),
                        'label' => __('Edit')
                    ];
                }

                if (AuthorizationHelper::isCurrentUserCan($content->getRoleName('view'))) {
                    $item[$name]['preview'] = [
                        'href' => UrlBuilderHelper::getContentViewUrl($content),
                        'label' => __('Preview'),
                        'target' => '_blank'
                    ];

                    if ($item['type'] === ContentInterface::TYPE_PAGE &&
                        $item['status'] === BuildableContentInterface::STATUS_PUBLISHED) {
                        $item[$name]['view'] = [
                            'href' => UrlBuilderHelper::getPublishedContentUrl($content),
                            'label' => __('View'),
                            'target' => '_blank'
                        ];
                    }
                }

                if (AuthorizationHelper::isCurrentUserCan($content->getRoleName('export'))) {
                    $item[$name]['export'] = [
                        'href' => UrlBuilderHelper::getContentExportUrl($content),
                        'label' => __('Export')
                    ];
                }

                if (AuthorizationHelper::isCurrentUserCan($content->getRoleName('delete'))) {
                    $title = $this->getEscaper()->escapeHtml($item['title']);
                    $item[$name]['delete'] = [
                        'href' => UrlBuilderHelper::getContentDeleteUrl($content),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __('Are you sure you want to delete a `%1`?', $title),
                            '__disableTmpl' => true,
                        ],
                        'post' => true,
                    ];
                }
            }
        }

        return $item;
    }

    /**
     * Get instance of escaper
     *
     * @return Escaper
     */
    private function getEscaper()
    {
        if (!$this->escaper) {
            $this->escaper = ObjectManager::getInstance()->get(Escaper::class);
        }
        return $this->escaper;
    }
}
