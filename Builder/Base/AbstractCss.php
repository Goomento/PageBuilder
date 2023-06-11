<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Builder\Modules\Stylesheet;
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

abstract class AbstractCss extends AbstractFile
{

    /**
     * Goomento CSS file generated status.
     *
     * The parsing result after generating CSS file.
     */
    const CSS_STATUS_FILE = 'file';

    /**
     * Goomento inline CSS status.
     *
     * The parsing result after generating inline CSS.
     */
    const CSS_STATUS_INLINE = 'inline';

    /**
     * Goomento CSS empty status.
     *
     * The parsing result when an empty CSS returned.
     */
    const CSS_STATUS_EMPTY = 'empty';

    /**
     * Fonts.
     *
     * Holds the list of fonts.
     *
     *
     * @var array
     */
    protected $fonts = [];

    /**
     * Stylesheet object.
     *
     * Holds the CSS file stylesheet instance.
     *
     *
     * @var Stylesheet
     */
    protected $stylesheetObj;

    /**
     * Printed.
     *
     * Holds the list of printed files.
     *
     *
     * @var array
     */
    private static $printed = [];

    /**
     * CSS file constructor.
     *
     * Initializing Goomento CSS file.
     *
     */
    public function __construct($fileName)
    {
        parent::__construct($fileName);

        $this->initStylesheet();
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
        return true;
    }

    /**
     * Update the CSS file.
     *
     * Delete old CSS, parse the CSS, save the new file and update the database.
     *
     * This method also sets the CSS status to be used later on in the render posses.
     *
     */
    public function update()
    {
        $this->updateFile();

        $meta = $this->getMeta();

        $meta['css_updated_time'] = time();

        $content = $this->getContent();
        $meta['fonts'] = $this->getFonts();

        if (empty($content)) {
            $meta['status'] = self::CSS_STATUS_EMPTY;
            $meta['css'] = '';
        } else {
            $useExternalFile = $this->useExternalFile();

            if ($useExternalFile) {
                $meta['status'] = self::CSS_STATUS_FILE;
                $meta['css'] = '';
            } else {
                $meta['status'] = self::CSS_STATUS_INLINE;
                $meta['css'] = $content;
            }
        }

        $this->updateMeta($meta);
    }

    public function write()
    {
        if ($this->useExternalFile()) {
            parent::write();
        }
    }

    /**
     * Enqueue CSS.
     *
     * Either enqueue the CSS file in Goomento or add inline style.
     *
     * This method is also responsible for loading the fonts.
     *
     */
    public function enqueue()
    {
        $handleId = $this->getFileHandleId();

        if (isset(self::$printed[ $handleId ])) {
            return;
        }

        self::$printed[ $handleId ] = true;

        $meta = $this->getMeta();

        if (self::CSS_STATUS_EMPTY === $meta['status']) {
            return;
        }

        // First time after clear cache and etc.
        if ('' === $meta['status'] || $this->isUpdateRequired()) {
            $this->update();

            $meta = $this->getMeta();
        }

        if (!$this->useExternalFile()) {
            $dep = $this->getInlineDependency();
            if (ThemeHelper::styleIs($dep, 'done')) {
                // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
                echo sprintf('<style id="%s">%s</style>', $this->getFileHandleId(), $meta['css']);
            } else {
                ThemeHelper::inlineStyle($dep, $meta['css']);
            }
        } else { // Re-check if it's not empty after CSS update.
            ThemeHelper::registerStyle(
                $this->getFileHandleId(),
                $this->getUrl(),
                $this->getEnqueueDependencies(),
                $this->getMeta('css_updated_time')
            );
            ThemeHelper::enqueueStyle($this->getFileHandleId());
        }

        $frontend = ObjectManagerHelper::getFrontend();
        // Handle fonts.
        if (!empty($meta['fonts'])) {
            foreach ($meta['fonts'] as $font) {
                $frontend->enqueueFont($font);
            }
        }

        foreach ($this->getFonts() as $defaultFont) {
            $frontend->enqueueFont($defaultFont);
        }

        $name = $this->getName();

        /**
         * Enqueue CSS file.
         *
         * Fires when CSS file is enqueued on Goomento.
         *
         * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
         *
         *
         * @param AbstractCss $this The current CSS file.
         */
        HooksHelper::doAction("pagebuilder/css-file/{$name}/enqueue", $this);
    }

