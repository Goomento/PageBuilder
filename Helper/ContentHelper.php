<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\Data\RevisionSearchResultsInterface;
use Goomento\PageBuilder\Builder\Elements\Column;
use Goomento\PageBuilder\Builder\Elements\Section;
use Goomento\PageBuilder\Builder\Widgets\TextEditor;

/**
 * @see \Goomento\PageBuilder\Helper\Content
 * @method static RevisionSearchResultsInterface getRevisionsByContent($contentId, $statuses = null, ?int $limit = null)
 * @method static RevisionInterface getRevision($revisionId)
 * @method static ContentInterface|null get($contentId)
 * @method static ContentInterface create(array $data)
 * @method static void save(ContentInterface $content, bool $createRevision = true)
 * @method static void delete($id)
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
     * @param array    $data_container Any type of Goomento data.
     * @param callable $callback       A function to iterate data by.
     * @param array    $args           Array of args pointers for passing parameters in & out of the callback
     *
     * @return mixed Iterated data.
     */
    public static function iterateData($data_container, $callback, $args = [])
    {
        if (isset($data_container['elType'])) {
            if (!empty($data_container['elements'])) {
                $data_container['elements'] = self::iterateData($data_container['elements'], $callback, $args);
            }

            return call_user_func($callback, $data_container, $args);
        }

        foreach ($data_container as $element_key => $element_value) {
            $element_data = self::iterateData($element_value, $callback, $args);

            if (null === $element_data) {
                continue;
            }

            $data_container[ $element_key ] = $element_data;
        }

        return $data_container;
    }
}
