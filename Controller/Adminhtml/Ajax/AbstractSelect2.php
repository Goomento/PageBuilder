<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Magento\Framework\Data\Collection\AbstractDb;

abstract class AbstractSelect2 extends AbstractAjax
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $term = $this->getRequest()->getParam('term');
        $data = [];
        if ($term && trim($term)) {
            $collection = $this->search(
                trim($term),
                $this->getRequest()->getParam('content_id')
            );
            $data = $collection;
            if ($collection instanceof AbstractDb) {
                $data = [];
                foreach ($collection->getItems() as $item) {
                    $value = $item->getId() . ': ' . $item->getName();
                    $data[] = [
                        'id' => $value,
                        'text' => $value,
                    ];
                }
            }
        }

        $result = (object) [];
        $result->results = $data;

        return $this->setResponseData([
            'data' => $result,
            'status_code' => 200,
        ])->sendResponse();
    }

    /**
     * Eg:
     * [
     *      [
     *          'id' => ID,
     *          'text' => TEXT
     *      ]
     *      ...
     * ]
     * @param string $query
     * @param null $contentId
     * @return array|AbstractDb
     */
    abstract public function search(string $query, $contentId = null);
}
