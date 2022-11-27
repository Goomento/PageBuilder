<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Model\Content as ContentModel;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @see \Goomento\PageBuilder\Helper\BuildableContent
 * @method static RevisionInterface[] getRevisionsByContent(ContentInterface $content, ?array $statuses = null, ?int $limit = 12, ?int $currentPage = 1)
 * @see \Goomento\PageBuilder\Helper\BuildableContent::getRevisionsByContent()
 * @method static RevisionInterface getRevision($revisionId)
 * @see \Goomento\PageBuilder\Helper\BuildableContent::getRevision()
 * @method static BuildableContentInterface[] getBuildableTemplates(?int $limit = 12, ?int $currentPage = 1)
 * @see \Goomento\PageBuilder\Helper\BuildableContent::getBuildableTemplates()
 * @method static ContentInterface|null getContent($contentId)
 * @see \Goomento\PageBuilder\Helper\BuildableContent::getContent()
 * @method static ContentInterface createContent(array $data)
 * @see \Goomento\PageBuilder\Helper\BuildableContent::createContent()
 * @method static BuildableContentInterface saveBuildableContent(BuildableContentInterface $content, string $saveMassage = '')
 * @see \Goomento\PageBuilder\Helper\BuildableContent::saveBuildableContent()
 * @method static void deleteBuildableContent(BuildableContentInterface $content)
 * @see \Goomento\PageBuilder\Helper\BuildableContent::deleteBuildableContent()
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class BuildableContentHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @inheritDoc
     */
    protected static function getStaticInstance()
    {
        return BuildableContent::class;
    }

    /**
     * @param ContentInterface $content
     * @return string
     */
    public static function getContentLabel(ContentInterface $content) : string
    {
        return ucfirst($content->getType()) . ' - ' . $content->getTitle() . ' ( ID: ' . $content->getId() . ' )';
    }

    /**
     * @param BuildableContentInterface $buildableContent
     * @return bool
     */
    public static function isContentStatus(BuildableContentInterface $buildableContent) : bool
    {
        return isset(ContentModel::getAvailableStatuses()[$buildableContent->getStatus()]);
    }

    /**
     * Iterate data.
     *
     * Accept any type of Goomento data and a callback function. The callback
     * function runs recursively for each element and his child elements.
     *
     *
     * @param array    $dataContainer Any type of Goomento data.
     * @param callable $callback       A function to iterate data by.
     * @param array    $args           Array of args pointers for passing parameters in & out of the callback
     *
     * @return mixed Iterated data.
     */
    public static function iterateData(array $dataContainer, callable $callback, array $args = [])
    {
        if (isset($dataContainer['elType'])) {
            if (!empty($dataContainer['elements'])) {
                $dataContainer['elements'] = self::iterateData($dataContainer['elements'], $callback, $args);
            }

            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            return call_user_func($callback, $dataContainer, $args);
        }

        foreach ($dataContainer as $elementKey => $elementValue) {
            $elementData = self::iterateData($elementValue, $callback, $args);

            if (null === $elementData) {
                continue;
            }

            $dataContainer[ $elementKey ] = $elementData;
        }

        return $dataContainer;
    }
}
