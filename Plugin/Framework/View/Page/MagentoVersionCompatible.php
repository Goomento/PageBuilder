<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

namespace Goomento\PageBuilder\Plugin\Framework\View\Page;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Page\Config\RendererInterface;

class MagentoVersionCompatible
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    )
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param RendererInterface $subject
     * @param $result
     * @return string
     */
    public function afterRenderHeadContent(RendererInterface $subject, $result)
    {
        if (is_string($result)) {
            try {
                $magentoVersion = $this->productMetadata->getVersion();
                $shouldModify = $magentoVersion && version_compare($magentoVersion, '2.4.4', '<');
                if ($shouldModify) {
                    $result = $this->getUnderscoreJs() . $result;
                }
            } catch (\Exception $e) {}
        }
        return $result;
    }

    /**
     * @return string
     */
    private function getUnderscoreJs()
    {
        return <<<HTML
<script>
    var require = window.require || {};
    require.paths = Object.assign({}, require.paths, {
        "underscore": "Goomento_PageBuilder/lib/underscore/underscore.min"
    });
</script>
HTML;
    }
}
