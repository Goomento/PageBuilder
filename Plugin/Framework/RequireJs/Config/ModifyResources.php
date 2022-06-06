<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Framework\RequireJs\Config;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\RequireJs\Config;

class ModifyResources
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
     * @param Config $config
     * @param $result
     * @return string
     */
    public function afterGetConfig(
        Config $config,
        $result
    )
    {
        $magentoVersion = $this->productMetadata->getVersion();
        if ($magentoVersion && version_compare($magentoVersion, '2.4.4', '<')) {
            $result .= $this->getUnderscoreJs();
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getUnderscoreJs() : string
    {
        $requireJs = "var config = { paths: { 'underscore': 'Goomento_PageBuilder/lib/underscore/underscore.min' } };";
        return str_replace(
            '%config%',
            trim($requireJs),
            Config::PARTIAL_CONFIG_TEMPLATE
        );
    }
}
