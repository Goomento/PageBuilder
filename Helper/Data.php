<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 * @package Goomento\PageBuilder\Helper
 */
class Data extends AbstractHelper
{
    use TraitStaticInstances;

    const ACTIVE_XML_PATH = 'pagebuilder/general/active';

    /**
     * @param $path
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getConfig($path, string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->scopeConfig->isSetFlag(self::ACTIVE_XML_PATH);
    }

    /**
     * @param $name
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getPageBuilderConfig($name, string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->getConfig("pagebuilder/{$name}", $scopeType, $scopeCode);
    }

    /**
     * @return bool
     */
    public function getAllowedDownloadImage()
    {
        return (bool) $this->getConfig('import/allowed_download_image');
    }

    /**
     * @return string
     */
    public function getDownloadedImageFolder()
    {
        return (string) $this->getPageBuilderConfig('import/allowed_download_image');
    }

    /**
     * @return string
     */
    public function getRenderFallback()
    {
        return (string) $this->getPageBuilderConfig('editor/style/css_print_method');
    }

    /**
     * @return int
     */
    public function getAllowedNumberOfRevision()
    {
        $number = $this->getPageBuilderConfig('editor/number_of_revision');
        if (null === $number) {
            $number = 100;
        }

        return $number;
    }
}
