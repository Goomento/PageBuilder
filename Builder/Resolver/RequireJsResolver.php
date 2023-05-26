<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Resolver;

use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class RequireJsResolver
{
    /**
     * Add hook to theme
     */
    public function __construct()
    {
        HooksHelper::addFilter('pagebuilder/editor/requirejs_config', [$this, 'updateEditorRequireJs']);
        HooksHelper::addFilter('pagebuilder/editor/js_variables', [$this, 'updateBuilderJsVariables']);
    }

    /**
     * @param array $requirejs
     * @return array
     */
    public function updateEditorRequireJs(array $requirejs) : array
    {
        $magentoVersion = Developer::magentoVersion();

        if (version_compare($magentoVersion, '2.3.6', '>=')) {
            $requirejs['paths']['jquery/file-uploader'] = 'jquery/fileUploader/jquery.fileuploader';
        }

        $requirejs['map']['*']['wysiwygAdapter'] = '//cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js';

        ThemeHelper::removeScripts('underscore');
        ThemeHelper::registerScript('underscore', 'Goomento_PageBuilder/lib/underscore/underscore.min');

        // Remove jQuery UI
        $magentoVersion = Developer::magentoVersion();
        ThemeHelper::removeScripts('jquery/ui');
        $jqueryUi = version_compare($magentoVersion, '2.4.5', '>=') ?
            'Goomento_PageBuilder/lib/jquery/jquery-ui.1.13.2.min' :
            'Goomento_PageBuilder/lib/jquery/jquery-ui.1.11.4.min';

        ThemeHelper::registerScript(
            'jquery/ui',
            $jqueryUi,
            ['jquery'],
            [
                'requirejs' => [
                    'map' => [
                        '*' => [
                            'jquery-ui-modules/widget' => 'jquery/ui',
                            'jquery-ui-modules/core' => 'jquery/ui',
                            'jquery-ui-modules/accordion' => 'jquery/ui',
                            'jquery-ui-modules/autocomplete' => 'jquery/ui',
                            'jquery-ui-modules/button' => 'jquery/ui',
                            'jquery-ui-modules/datepicker' => 'jquery/ui',
                            'jquery-ui-modules/dialog' => 'jquery/ui',
                            'jquery-ui-modules/draggable' => 'jquery/ui',
                            'jquery-ui-modules/droppable' => 'jquery/ui',
                            'jquery-ui-modules/effect-blind' => 'jquery/ui',
                            'jquery-ui-modules/effect-bounce' => 'jquery/ui',
                            'jquery-ui-modules/effect-clip' => 'jquery/ui',
                            'jquery-ui-modules/effect-drop' => 'jquery/ui',
                            'jquery-ui-modules/effect-explode' => 'jquery/ui',
                            'jquery-ui-modules/effect-fade' => 'jquery/ui',
                            'jquery-ui-modules/effect-fold' => 'jquery/ui',
                            'jquery-ui-modules/effect-highlight' => 'jquery/ui',
                            'jquery-ui-modules/effect-scale' => 'jquery/ui',
                            'jquery-ui-modules/effect-pulsate' => 'jquery/ui',
                            'jquery-ui-modules/effect-shake' => 'jquery/ui',
                            'jquery-ui-modules/effect-slide' => 'jquery/ui',
                            'jquery-ui-modules/effect-transfer' => 'jquery/ui',
                            'jquery-ui-modules/effect' => 'jquery/ui',
                            'jquery-ui-modules/menu' => 'jquery/ui',
                            'jquery-ui-modules/mouse' => 'jquery/ui',
                            'jquery-ui-modules/position' => 'jquery/ui',
                            'jquery-ui-modules/progressbar' => 'jquery/ui',
                            'jquery-ui-modules/resizable' => 'jquery/ui',
                            'jquery-ui-modules/selectable' => 'jquery/ui',
                            'jquery-ui-modules/slider' => 'jquery/ui',
                            'jquery-ui-modules/sortable' => 'jquery/ui',
                            'jquery-ui-modules/spinner' => 'jquery/ui',
                            'jquery-ui-modules/tabs' => 'jquery/ui',
                            'jquery-ui-modules/tooltip' => 'jquery/ui',
                            'jquery-ui-modules/timepicker' => 'jquery/ui',
                        ]
                    ]
                ]
            ]
        );

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
