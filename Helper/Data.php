<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    use TraitStaticInstances;

    const PREFIX_XML_PATH = 'pagebuilder';

    const ACTIVE_XML_PATH = self::PREFIX_XML_PATH . '/general/active';

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
    public function getBuilderConfig($name, string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->getConfig(sprintf('%s/%s', static::PREFIX_XML_PATH, $name), $scopeType, $scopeCode);
    }

    /**
     * @return bool
     */
    public function getAllowedDownloadImage()
    {
        return (bool) $this->getBuilderConfig(
            'import/allowed_download_image'
        );
    }

    /**
     * The download url with slash ending.
     * @return string
     */
    public function getDownloadFolder()
    {
        $folder = (string) $this->getBuilderConfig(
            'import/download_folder'
        );
        $folder = trim($folder);
        if ($folder) {
            $folder = rtrim($folder, '\\/') . '/';
        }

        return $folder;
    }

    /**
     * @return string
     */
    public function getRenderFallback()
    {
        return (string) $this->getBuilderConfig(
            'editor/render/fallback'
        );
    }

    /**
     * @return int
     */
    public function getAllowedNumberOfRevision()
    {
        return (int) $this->getBuilderConfig(
            'editor/number_of_revision'
        );
    }
}
