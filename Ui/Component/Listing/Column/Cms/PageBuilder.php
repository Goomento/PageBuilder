<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Ui\Component\Listing\Column\Cms;

use Goomento\PageBuilder\Helper\StaticEncryptor;
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
     * @var ContentRelation
     */
    private $contentRelation;

    /**
     * PageBuilder constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param ContentRelation $contentRelation
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ContentRelation $contentRelation,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->contentRelation = $contentRelation;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $label = __('Edit');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $field = $item['id_field_name'];
                $type = $field === 'page_id' ? ContentRelation::TYPE_CMS_PAGE : ContentRelation::TYPE_CMS_BLOCK;
                if (isset($item['pagebuilder_content_id']) && $item['pagebuilder_content_id']) {
                    $backUrl = $this->contentRelation->getRelationEditableUrl(
                        $type,
                        $item[$field]
                    );
                    $item[$name]['editor'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'pagebuilder/content/editor',
                            [
                                'content_id' => $item['pagebuilder_content_id'],
                                'back_url' => StaticEncryptor::encrypt($backUrl)
                            ]
                        ),
                        'label' =>$label
                    ];
                } else {
                    $item[$name]['create'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'pagebuilder/relation/assignContent',
                            [
                                'type' => $type,
                                'id' => $item[$field],
                            ]
                        ),
                        'label' => $label,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
