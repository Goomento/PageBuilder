<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Ui\Component\Listing\Column\Content;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Shortcode
 * @package Goomento\PageBuilder\Ui\Component\Listing\Column\Content
 */
class Shortcode extends Column
{
    private $template = '{{widget type="PageBuilderRenderer" identifier="%s"}}';
    /**
     * @inheirtDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['identifier'])) {
                    $item[$name] = sprintf($this->template, $item['identifier']);
                }
            }
        }

        return $dataSource;
    }
}
