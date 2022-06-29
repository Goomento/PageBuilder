<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Css;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;

class ContentCss extends AbstractCss
{
    const META_KEY = 'css';

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
     * @var BuildableContentInterface
     */
    private $model;

    /**
     * ContentCss CSS file constructor.
     *
     * Initializing the CSS file of the post. Set the content ID and initiate the stylesheet.
     *
     *
     * @param BuildableContentInterface $content
     */
    public function __construct(BuildableContentInterface $content)
    {
        $this->model = $content;

        $parts = [
            'pagebuilder-',
            $content->getStatus(),
            '-',
            $content->getId(),
            '.css'
        ];

        parent::__construct(implode('', $parts));
    }

    /**
     * @return BuildableContentInterface
     */
    public function getModel()
    {
        return $this->model;
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
        return '.gmt-' . $this->getModel()->getStatus() . '-' . $this->getModel()->getId() . ' .gmt-element' . $element->getUniqueSelector();
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
        return $this->model->getSetting(static::META_KEY);
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
        $this->model->setSetting(static::META_KEY, $meta);
        ContentHelper::save( $this->model );
    }

    /**
     * Delete meta.
     *
     * Delete the file meta data.
     *
     */
    protected function deleteMeta()
    {
        $this->model->deleteSetting(static::META_KEY);
        ContentHelper::save( $this->model );
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
        return $this->model->getElements();
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
        $elementManager = ObjectManagerHelper::getElementsManager();
        if (!empty($data)) {
            foreach ($data as $elementData) {
                $element = $elementManager->createElementInstance((array) $elementData);
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
     * @param ControlsStack $controlsStack The controls stack.
     * @param array $controls Controls array.
     * @param array $values Values array.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     * @param array|null $allControls All controls.
     */
    public function addControlsStackStyleRules(
        ControlsStack $controlsStack,
        array         $controls,
        array         $values,
        array         $placeholders,
        array         $replacements,
        array         $allControls = null
    )
    {
        parent::addControlsStackStyleRules($controlsStack, $controls, $values, $placeholders, $replacements, $allControls);

        if ($controlsStack instanceof AbstractElement) {
            foreach ($controlsStack->getChildren() as $childElement) {
                $this->renderStyles($childElement);
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
        return 'gmt-' . $this->getModel()->getStatus()   . '-' . $this->getModel()->getId();
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

    /**
     * Use external file.
     *
     * Whether to use external CSS file of not. When there are new schemes or settings
     * updates.
     *
     *
     * @return bool True if the CSS requires an update, False otherwise.
     */
    protected function useExternalFile()
    {
        return !DataHelper::useInlineCss();
    }
}
