<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\DynamicTags\DataConfig;
use Goomento\PageBuilder\Builder\DynamicTags\Urls;
use Goomento\PageBuilder\Builder\DynamicTags\Images;
use Goomento\PageBuilder\Builder\DynamicTags\CmsBlocks;
use Goomento\PageBuilder\Builder\DynamicTags\CmsPages;
use Goomento\PageBuilder\Builder\DynamicTags\PageBuilderContents;
use Goomento\PageBuilder\Builder\Modules\Ajax;
use Goomento\PageBuilder\Builder\Base\AbstractBaseTag;
use Goomento\PageBuilder\Builder\Base\AbstractTag;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;

class Tags
{
    use TraitComponentsLoader;

    const TAG_LABEL = 'gmt-tag';

    const MODE_RENDER = 'render';

    const MODE_REMOVE = 'remove';

    const DYNAMIC_SETTING_KEY = '__dynamic__';
    /**
     * Dynamic tags text category.
     */
    const TEXT_CATEGORY = 'text';
    /**
     * Dynamic tags WYSIWYG category.
     */
    const WYSIWYG_CATEGORY = 'wysiwyg';
    /**
     * Dynamic tags URL category.
     */
    public const URL_CATEGORY = 'url';
    /**
     * Dynamic tags number category.
     */
    const NUMBER_CATEGORY = 'number';
    /**
     * Base dynamic tag group.
     */
    const BASE_GROUP = 'base';
    /**
     * Dynamic tags image category.
     */
    public const IMAGE_CATEGORY = 'image';
    /**
     * Dynamic tags config category.
     */
    public const ICON_CATEGORY = 'icon';

    private $tagsGroups = [];

    private $tagsInfo = [];

    private $parsingMode = self::MODE_RENDER;

