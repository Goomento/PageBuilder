<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\Content;
use Goomento\PageBuilder\Model\ContentManagement;
use Goomento\PageBuilder\Model\ContentRegistry;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ContentGetter
 * @package Goomento\PageBuilder\Helper
 */
class StaticContent
{
    use TraitStaticInstances;

    /**
     * @param $id
     * @return ContentInterface|Content|null
     */
    public static function get($id)
    {
        /** @var ContentRegistry $instance */
        $instance = self::getInstance(ContentRegistry::class);
        return $instance->getById((int) $id);
    }

    /**
     * @param array $data
     * @return ContentInterface
     * @throws LocalizedException
     * @deplacated
     */
    public static function create(array $data)
    {
        /** @var ContentManagement $instance */
        $instance = self::getInstance(ContentManagement::class);
        $content = $instance->createContent($data);
        if ($content && $content->getId()) {
            return self::get(
                $content->getId()
            );
        }
        return $content;
    }

    /**
     * @param $id
     * @return bool
     * @throws LocalizedException
     */
    public static function delete($id)
    {
        /** @var ContentRegistry $instance */
        $instance = self::getInstance(ContentRegistry::class);
        return $instance->delete($id);
    }
}