    /**
     * Print CSS.
     *
     * Output the final CSS inside the `<style>` tags and all the frontend fonts in
     * use.
     *
     */
    public function printCss()
    {
        $frontend = ObjectManagerHelper::getFrontend();
        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo '<style>' . $this->getContent() . '</style>'; // XSS ok.
        $frontend->printFontsLinks();
    }

    /**
     * Add control rules.
     *
     * Parse the CSS for all the elements inside any given control.
     *
     * This method recursively renders the CSS for all the selectors in the control.
     *
     *
     * @param array    $control        The controls.
     * @param array    $controlsStack The controls stack.
     * @param callable $valueCallback Callback function for the value.
     * @param array    $placeholders   Placeholders.
     * @param array    $replacements   Replacements.
     */
    public function addControlRules(array $control, array $controlsStack, callable $valueCallback, array $placeholders, array $replacements)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $value = call_user_func($valueCallback, $control);

        if (null === $value || empty($control['selectors'])) {
            return;
        }

        foreach ($control['selectors'] as $selector => $cssProperty) {
            try {
                $outputCssProperty = preg_replace_callback('/\{\{(?:([^.}]+)\.)?([^}| ]*)(?: *\|\| *(?:([^.}]+)\.)?([^}| ]*) *)*}}/', function ($matches) use ($control, $valueCallback, $controlsStack, $value, $cssProperty) {
                    $externalControlMissing = $matches[1] && ! isset($controlsStack[ $matches[1] ]);

                    $parsedValue = '';

                    if (!$externalControlMissing) {
                        $parsedValue = $this->parsePropertyPlaceholder($control, $value, $controlsStack, $valueCallback, $matches[2], $matches[1]);
                    }

                    if ('' === $parsedValue) {
                        if (isset($matches[4])) {
                            $parsedValue = $matches[4];

                            $isStringValue = preg_match('/^([\'"])(.*)\1$/', $parsedValue, $stringMatches);

                            if ($isStringValue) {
                                $parsedValue = $stringMatches[2];
                            } elseif (! is_numeric($parsedValue)) {
                                if ($matches[3] && ! isset($controlsStack[ $matches[3] ])) {
                                    return '';
                                }

                                $parsedValue = $this->parsePropertyPlaceholder($control, $value, $controlsStack, $valueCallback, $matches[4], $matches[3]);
                            }
                        }

                        if ('' === $parsedValue) {
                            if ($externalControlMissing) {
                                return '';
                            }

                            throw new BuilderException();
                        }
                    }

                    return $parsedValue;
                }, $cssProperty);
            } catch (Exception $e) {
                return;
            }

            if (!$outputCssProperty) {
                continue;
            }

            $devicePattern = '/^(?:\([^\)]+\)){1,2}/';

            preg_match($devicePattern, $selector, $deviceRules);

            $query = [];

            if ($deviceRules) {
                $selector = preg_replace($devicePattern, '', $selector);

                preg_match_all('/\(([^\)]+)\)/', $deviceRules[0], $pureDeviceRules);

                $pureDeviceRules = $pureDeviceRules[1];

                foreach ($pureDeviceRules as $deviceRule) {
                    if (ControlsStack::RESPONSIVE_DESKTOP === $deviceRule) {
                        continue;
                    }

                    $device = preg_replace('/\+$/', '', $deviceRule);

                    $endpoint = $device === $deviceRule ? 'max' : 'min';

                    $query[ $endpoint ] = $device;
                }
            }

            $parsedSelector = str_replace($placeholders, $replacements, $selector);

            if (!$query && ! empty($control['responsive'])) {
                $query = array_intersect_key($control['responsive'], array_flip([ 'min', 'max' ]));

                if (!empty($query['max']) && ControlsStack::RESPONSIVE_DESKTOP === $query['max']) {
                    unset($query['max']);
                }
            }

            $this->stylesheetObj->addRules($parsedSelector, $outputCssProperty, $query);
        }
    }

    /**
     * @param array    $control
     * @param mixed    $value
     * @param array    $controlsStack
     * @param callable $valueCallback
     * @param string   $placeholder
     * @param string   $parserControlName
     *
     * @return string
     */
    public function parsePropertyPlaceholder(array $control, $value, array $controlsStack, $valueCallback, $placeholder, $parserControlName = null)
    {
        if ($parserControlName) {
            $control = $controlsStack[ $parserControlName ];
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $value = call_user_func($valueCallback, $control);
        }

        if (Controls::FONT === $control['type']) {
            $this->setFont($value);
        }

        $controlManager = ObjectManagerHelper::getControlsManager();
        $controlObj = $controlManager->getControl($control['type']);

        return (string) $controlObj->getStyleValue($placeholder, $value, $control);
    }

    /**
     * Get the fonts.
     *
     * Retrieve the list of fonts.
     *
     *
     * @return array Fonts.
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * @param $fontName
     * @return $this
     */
    public function setFont($fontName)
    {
        if (!in_array($fontName, $this->fonts)) {
            $this->fonts[] = $fontName;
        }

        return $this;
    }

    /**
     * Get stylesheet.
     *
     * Retrieve the CSS file stylesheet instance.
     *
     *
     * @return Stylesheet The stylesheet object.
     */
    public function getStylesheet()
    {
        return $this->stylesheetObj;
    }

    /**
     * Add controls stack style rules.
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
    public function addControlsStackStyleRules(ControlsStack $controlsStack, array $controls, array $values, array $placeholders, array $replacements, array $allControls = null)
    {
        if (!$allControls) {
            $allControls = $controlsStack->getControls();
        }

        $parsedDynamicSettings = $controlsStack->parseDynamicSettings($values, $controls);

        foreach ($controls as $control) {
            if (!empty($control['style_fields'])) {
                $this->addRepeaterControlStyleRules($controlsStack, $control, $values[ $control['name'] ], $placeholders, $replacements);
            }

            if (!empty($control[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ])) {
                $this->addDynamicControlStyleRules($control, $control[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ]);
            }

            if (!empty($parsedDynamicSettings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ])) {
                // Dynamic CSS should not be added to the CSS files.
                // Instead it's handled by \Goomento\PageBuilder\Core\DynamicTags\Dynamic_CSS
                // and printed in a style tag.
                unset($parsedDynamicSettings[ $control['name'] ]);
                continue;
            }

            if (empty($control['selectors'])) {
                continue;
            }

            $this->addControlStyleRules($control, $parsedDynamicSettings, $allControls, $placeholders, $replacements);
        }
    }

    /**
     * Get file handle ID.
     *
     * Retrieve the file handle ID.
     *
     * @return string CSS file handle ID.
     */
    abstract protected function getFileHandleId();

    /**
     * Render CSS.
     *
     * Parse the CSS.
     *
     */
    abstract protected function renderCss();

    /**
     * @return array|string[]
     */
    protected function getDefaultMeta()
    {
        return array_merge(parent::getDefaultMeta(), [
            'fonts' => array_unique($this->fonts),
            'status' => '',
        ]);
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
        return [];
    }

    /**
     * Get inline dependency.
     *
     *
     * @return string Name of the stylesheet.
     */
    protected function getInlineDependency()
    {
        return '';
    }

    /**
     * @return bool
     */
    protected function isUpdateRequired()
    {
        $isAdmin = StateHelper::isAdminhtml();
        if ($isAdmin) {
            return true;
        }
        $time = $this->getMeta('css_updated_time');
        if (!$time) {
            return true;
        }
        $cssUpdatedTime = ConfigHelper::getValue('css_updated_time');
        if ($cssUpdatedTime && $cssUpdatedTime > $time) {
            return true;
        }

        return false;
    }

    /**
     * Parse CSS.
     *
     * Parsing the CSS file.
     *
     */
    protected function parseContent()
    {
        $this->renderCss();

        $name = $this->getName();

        /**
         * Parse CSS file.
         *
         * Fires when CSS file is parsed on Goomento.
         *
         * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
         *
         *
         * @param AbstractCss $this The current CSS file.
         */
        HooksHelper::doAction("pagebuilder/css-file/{$name}/parse", $this);

        return $this->stylesheetObj->__toString();
    }

    /**
     * Add control style rules.
     *
     * Register new style rules for the control.
     *
     *
     * @param array $control      The control.
     * @param array $values       Values array.
     * @param array $controls     The controls stack.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     */
    protected function addControlStyleRules(array $control, array $values, array $controls, array $placeholders, array $replacements)
    {
        $this->addControlRules(
            $control,
            $controls,
            function ($control) use ($values) {
                return $this->getStyleControlValue($control, $values);
            },
            $placeholders,
            $replacements
        );
    }

    /**
     * Get style control value.
     *
     * Retrieve the value of the style control for any give control and values.
     *
     * It will retrieve the control name and return the style value.
     *
     *
     * @param array $control The control.
     * @param array $values  Values array.
     *
     * @return mixed Style control value.
     */
    private function getStyleControlValue(array $control, array $values)
    {
        $value = $values[ $control['name'] ];

        if (isset($control['selectors_dictionary'][ $value ])) {
            $value = $control['selectors_dictionary'][ $value ];
        }

        if (! is_numeric($value) && ! is_float($value) && empty($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Init stylesheet.
     *
     * Initialize CSS file stylesheet by creating a new `Stylesheet` object and register new
     * breakpoints for the stylesheet.
     *
     */
    private function initStylesheet()
    {
        /** @var Stylesheet stylesheet_obj */
        $this->stylesheetObj = ObjectManagerHelper::get(Stylesheet::class);

        $breakpoints = Developer::DEFAULT_BREAKPOINTS;

        $this->stylesheetObj
            ->addDevice('mobile', 0)
            ->addDevice('tablet', $breakpoints['md'])
            ->addDevice('desktop', $breakpoints['lg']);
    }

    /**
     * Add repeater control style rules.
     *
     * Register new style rules for the repeater control.
     *
     *
     * @param ControlsStack $controlsStack The control stack.
     * @param array $repeaterControl The repeater control.
     * @param array $repeaterValues Repeater values array.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     */
    protected function addRepeaterControlStyleRules(ControlsStack $controlsStack, array $repeaterControl, array $repeaterValues, array $placeholders, array $replacements)
    {
        $placeholders = array_merge($placeholders, [ '{{CURRENT_ITEM}}' ]);

        foreach ($repeaterControl['style_fields'] as $index => $item) {
            $this->addControlsStackStyleRules(
                $controlsStack,
                $item,
                $repeaterValues[ $index ],
                $placeholders,
                array_merge($replacements, [ '.gmt-repeater-item-' . $repeaterValues[ $index ]['_id'] ]),
                $repeaterControl['fields']
            );
        }
    }

    /**
     * Add dynamic control style rules.
     *
     * Register new style rules for the dynamic control.
     *
     *
     * @param array $control The control.
     * @param string $value The value.
     * @throws Exception
     */
    protected function addDynamicControlStyleRules(array $control, string $value)
    {
        $tagsManager = ObjectManagerHelper::getTagsManager();

        $tagsManager->parseTagsText($value, $control, function ($id, $name, $settings) use ($tagsManager) {
            $tag = $tagsManager->createTag($id, $name, $settings);

            if (!$tag instanceof AbstractTag) {
                return;
            }

            $this->addControlsStackStyleRules($tag, $tag->getStyleControls(), $tag->getActiveSettings(), [ '{{WRAPPER}}' ], [ '#gmt-tag-' . $id ]);
        });
    }
}
