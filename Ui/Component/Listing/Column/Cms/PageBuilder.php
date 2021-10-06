<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Ui\Component\Listing\Column\Cms;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Goomento\PageBuilder\Model\ContentRelation;

/**
 * Class PageBuilder
 * @package Goomento\PageBuilder\Ui\Component\Listing\Column\Cms
 */
class PageBuilder extends Column
{

    /**
     * @var string
     */
    private $urlBuilder;

    /**
     * PageBuilder constructor.
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
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $field = $item['id_field_name'];
                $type = $field === 'page_id' ? ContentRelation::TYPE_CMS_PAGE : ContentRelation::TYPE_CMS_BLOCK;
                $item[$name]['pagebuilder'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'pagebuilder/relation/assign',
                        [
                            'type' => $type,
                            'id' => $item[$field],
                        ]
                    ),
                    'label' => __('Enter'),
                ];
            }
        }

        return $dataSource;
    }
}
