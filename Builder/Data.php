<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\DynamicTags\Manager;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Data
 * @package Goomento\PageBuilder\Builder
 */
class Data
{

    /**
     * Current DB version of the editor.
     */
    const DB_VERSION = '1.0';

    /**
     * ContentCss publish status.
     */
    const STATUS_PUBLISH = 'publish';

    /**
     * ContentCss draft status.
     */
    const STATUS_DRAFT = 'draft';

    /**
     * ContentCss private status.
     */
    const STATUS_PRIVATE = 'private';

    /**
     * ContentCss autosave status.
     */
    const STATUS_AUTOSAVE = 'autosave';

    /**
     * ContentCss pending status.
     */
    const STATUS_PENDING = 'pending';

    /**
     * Switched post data.
     *
     * Holds the switched post data.
     *
     * @var array Switched post data. Default is an empty array.
     */
    protected $switched_post_data = [];

    /**
     * Switched data.
     *
     * Holds the switched data.
     *
     * @var array Switched data. Default is an empty array.
     */
    protected $switched_data = [];

    /**
     * Get builder.
     *
     * Retrieve editor data from the database.
     *
     *
     *
     * @param int     $post_id           ContentCss ID.
     * @param string  $status            Optional. ContentCss status. Default is `publish`.
     *
     * @return array Editor data.
     */
    public function getBuilder($post_id, $status = self::STATUS_PUBLISH)
    {
        $document = StaticObjectManager::get(DocumentsManager::class)->get($post_id);
        if ($document) {
            $editor_data = $document->getElementsRawData(null, true);
        } else {
            $editor_data = [];
        }

        return $editor_data;
    }

    /**
     * Render element plain content.
     *
     * When saving data in the editor, this method renders recursively the plain
     * content containing only the content and the HTML. No CSS data.
     *
     * @param array $element_data Element data.
     */
    private function renderElementPlainContent($element_data)
    {
        if ('widget' === $element_data['elType']) {
            /** @var Widget $widget */
            /** @var Elements $elementsManager */
            $elementsManager = StaticObjectManager::get(Elements::class);
            $widget = $elementsManager->createElementInstance($element_data);

            if ($widget) {
                $widget->renderPlainContent();
            }
        }

        if (! empty($element_data['elements'])) {
            foreach ($element_data['elements'] as $element) {
                $this->renderElementPlainContent($element);
            }
        }
    }

    /**
     * Save plain text.
     *
     * Retrieves the raw content, removes all kind of unwanted HTML tags and saves
     * the content as the `post_content` field in the database.
     *
     *
     * @param int $content_id ContentCss ID.
     */
    public function savePlainText($content_id)
    {
        // Switch $dynamic_tags to parsing mode = remove.
        $dynamic_tags = StaticObjectManager::get(\Goomento\PageBuilder\Core\DynamicTags\Manager::class);
        $parsing_mode = $dynamic_tags->getParsingMode();
        $dynamic_tags->setParsingMode(Manager::MODE_REMOVE);

        $plain_text = $this->getPlainText($content_id);

        StaticContent::get($content_id)->setContent($plain_text);

        // Restore parsing mode.
        $dynamic_tags->setParsingMode($parsing_mode);
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
    public function iterateData($data_container, $callback, $args = [])
    {
        if (isset($data_container['elType'])) {
            if (! empty($data_container['elements'])) {
                $data_container['elements'] = $this->iterateData($data_container['elements'], $callback, $args);
            }

            return call_user_func($callback, $data_container, $args);
        }

        foreach ($data_container as $element_key => $element_value) {
            $element_data = $this->iterateData($element_value, $callback, $args);

            if (null === $element_data) {
                continue;
            }

            $data_container[ $element_key ] = $element_data;
        }

        return $data_container;
    }

    /**
     * Get plain text.
     *
     * Retrieve the post plain text.
     *
     *
     * @param int $post_id ContentCss ID.
     *
     * @return string ContentCss plain text.
     */
    public function getPlainText($post_id)
    {
        $document = StaticObjectManager::get(DocumentsManager::class)->get($post_id);
        $data = $document ? $document->getElementsData() : [];

        return $this->getPlainTextFromData($data);
    }

    /**
     * Get plain text from data.
     *
     * Retrieve the post plain text from any given SagoTheme data.
     *
     *
     * @param array $data ContentCss ID.
     *
     * @return string ContentCss plain text.
     */
    public function getPlainTextFromData($data)
    {
        ob_start();
        if ($data) {
            foreach ($data as $element_data) {
                $this->renderElementPlainContent($element_data);
            }
        }

        $plain_text = ob_get_clean();

        // Remove unnecessary tags.
        $plain_text = preg_replace('/<\/?div[^>]*\>/i', '', $plain_text);
        $plain_text = preg_replace('/<\/?span[^>]*\>/i', '', $plain_text);
        $plain_text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $plain_text);
        $plain_text = preg_replace('/<i [^>]*><\\/i[^>]*>/', '', $plain_text);
        $plain_text = preg_replace('/ class=".*?"/', '', $plain_text);

        // Remove empty lines.
        $plain_text = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $plain_text);

        return trim($plain_text);
    }
}
