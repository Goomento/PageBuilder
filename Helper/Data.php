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

    /**
     * Config prefix for the whole system
     */
    const PREFIX_XML_PATH = 'pagebuilder';

    /**
     * Active path
     */
    const ACTIVE_XML_PATH = self::PREFIX_XML_PATH . '/general/active';

    /**
     * Magento config path for minify CSS files
     */
    const DEV_CSS_MINIFY_FILES_XML_PATH = 'dev/css/minify_files';

    /**
     * Magento config path for minify JS files
     */
    const DEV_JS_MINIFY_FILES_XML_PATH = 'dev/js/minify_files';

    /**
     * Use for purchased products by Goomento
     */
    const CONNECTOR_BROWSER_CHECK_ENDPOINT = 'https://goomento.com/rest/V1/token/browser-check';

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
    public function isActive() : bool
    {
        return (bool) $this->getConfig(self::ACTIVE_XML_PATH);
    }

    /**
     * @return string
     */
    public function getConnectorToken() : string
    {
        return (string) $this->getBuilderConfig('connector/token');
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
     * @param string $name
     * @return mixed
     */
    public function getEditorConfig(string $name)
    {
        return $this->getBuilderConfig('editor/' . $name);
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
    public function isBuilderAssistanceActive() : bool
    {
        return (bool) $this->getBuilderConfig(
            'builder_assistance/active'
        );
    }

    /**
     * Show builder assistance to all page
     *
     * @return bool
     */
    public function isBuilderAssistanceOnAllPage() : bool
    {
        return (bool) $this->getBuilderConfig(
            'builder_assistance/all_page'
        );
    }

    /**
     * Show builder assistance to all page
     *
     * @return array
     */
    public function getBuilderAssistanceCustomPages() : array
    {
        $pages = (string) $this->getBuilderConfig(
            'builder_assistance/custom_pages'
        );
        try {
            $pages = DataHelper::decode($pages);
        } catch (\Exception $e) {
            $pages = [];
        }
        return array_column($pages, 'page');
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
        return (bool) $this->getEditorConfig('debug');
    }

    /**
     * Use local font instead
     *
     * @return bool
     */
    public function isLocalFont()
    {
        return (bool) $this->getEditorConfig('style/local_font');
    }

    /**
     * Use Inline CSS
     *
     * @return bool
     */
    public function useInlineCss()
    {
        return (bool) $this->getEditorConfig('style/use_inline_css');
    }

    /**
     * @return int
     * @deprecated
     */
    public function getAllowedNumberOfRevision()
    {
        return (int) $this->getEditorConfig('number_of_revision');
    }
    /**
     * Should add resources globally
     * @return bool
     */
    public function addResourceGlobally()
    {
        return (bool) $this->getEditorConfig('resources_globally');
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

    /**
     * Is CSS minification enable
     *
     * @return bool
     */
    public function isCssMinifyFilesEnabled()
    {
        return (bool) $this->getConfig(self::DEV_CSS_MINIFY_FILES_XML_PATH);
    }

    /**
     * Is JavaScript minification enable
     *
     * @return bool
     */
    public function isJsMinifyFilesEnabled()
    {
        return (bool) $this->getConfig(self::DEV_JS_MINIFY_FILES_XML_PATH);
    }

    /**
     * Get user-defined custom media URL
     *
     * @return string
     */
    public function getCustomMediaUrl()
    {
        return (string) $this->getEditorConfig('media/url');
    }

    /**
     * Get number of revision to keep
     *
     * @return int
     */
    public function getNumberOfRevisionToKeep() : int
    {
        return (int) $this->getEditorConfig('revisions/number_to_keep');
    }
}
