<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Ui\Component\Listing\Column\Content;

use Goomento\PageBuilder\Helper\StaticAuthorization;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class PageActions
 * @package Goomento\PageBuilder\Ui\Component\Listing\Column\Content
 */
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

                    if (StaticAuthorization::isCurrentUserCan($type)) {

                        if (StaticAuthorization::isCurrentUserCan($type . '_view')) {
                            $item[$name]['edit'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    'pagebuilder/content/edit',
                                    [
                                        'content_id' => $item['content_id'],
                                        'type' => $type,
                                    ]
                                ),
                                'label' => __('Edit')
                            ];
                            $item[$name]['editor'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    'pagebuilder/content/editor',
                                    [
                                        'content_id' => $item['content_id'],
                                        'type' => $type,
                                    ]
                                ),
                                'label' => __('Page Builder')
                            ];
                            $item[$name]['view'] = [
                                'href' => StaticUrlBuilder::getContentViewUrl($item['content_id']),
                                'label' => __('View'),
                                'target' => '_blank'
                            ];
                        }

                        if (StaticAuthorization::isCurrentUserCan($type . '_export') === true) {
                            $item[$name]['export'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    'pagebuilder/content/export',
                                    [
                                        'content_id' => $item['content_id'],
                                        'type' => $type,
                                    ]
                                ),
                                'label' => __('Export')
                            ];
                        }

                        if (StaticAuthorization::isCurrentUserCan($type . '_delete') === true) {
                            $title = $this->getEscaper()->escapeHtml($item['title']);
                            $item[$name]['delete'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    'pagebuilder/content/delete',
                                    [
                                        'content_id' => $item['content_id'],
                                        'type' => $item['type'],
                                    ]
                                ),
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
