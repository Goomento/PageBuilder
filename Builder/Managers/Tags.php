<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Modules\Ajax as Ajax;
use Goomento\PageBuilder\Builder\Base\AbstractBaseTag;
use Goomento\PageBuilder\Builder\Base\AbstractTag;
use Goomento\PageBuilder\Helper\HooksHelper;

class Tags
{
    const TAG_LABEL = 'gmt-tag';

    const MODE_RENDER = 'render';

    const MODE_REMOVE = 'remove';

    const DYNAMIC_SETTING_KEY = '__dynamic__';

    private $tags_groups = [];

    private $tags_info = [];

    private $parsing_mode = self::MODE_RENDER;

    /**
     * Dynamic tags manager constructor.
     *
     * Initializing SagoTheme dynamic tags manager.
     *
     */
    public function __construct()
    {
        $this->addActions();
    }

    /**
     * Parse dynamic tags text.
     *
     * Receives the dynamic tag text, and returns a single value or multiple values
     * from the tag callback function.
     *
     *
     * @param string   $text           Dynamic tag text.
     * @param array    $settings       The dynamic tag settings.
     * @param callable $parse_callback The functions that renders the dynamic tag.
     *
     * @return string|string[]|mixed A single string or an array of strings with
     *                               the return values from each tag callback
     *                               function.
     */
    public function parseTagsText($text, array $settings, callable $parse_callback)
    {
        if (!empty($settings['returnType']) && 'object' === $settings['returnType']) {
            $value = $this->parseTagText($text, $settings, $parse_callback);
        } else {
            $value = preg_replace_callback('/\[' . self::TAG_LABEL . '.+?(?=\])\]/', function ($tag_text_match) use ($settings, $parse_callback) {
                return $this->parseTagText($tag_text_match[0], $settings, $parse_callback);
            }, $text);
        }

        return $value;
    }

    /**
     * Parse dynamic tag text.
     *
     * Receives the dynamic tag text, and returns the value from the callback
     * function.
     *
     *
     * @param string   $tag_text       Dynamic tag text.
     * @param array    $settings       The dynamic tag settings.
     * @param callable $parse_callback The functions that renders the dynamic tag.
     *
     * @return string|array|mixed If the tag was not found an empty string or an
     *                            empty array will be returned, otherwise the
     *                            return value from the tag callback function.
     */
    public function parseTagText($tag_text, array $settings, callable $parse_callback)
    {
        $tag_data = $this->tagTextToTagData($tag_text);

        if (!$tag_data) {
            if (!empty($settings['returnType']) && 'object' === $settings['returnType']) {
                return [];
            }

            return '';
        }

        return call_user_func_array($parse_callback, $tag_data);
    }

    /**
     *
     * @param string $tag_text
     *
     * @return array|null
     */
    public function tagTextToTagData($tag_text)
    {
        preg_match('/id="(.*?(?="))"/', $tag_text, $tag_id_match);
        preg_match('/name="(.*?(?="))"/', $tag_text, $tag_name_match);
        preg_match('/settings="(.*?(?="]))/', $tag_text, $tag_settings_match);

        if (!$tag_id_match || ! $tag_name_match || ! $tag_settings_match) {
            return null;
        }

        return [
            'id' => $tag_id_match[1],
            'name' => $tag_name_match[1],
            'settings' => json_decode(urldecode($tag_settings_match[1]), true),
        ];
    }

    /**
     * Dynamic tag to text.
     *
     * Retrieve the shortcode that represents the dynamic tag.
     *
     *
     * @param AbstractBaseTag $tag An instance of the dynamic tag.
     *
     * @return string The shortcode that represents the dynamic tag.
     */
    public function tagToText(AbstractBaseTag $tag)
    {
        return sprintf('[%1$s id="%2$s" name="%3$s" settings="%4$s"]', self::TAG_LABEL, $tag->getId(), $tag->getName(), urlencode(\Zend_Json::encode($tag->getSettings(), JSON_FORCE_OBJECT)));
    }

    /**
     * @param string $tag_id
     * @param string $tag_name
     * @param array  $settings
     *
     * @return string
     */
    public function tagDataToTagText($tag_id, $tag_name, array $settings = [])
    {
        $tag = $this->createTag($tag_id, $tag_name, $settings);

        if (!$tag) {
            return '';
        }

        return $this->tagToText($tag);
    }

