<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Ui\Component\Listing\Column\Content;

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
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['content_id'])) {
                    $type = (string) $item['type'];

                    if (AuthorizationHelper::isCurrentUserCan($type)) {
                        if (AuthorizationHelper::isCurrentUserCan($type . '_save')) {
                            $item[$name]['edit'] = [
                                'href' => UrlBuilderHelper::getContentEditUrl($item['content_id']),
                                'label' => __('Edit')
                            ];
                            $item[$name]['editor'] = [
                                'href' => UrlBuilderHelper::getLiveEditorUrl($item['content_id']),
                                'label' => __('Editor')
                            ];
                        }
                        if (AuthorizationHelper::isCurrentUserCan($type . '_view')) {
                            $item[$name]['preview'] = [
                                'href' => UrlBuilderHelper::getContentViewUrl($item['content_id']),
                                'label' => __('Preview'),
                                'target' => '_blank'
                            ];

                            if ($item['status'] === ContentInterface::STATUS_PUBLISHED) {
                                $item[$name]['view'] = [
                                    'href' => UrlBuilderHelper::getPublishedContentUrl($item['content_id']),
                                    'label' => __('View'),
                                    'target' => '_blank'
                                ];
                            }
                        }

                        if (AuthorizationHelper::isCurrentUserCan($type . '_export') === true) {
                            $item[$name]['export'] = [
                                'href' => UrlBuilderHelper::getContentExportUrl($item['content_id']),
                                'label' => __('Export')
                            ];
                        }

                        if (AuthorizationHelper::isCurrentUserCan($type . '_delete') === true) {
                            $title = $this->getEscaper()->escapeHtml($item['title']);
                            $item[$name]['delete'] = [
                                'href' => UrlBuilderHelper::getContentDeleteUrl($item['content_id']),
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
            }
        }

        return $dataSource;
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
