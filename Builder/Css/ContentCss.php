<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Css;

use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class ContentCss extends AbstractCss
{
    /**
     * SagoTheme post CSS file prefix.
     */
    const FILE_PREFIX = 'content-';

    const META_KEY = 'css';

    /**
     * ContentCss ID.
     *
     * Holds the current content ID.
     *
     * @var int
     */
    private $contentId;

    /**
     * Get CSS file name.
     *
     * Retrieve the CSS file name.
     *
     *
     * @return string CSS file name.
     */
    const NAME = 'content';

    /**
     * ContentCss CSS file constructor.
     *
     * Initializing the CSS file of the post. Set the content ID and initiate the stylesheet.
     *
     *
     * @param mixed $contentId ContentCss ID.
     */
    public function __construct($contentId)
    {
        $this->contentId = $contentId;

        parent::__construct(self::FILE_PREFIX . $contentId . '.css');
    }

    /**
     * Get content ID.
     *
     * Retrieve the ID of current content.
     *
     *
     * @return int ContentCss ID.
     */
    public function getContentId()
    {
        return (int) $this->contentId;
    }

    /**
     * Get unique element selector.
     *
     * Retrieve the unique selector for any given element.
     *
     *
     * @param AbstractElement $element The element.
     *
     * @return string Unique element selector.
     */
    public function getElementUniqueSelector(AbstractElement $element)
    {
        return '.gmt-' . $this->contentId . ' .gmt-element' . $element->getUniqueSelector();
    }

    /**
     * Load meta data.
     *
     * Retrieve the post CSS file meta data.
     *
     *
     * @return array ContentCss CSS file meta data.
     */
    protected function loadMeta()
    {
        return ContentHelper::get($this->contentId)->getSetting(static::META_KEY);
    }

    /**
     * Update meta data.
     *
     * Update the global CSS file meta data.
     *
     *
     * @param array $meta New meta data.
     * @return void
     * @throws \Exception
     */
    protected function updateMeta($meta)
    {
        $content = ContentHelper::get($this->contentId);
        $content->setSetting(static::META_KEY, $meta);
        ContentHelper::save($content, false);
    }

    /**
     * Delete meta.
     *
     * Delete the file meta data.
     *
     */
    protected function deleteMeta()
    {
        $content = ContentHelper::get($this->contentId);
        $content->deleteSetting(static::META_KEY);
        ContentHelper::save($content, false);
    }

    /**
     * Get post data.
     *
     * Retrieve raw post data from the database.
     *
     *
     * @return array ContentCss data.
     */
    protected function getData()
    {
        return ContentHelper::get($this->contentId)->getElements();
    }

    /**
     * Render CSS.
     *
     * Parse the CSS for all the elements.
     *
     */
    protected function renderCss()
    {
        $data = $this->getData();

        if (!empty($data)) {
            foreach ($data as $element_data) {
                $element = ObjectManagerHelper::getElementsManager()
                    ->createElementInstance((array) $element_data);

                if (!$element) {
                    continue;
                }

                $this->renderStyles($element);
            }
        }
    }

    /**
     * Enqueue CSS.
     *
     * Enqueue the post CSS file in Goomento.
     *
     */
    public function enqueue()
    {
        parent::enqueue();
    }

    /**
     * Add controls-stack style rules.
     *
     * Parse the CSS for all the elements inside any given controls stack.
     *
     * This method recursively renders the CSS for all the child elements in the stack.
     *
     *
     * @param ControlsStack $controls_stack The controls stack.
     * @param array $controls Controls array.
     * @param array $values Values array.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     * @param array|null $all_controls All controls.
     */
    public function addControlsStackStyleRules(ControlsStack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, array $all_controls = null)
    {
        parent::addControlsStackStyleRules($controls_stack, $controls, $values, $placeholders, $replacements, $all_controls);

        if ($controls_stack instanceof AbstractElement) {
            foreach ($controls_stack->getChildren() as $child_element) {
                $this->renderStyles($child_element);
            }
        }
    }

    /**
     * Get enqueue dependencies.
     *
     * Retrieve the name of the stylesheet used by `\Goomento\PageBuilder\Helper\Theme::enqueueStyle()`.
     *
     *
     * @return array Name of the stylesheet.
     */
    protected function getEnqueueDependencies()
    {
        return [ 'goomento-frontend' ];
    }

    /**
     * Get inline dependency.
     *
     *
     *
     * @return string Name of the stylesheet.
     */
    protected function getInlineDependency()
    {
        return 'goomento-frontend';
    }

    /**
     * Get file handle ID.
     *
     * Retrieve the handle ID for the post CSS file.
     *
     *
     * @return string CSS file handle ID.
     */
    protected function getFileHandleId()
    {
        return 'goomento-content-' . $this->contentId;
    }

    /**
     * Render styles.
     *
     * Parse the CSS for any given element.
     *
     *
     * @param AbstractElement $element The element.
     */
    protected function renderStyles(AbstractElement $element)
    {
        /**
         * Before element parse CSS.
         *
         * Fires before the CSS of the element is parsed.
         *
         *
         * @param ContentCss         $this    The post CSS file.
         * @param AbstractElement $element The element.
         */
        HooksHelper::doAction('pagebuilder/element/before_parse_css', $this, $element);

        $element_settings = $element->getSettings();

        $this->addControlsStackStyleRules($element, $element->getStyleControls(null, $element->getParsedDynamicSettings()), $element_settings, [ '{{ID}}', '{{WRAPPER}}' ], [ $element->getId(), $this->getElementUniqueSelector($element) ]);

        /**
         * After element parse CSS.
         *
         * Fires after the CSS of the element is parsed.
         *
         *
         * @param ContentCss         $this    The post CSS file.
         * @param AbstractElement $element The element.
         */
        HooksHelper::doAction('pagebuilder/element/parse_css', $this, $element);
    }
}
