<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Store\Model\ScopeInterface;

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
    public function getConfig($path, string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getConfig(self::ACTIVE_XML_PATH);
    }

    /**
     * @param $name
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getBuilderConfig($name, string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
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
     * @return bool
     */
    public function isBuilderAssistanceActive()
    {
        return (bool) $this->getBuilderConfig(
            'builder_assistance/active'
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
     * Enabled Debug Mode or not
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return (bool) $this->getBuilderConfig(
            'editor/debug'
        );
    }

    /**
     * Use local font instead
     *
     * @return bool
     */
    public function isLocalFont()
    {
        return (bool) $this->getBuilderConfig(
            'editor/style/local_font'
        );
    }

    /**
     * Use Inline CSS
     *
     * @return bool
     */
    public function useInlineCss()
    {
        return (bool) $this->getBuilderConfig(
            'editor/style/use_inline_css'
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
    /**
     * Should add resources globally
     * @return bool
     */
    public function addResourceGlobally()
    {
        return (bool) $this->getBuilderConfig(
            'editor/resources_globally'
        );
    }

    /**
     * Facebook App Key
     * @return string
     */
    public function getFbAppId()
    {
        return (string) $this->getBuilderConfig(
            'integrations/fb_app_id'
        );
    }

    /**
     * Maps Embedded Key by Google
     * @return string
     */
    public function getGoogleMapsKey()
    {
        return (string) $this->getBuilderConfig(
            'integrations/google_maps_key'
        );
    }
}