    /**
     * @param string $tag_id
     * @param string $tag_name
     * @param array  $settings
     *
     * @return AbstractTag|null
     */
    public function createTag($tag_id, $tag_name, array $settings = [])
    {
        $tag_info = $this->getTagInfo($tag_name);

        if (!$tag_info) {
            return null;
        }

        $tag_class = $tag_info['class'];

        return new $tag_class([
            'settings' => $settings,
            'id' => $tag_id,
        ]);
    }

    /**
     *
     * @param       $tag_id
     * @param       $tag_name
     * @param array $settings
     *
     * @return null|string
     */
    public function getTagDataContent($tag_id, $tag_name, array $settings = [])
    {
        if (self::MODE_REMOVE === $this->parsing_mode) {
            return null;
        }

        $tag = $this->createTag($tag_id, $tag_name, $settings);

        if (!$tag) {
            return null;
        }

        return $tag->getContent();
    }

    /**
     *
     * @param $tag_name
     *
     * @return mixed|null
     */
    public function getTagInfo($tag_name)
    {
        $tags = $this->getTags();

        if (empty($tags[ $tag_name ])) {
            return null;
        }

        return $tags[ $tag_name ];
    }


    public function getTags()
    {
        if (! HooksHelper::didAction('goomento/dynamic_tags/register_tags')) {
            /**
             * Register dynamic tags.
             *
             * Fires when SagoTheme registers dynamic tags.
             *
             *
             * @param Tags $this Dynamic tags manager.
             */
            HooksHelper::doAction('pagebuilder/dynamic_tags/register_tags', $this);
        }

        return $this->tags_info;
    }

    /**
     *
     * @param string $class
     */
    public function registerTag($class)
    {
        /** @var AbstractTag $tag */
        $tag = new $class();

        $this->tags_info[ $tag->getName() ] = [
            'class' => $class,
            'instance' => $tag,
        ];
    }

    /**
     *
     * @param string $tag_name
     */
    public function unregisterTag($tag_name)
    {
        unset($this->tags_info[ $tag_name ]);
    }

    /**
     *
     * @param       $group_name
     * @param array $group_settings
     */
    public function registerGroup($group_name, array $group_settings)
    {
        $default_group_settings = [
            'title' => '',
        ];

        $group_settings = array_merge($default_group_settings, $group_settings);

        $this->tags_groups[ $group_name ] = $group_settings;
    }


    public function printTemplates()
    {
        foreach ($this->getTags() as $tag_name => $tag_info) {
            $tag = $tag_info['instance'];

            if (!$tag instanceof AbstractTag) {
                continue;
            }

            $tag->printTemplate();
        }
    }


    public function getTagsConfig()
    {
        $config = [];

        foreach ($this->getTags() as $tag_name => $tag_info) {
            /** @var AbstractTag $tag */
            $tag = $tag_info['instance'];

            $config[ $tag_name ] = $tag->getEditorConfig();
        }

        return $config;
    }


    public function getConfig()
    {
        return [
            'tags' => $this->getTagsConfig(),
            'groups' => $this->tags_groups,
        ];
    }

    /**
     *
     * @throws \Exception If content ID is missing.
     * @throws \Exception If current user don't have permissions to edit the post.
     */
    public function ajaxRenderTags($data)
    {
        if (empty($data['content_id'])) {
            throw new \Exception('Missing content id.');
        }

        /**
         * Before dynamic tags rendered.
         *
         * Fires before SagoTheme renders the dynamic tags.
         *
         */
        HooksHelper::doAction('pagebuilder/dynamic_tags/before_render');

        $tags_data = [];

        foreach ($data['tags'] as $tag_key) {
            $tag_key_parts = explode('-', $tag_key);

            $tag_name = base64_decode($tag_key_parts[0]);

            $tag_settings = json_decode(urldecode(base64_decode($tag_key_parts[1])), true);

            $tag = $this->createTag(null, $tag_name, $tag_settings);

            $tags_data[ $tag_key ] = $tag->getContent();
        }

        /**
         * After dynamic tags rendered.
         *
         * Fires after SagoTheme renders the dynamic tags.
         *
         */
        HooksHelper::doAction('pagebuilder/dynamic_tags/after_render');

        return $tags_data;
    }

    /**
     *
     * @param $mode
     */
    public function setParsingMode($mode)
    {
        $this->parsing_mode = $mode;
    }


    public function getParsingMode()
    {
        return $this->parsing_mode;
    }


    public function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('render_tags', [ $this, 'ajaxRenderTags' ]);
    }


    private function addActions()
    {
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
    }
}
