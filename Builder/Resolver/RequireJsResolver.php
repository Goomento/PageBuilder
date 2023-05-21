<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Resolver;

use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

class RequireJsResolver
{
    /**
     * Add hook to theme
     */
    public function __construct()
    {
        HooksHelper::addFilter('pagebuilder/editor/requirejs_config', [$this, 'updateBuilderRequireJs']);
        HooksHelper::addFilter('pagebuilder/editor/js_variables', [$this, 'updateBuilderJsVariables']);
    }

    /**
     * @param array $requirejs
     * @return array
     */
    public function updateBuilderRequireJs(array $requirejs) : array
    {
        $magentoVersion = Configuration::magentoVersion();

        if (version_compare($magentoVersion, '2.3.6', '>=')) {
            $requirejs['paths']['jquery/file-uploader'] = 'jquery/fileUploader/jquery.fileuploader';
        }

        if (version_compare($magentoVersion, '2.4.6', '>=')) {
            $requirejs['map']['*']['jquery-ui-modules/timepicker'] = 'jquery/timepicker';
        }

        $requirejs['map']['*']['wysiwygAdapter'] = '//cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js';

        ThemeHelper::removeScripts('underscore');
        ThemeHelper::registerScript('underscore', 'Goomento_PageBuilder/lib/underscore/underscore.min');

        return $requirejs;
    }

    /**
     * @param array $config
     * @return array
     */
    public function updateBuilderJsVariables(array $config) : array
    {
        return $config;
    }
}
