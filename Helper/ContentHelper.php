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
use Goomento\PageBuilder\Builder\Elements\Column;
use Goomento\PageBuilder\Builder\Elements\Section;
use Goomento\PageBuilder\Builder\Widgets\TextEditor;
use Goomento\PageBuilder\Model\Content as ContentModel;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @see \Goomento\PageBuilder\Helper\Content
 * @method static RevisionInterface[] getRevisionsByContent(ContentInterface $content, ?array $statuses = null, ?int $limit = 200, ?int $currentPage = 1)
 * @see \Goomento\PageBuilder\Helper\Content::getRevisionsByContent()
 * @method static RevisionInterface|null getLastRevisionByContent(ContentInterface $content)
 * @see \Goomento\PageBuilder\Helper\Content::getLastRevisionByContent()
 * @method static RevisionInterface getRevision($revisionId)
 * @see \Goomento\PageBuilder\Helper\Content::getRevision()
 * @method static ContentInterface|null get($contentId)
 * @see \Goomento\PageBuilder\Helper\Content::get()
 * @method static ContentInterface create(array $data)
 * @see \Goomento\PageBuilder\Helper\Content::create()
 * @method static BuildableContentInterface save(BuildableContentInterface $content, bool $createRevision = true)
 * @see \Goomento\PageBuilder\Helper\Content::save()
 * @method static void delete($id)
 * @see \Goomento\PageBuilder\Helper\Content::delete()
 * @method static null|RevisionInterface saveAsRevision(ContentInterface $content, string $status = BuildableContentInterface::STATUS_REVISION)
 * @see \Goomento\PageBuilder\Helper\Content::saveAsRevision()
 * @method static null|RevisionInterface saveRevision(RevisionInterface $content)
 * @see \Goomento\PageBuilder\Helper\Content::saveRevision()
 */
class ContentHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @inheritDoc
     */
    static protected function getStaticInstance()
    {
        return Content::class;
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
     * Create Content with HTML
     *
     * @param string $html
     * @param array $data
     * @return ContentInterface
     */
    public static function createContentWithHtml(string $html, array $data = []) : ContentInterface
    {
        $data['elements'] = [[
            'id' => EncryptorHelper::uniqueString(7),
            'isInner' => false,
            'elType' => Section::NAME,
            'settings' => [],
            'elements' => [[
                'id' => EncryptorHelper::uniqueString(7),
                'isInner' => false,
                'elType' => Column::NAME,
                'settings' => [
                    '_column_size' => 100
                ],
                'elements' => [[
                    'id' => EncryptorHelper::uniqueString(7),
                    'isInner' => false,
                    'elType' => TextEditor::TYPE,
                    'widgetType' => TextEditor::NAME,
                    'elements' => [],
                    'settings' => [
                        TextEditor::NAME . '_editor' => /** @noEscape */ $html
                    ],
                ]]
            ]],
        ]];

        return self::create($data);
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