    /**
     * Dynamic tags manager constructor.
     *
     * Initializing Goomento dynamic tags manager.
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
     * @param string $text Dynamic tag text.
     * @param array $settings The dynamic tag settings.
     * @param callable $parseCallback The functions that renders the dynamic tag.
     *
     * @return string|string[]|mixed A single string or an array of strings with
     *                               the return values from each tag callback
     *                               function.
     */
    public function parseTagsText(string $text, array $settings, callable $parseCallback)
    {
        if (!empty($settings['returnType']) && 'object' === $settings['returnType']) {
            $value = $this->parseTagText($text, $settings, $parseCallback);
        } else {
            $value = preg_replace_callback('/\[' . self::TAG_LABEL . '.+?(?=\])\]/', function ($tagTextMatch) use ($settings, $parseCallback) {
                return $this->parseTagText($tagTextMatch[0], $settings, $parseCallback);
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
     * @param string $tagText Dynamic tag text.
     * @param array $settings The dynamic tag settings.
     * @param callable $parseCallback The functions that renders the dynamic tag.
     *
     * @return string|array|mixed If the tag was not found an empty string or an
     *                            empty array will be returned, otherwise the
     *                            return value from the tag callback function.
     */
    public function parseTagText(string $tagText, array $settings, callable $parseCallback)
    {
        $tagData = $this->tagTextToTagData($tagText);

        if (!$tagData) {
            if (!empty($settings['returnType']) && 'object' === $settings['returnType']) {
                return [];
            }

            return '';
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        return call_user_func_array($parseCallback, $tagData);
    }

    /**
     *
     * @param string $tagText
     *
     * @return array|null
     */
    public function tagTextToTagData(string $tagText) :?array
    {
        preg_match('/id="(.*?(?="))"/', $tagText, $tagIdMatch);
        preg_match('/name="(.*?(?="))"/', $tagText, $tagNameMatch);
        preg_match('/settings="(.*?(?="]))/', $tagText, $tagSettingsMatch);

        if (!$tagIdMatch || ! $tagNameMatch || ! $tagSettingsMatch) {
            return null;
        }

        return [
            'id' => $tagIdMatch[1],
            'name' => $tagNameMatch[1],
            'settings' => DataHelper::decode(urldecode($tagSettingsMatch[1])),
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
        return sprintf('[%1$s id="%2$s" name="%3$s" settings="%4$s"]', self::TAG_LABEL, $tag->getId(), $tag->getName(), urlencode(DataHelper::encode($tag->getSettings())));
    }

    /**
     * @param string $tagId
     * @param string $tagName
     * @param array  $settings
     *
     * @return string
     */
    public function tagDataToTagText($tagId, $tagName, array $settings = [])
    {
        $tag = $this->createTag($tagId, $tagName, $settings);

        if (!$tag) {
            return '';
        }

        return $this->tagToText($tag);
    }

    /**
     * @param string $id
     * @param string $name
     * @param array  $settings
     * @return AbstractTag|null
     */
    public function createTag($id, $name, array $settings = [])
    {
        $component = $this->getTag($name);

        if (!$component) {
            return null;
        }

        $tagClass = get_class($component);

        return ObjectManagerHelper::create($tagClass, [
            'data' => [
                'settings' => $settings,
                'id' => $id,
            ]
        ]);
    }

    /**
     *
     * @param       $id
     * @param       $name
     * @param array $settings
     *
     * @return null|string
     */
    public function getTagDataContent($id, $name, array $settings = [])
    {
        if (self::MODE_REMOVE === $this->parsingMode) {
            return null;
        }

        $tag = $this->createTag($id, $name, $settings);

        if (!$tag) {
            return null;
        }

        return $tag->getContent();
    }

    /**
     *
     * @param $tagName
     *
     * @return AbstractTag|null
     */
    public function getTag($tagName)
    {
        return $this->getComponent($tagName);
    }

    /**
     * @return AbstractTag[]
     */
    public function getTags()
    {
        if (! HooksHelper::didAction('goomento/dynamic_tags/register_tags')) {
            /**
             * Register dynamic tags.
             *
             * Fires when Goomento registers dynamic tags.
             *
             *
             * @param Tags $this Dynamic tags manager.
             */
            HooksHelper::doAction('pagebuilder/dynamic_tags/register_tags', $this);
        }

        return $this->getComponents();
    }

    /**
     *
     * @param string|AbstractTag $tag
     */
    public function registerTag($tag)
    {
        $this->setComponent($tag->getName(), $tag);
    }

    /**
     *
     * @param string $tagName
     */
    public function unregisterTag($tagName)
    {
        $this->removeComponent($tagName);
    }

    /**
     *
     * @param       $groupName
     * @param array $groupSettings
     */
    public function registerGroup($groupName, array $groupSettings)
    {
        $defaultGroupSettings = [
            'title' => '',
        ];

        $groupSettings = array_merge($defaultGroupSettings, $groupSettings);

        $this->tagsGroups[ $groupName ] = $groupSettings;
    }


    /**
     * @return void
     */
    public function printTemplates()
    {
        foreach ($this->getComponents() as $tag) {

            if (!$tag instanceof AbstractTag) {
                continue;
            }

            $tag->printTemplate();
        }
    }

    /**
     * @return array
     */
    public function getTagsConfig()
    {
        $config = [];

        foreach ($this->getTags() as $tag) {
            $config[ $tag->getName() ] = $tag->getEditorConfig();
        }

        return $config;
    }


    public function getConfig()
    {
        return [
            'tags' => $this->getTagsConfig(),
            'groups' => $this->tagsGroups,
        ];
    }

    /**
     *
     * @throws Exception If content ID is missing.
     * @throws Exception If current user don't have permissions to edit the post.
     */
    public function ajaxRenderTags($data)
    {
        /**
         * Before dynamic tags rendered.
         *
         * Fires before Goomento renders the dynamic tags.
         *
         */
        HooksHelper::doAction('pagebuilder/dynamic_tags/before_render');

        $tagData = [];

        foreach ($data['tags'] as $tagKey) {
            $tagKeyParts = explode('-', $tagKey);

            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $tagName = base64_decode($tagKeyParts[0]);

            $tagSettings = DataHelper::decode(urldecode(base64_decode($tagKeyParts[1])));

            $tag = $this->createTag(null, $tagName, $tagSettings);

            $tagData[ $tagKey ] = $tag->getContent();
        }

        /**
         * After dynamic tags rendered.
         *
         * Fires after Goomento renders the dynamic tags.
         *
         */
        HooksHelper::doAction('pagebuilder/dynamic_tags/after_render');

        return $tagData;
    }

    /**
     *
     * @param $mode
     */
    public function setParsingMode($mode)
    {
        $this->parsingMode = $mode;
    }


    public function getParsingMode()
    {
        return $this->parsingMode;
    }

    /**
     * @param Ajax $ajax
     * @return void
     * @throws Exception
     */
    public function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('render_tags', [ $this, 'ajaxRenderTags' ]);
    }

    /**
     * Register tags.
     *
     * Add all the available dynamic tags.
     *
     *
     */
    public function registerTags()
    {
        $this->setComponent([
            DataConfig::NAME => DataConfig::class,
            Urls::NAME => Urls::class,
            Images::NAME => Images::class,
            CmsBlocks::NAME => CmsBlocks::class,
            CmsPages::NAME => CmsPages::class,
            PageBuilderContents::NAME => PageBuilderContents::class,
        ]);
    }

    /**
     * Get groups.
     *
     * Retrieve the dynamic tag groups.
     *
     *
     * @return array Tag dynamic tag groups.
     */
    public function getGroups()
    {
        $groups = [
            self::BASE_GROUP => [
                'title' => 'Base Tags',
            ],
            self::URL_CATEGORY => [
                'title' => __('Url'),
            ],
            self::TEXT_CATEGORY => [
                'title' => __('Text'),
            ],
            self::WYSIWYG_CATEGORY => [
                'title' => __('WYSIWYG'),
            ],
            self::IMAGE_CATEGORY => [
                'title' => __('Image'),
            ],
        ];

        foreach ($groups as $name => $group) {
            $this->registerGroup($name, $group);
        }

        return $this->tagsGroups;
    }

    /**
     * @return void
     */
    private function addActions()
    {
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);

        $this->registerTags();
        $this->getGroups();
    }
}
