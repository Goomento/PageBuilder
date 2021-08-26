<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\DynamicTags;

use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Base\Element;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Files\Css\ContentCss;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class DynamicCss
 * @package Goomento\PageBuilder\Core\DynamicTags
 */
class DynamicCss extends ContentCss
{
    protected $postIdForData;
    /**
     * Dynamic_CSS constructor.
     *
     * @param int $contentId ContentCss ID
     * @param int $contentIdForData
     */
    public function __construct($contentId, $contentIdForData)
    {
        $this->postIdForData = $contentIdForData;

        parent::__construct($contentId);
    }


    public function getName()
    {
        return 'dynamic';
    }


    protected function useExternalFile()
    {
        return false;
    }


    protected function getFileHandleId()
    {
        return 'goomento-post-dynamic-' . $this->postIdForData;
    }


    protected function getData()
    {
        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);
        $document = $documentManager->get($this->postIdForData);
        return $document ? $document->getElementsData() : [];
    }


    public function getMeta($property = null)
    {
        // Parse CSS first, to get the fonts list.
        $css = $this->getContent();

        $meta = [
            'status' => $css ? self::CSS_STATUS_INLINE : self::CSS_STATUS_EMPTY,
            'fonts' => $this->getFonts(),
            'css' => $css,
        ];

        if ($property) {
            return $meta[$property] ?? null;
        }

        return $meta;
    }

    /**
     * @param ControlsStack $controls_stack
     * @param array $controls
     * @param array $values
     * @param array $placeholders
     * @param array $replacements
     * @param array|null $all_controls
     */
    public function addControlsStackStyleRules(ControlsStack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, array $all_controls = null)
    {
        $dynamic_settings = $controls_stack->getSettings('__dynamic__');
        if (! empty($dynamic_settings)) {
            $controls = array_intersect_key($controls, $dynamic_settings);

            $all_controls = $controls_stack->getControls();

            $parsed_dynamic_settings = $controls_stack->parseDynamicSettings($values, $controls);

            foreach ($controls as $control) {
                if (! empty($control['style_fields'])) {
                    $this->addRepeaterControlStyleRules($controls_stack, $control, $values[ $control['name'] ], $placeholders, $replacements);
                }

                if (empty($control['selectors'])) {
                    continue;
                }

                $this->addControlStyleRules($control, $parsed_dynamic_settings, $all_controls, $placeholders, $replacements);
            }
        }

        if ($controls_stack instanceof Element) {
            foreach ($controls_stack->getChildren() as $child_element) {
                $this->renderStyles($child_element);
            }
        }
    }
}
