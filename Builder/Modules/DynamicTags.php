<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Builder\Base\AbstractModule as BaseModule;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class DynamicTags extends BaseModule
{
    /**
     * Base dynamic tag group.
     */
    const BASE_GROUP = 'base';

    /**
     * Dynamic tags text category.
     */
    const TEXT_CATEGORY = 'text';

    /**
     * Dynamic tags URL category.
     */
    const URL_CATEGORY = 'url';

    /**
     * Dynamic tags image category.
     */
    const IMAGE_CATEGORY = 'image';

    /**
     * Dynamic tags media category.
     */
    const MEDIA_CATEGORY = 'media';

    /**
     * Dynamic tags post meta category.
     */
    const POST_META_CATEGORY = 'post_meta';

    /**
     * Dynamic tags gallery category.
     */
    const GALLERY_CATEGORY = 'gallery';

    /**
     * Dynamic tags number category.
     */
    const NUMBER_CATEGORY = 'number';

    /**
     * Dynamic tags module constructor.
     *
     * Initializing SagoTheme dynamic tags module.
     *
     */
    public function __construct()
    {
        $this->registerGroups();
        HooksHelper::addAction('pagebuilder/dynamic_tags/register_tags', [ $this,'registerTags' ]);
    }

    /**
     * Get module name.
     *
     * Retrieve the dynamic tags module name.
     *
     *
     * @return string Module name.
     */
    public function getName()
    {
        return 'dynamic_tags';
    }

    /**
     * Get classes names.
     *
     * Retrieve the dynamic tag classes names.
     *
     *
     * @return array Tag dynamic tag classes names.
     */
    public function getTagClassesNames()
    {
        return [
        ];
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
        return [
            self::BASE_GROUP => [
                'title' => 'Base Tags',
            ],
            self::URL_CATEGORY => [
                'title' => __('Url'),
            ],
            self::TEXT_CATEGORY => [
                'title' => __('Text'),
            ],
        ];
    }

    /**
     * Register groups.
     *
     * Add all the available tag groups.
     *
     */
    private function registerGroups()
    {
        foreach ($this->getGroups() as $group_name => $group_settings) {
            ObjectManagerHelper::get(Tags::class)->registerGroup($group_name, $group_settings);
        }
    }

    /**
     * Register tags.
     *
     * Add all the available dynamic tags.
     *
     *
     * @param Tags $dynamic_tags
     */
    public function registerTags(Tags $dynamic_tags)
    {
        foreach ($this->getTagClassesNames() as $tag_class) {
            $dynamic_tags->registerTag($tag_class);
        }
    }
}
