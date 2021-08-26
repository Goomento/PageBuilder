<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Animation;
use Goomento\PageBuilder\Builder\Controls\Base;
use Goomento\PageBuilder\Builder\Controls\BaseData;
use Goomento\PageBuilder\Builder\Controls\BoxShadow;
use Goomento\PageBuilder\Builder\Controls\Button;
use Goomento\PageBuilder\Builder\Controls\Choose;
use Goomento\PageBuilder\Builder\Controls\Code;
use Goomento\PageBuilder\Builder\Controls\Color;
use Goomento\PageBuilder\Builder\Controls\DateTime;
use Goomento\PageBuilder\Builder\Controls\DeprecatedNotice;
use Goomento\PageBuilder\Builder\Controls\Dimensions;
use Goomento\PageBuilder\Builder\Controls\Divider;
use Goomento\PageBuilder\Builder\Controls\ExitAnimation;
use Goomento\PageBuilder\Builder\Controls\Font;
use Goomento\PageBuilder\Builder\Controls\Gallery;
use Goomento\PageBuilder\Builder\Controls\Groups\Background;
use Goomento\PageBuilder\Builder\Controls\Groups\Border;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilter;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSize;
use Goomento\PageBuilder\Builder\Controls\Groups\Typography;
use Goomento\PageBuilder\Builder\Controls\Heading;
use Goomento\PageBuilder\Builder\Controls\Hidden;
use Goomento\PageBuilder\Builder\Controls\HoverAnimation;
use Goomento\PageBuilder\Builder\Controls\Icon;
use Goomento\PageBuilder\Builder\Controls\ImageDimensions;
use Goomento\PageBuilder\Builder\Controls\Media;
use Goomento\PageBuilder\Builder\Controls\NestedRepeater;
use Goomento\PageBuilder\Builder\Controls\Number;
use Goomento\PageBuilder\Builder\Controls\PopoverToggle;
use Goomento\PageBuilder\Builder\Controls\RawHtml;
use Goomento\PageBuilder\Builder\Controls\Repeater;
use Goomento\PageBuilder\Builder\Controls\Section;
use Goomento\PageBuilder\Builder\Controls\Select;
use Goomento\PageBuilder\Builder\Controls\Select2;
use Goomento\PageBuilder\Builder\Controls\Slider;
use Goomento\PageBuilder\Builder\Controls\Structure;
use Goomento\PageBuilder\Builder\Controls\Switcher;
use Goomento\PageBuilder\Builder\Controls\Tab;
use Goomento\PageBuilder\Builder\Controls\Tabs;
use Goomento\PageBuilder\Builder\Controls\Text;
use Goomento\PageBuilder\Builder\Controls\Textarea;
use Goomento\PageBuilder\Builder\Controls\TextShadow;
use Goomento\PageBuilder\Builder\Controls\Url;
use Goomento\PageBuilder\Builder\Controls\Wysiwyg;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\DynamicTags\DynamicCss;
use Goomento\PageBuilder\Core\Settings\Manager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticLogger;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use ReflectionException;

/**
 * Class Controls
 * @package Goomento\PageBuilder\Builder\Managers
 */
class Controls
{
    const TAB_CONTENT = 'content';
    const TAB_STYLE = 'style';
    const TAB_ADVANCED = 'advanced';
    const TAB_RESPONSIVE = 'responsive';
    const TAB_LAYOUT = 'layout';
    const TAB_SETTINGS = 'settings';
    const TEXT = 'text';
    const NUMBER = 'number';
    const TEXTAREA = 'textarea';
    const SELECT = 'select';
    const SWITCHER = 'switcher';
    const BUTTON = 'button';
    const HIDDEN = 'hidden';
    const HEADING = 'heading';
    const RAW_HTML = 'raw_html';
    const DEPRECATED_NOTICE = 'deprecated_notice';
    const POPOVER_TOGGLE = 'popover_toggle';
    const SECTION = 'section';
    const TAB = 'tab';
    const TABS = 'tabs';
    const DIVIDER = 'divider';
    const COLOR = 'color';
    const MEDIA = 'media';
    const SLIDER = 'slider';
    const DIMENSIONS = 'dimensions';
    const CHOOSE = 'choose';
    const WYSIWYG = 'wysiwyg';
    const CODE = 'code';
    const FONT = 'font';
    const IMAGE_DIMENSIONS = 'image_dimensions';
    const URL = 'url';
    const REPEATER = 'repeater';
    const NESTED_REPEATER = 'nested_repeater';
    const ICON = 'icon';
    const ICONS = 'icons';
    const GALLERY = 'gallery';
    const STRUCTURE = 'structure';
    const SELECT2 = 'select2';

    /**
     * Date/Time control.
     */
    const DATE_TIME = 'date_time';
    const BOX_SHADOW = 'box_shadow';
    const TEXT_SHADOW = 'text_shadow';
    const ANIMATION = 'animation';
    const HOVER_ANIMATION = 'hover_animation';
    const EXIT_ANIMATION = 'exit_animation';
    private $controls = null;
    private $control_groups = [];
    private $stacks = [];
    private static $tabs;

    /**
     * Init tab
     */
    private static function initTabs()
    {
        self::$tabs = [
            self::TAB_CONTENT => __('Content'),
            self::TAB_STYLE => __('Style'),
            self::TAB_ADVANCED => __('Advanced'),
            self::TAB_RESPONSIVE => __('Responsive'),
            self::TAB_LAYOUT => __('Layout'),
            self::TAB_SETTINGS => __('Settings'),
        ];
    }

    /**
     * @return mixed
     */
    public static function getTabs()
    {
        if (! self::$tabs) {
            self::initTabs();
        }

        return self::$tabs;
    }

    /**
     * @param $tab_name
     * @param $tab_label
     */
    public static function addTab($tab_name, $tab_label)
    {
        if (! self::$tabs) {
            self::initTabs();
        }

        if (isset(self::$tabs[ $tab_name ])) {
            return;
        }

        self::$tabs[ $tab_name ] = $tab_label;
    }

    /**
     * @return string[]
     */
    public static function getGroupsNames()
    {
        return [
            Background::class,
            Border::class,
            Typography::class,
            ImageSize::class,
            \Goomento\PageBuilder\Builder\Controls\Groups\BoxShadow::class,
            CssFilter::class,
            \Goomento\PageBuilder\Builder\Controls\Groups\TextShadow::class,
        ];
    }

    /**
     * @return string[]
     */
    public static function getControlsNames()
    {
        return [
            Text::class,
            Number::class,
            Textarea::class,
            Select::class,
            Switcher::class,
            Button::class,
            Hidden::class,
            Heading::class,
            RawHtml::class,
            PopoverToggle::class,
            Section::class,
            Tab::class,
            Tabs::class,
            Divider::class,
            DeprecatedNotice::class,
            Color::class,
            Media::class,
            Slider::class,
            Dimensions::class,
            Choose::class,
            Wysiwyg::class,
            Code::class,
            Font::class,
            ImageDimensions::class,
            Url::class,
            Icon::class,
            \Goomento\PageBuilder\Builder\Controls\Icons::class,
            Gallery::class,
            Structure::class,
            Select2::class,
            DateTime::class,
            Repeater::class,
            NestedRepeater::class,
            BoxShadow::class,
            TextShadow::class,
            Animation::class,
            HoverAnimation::class,
            ExitAnimation::class,
        ];
    }

    /**
     * Register controls.
     *
     * This method creates a list of all the supported controls by requiring the
     * control files and initializing each one of them.
     *
     * The list of supported controls includes the regular controls and the group
     * controls.
     *
     *
     */
    private function registerControls()
    {
        $this->controls = [];
        foreach (self::getControlsNames() as $class_name) {
            /** @var \Goomento\PageBuilder\Builder\Controls\Base $control */
            $control = StaticObjectManager::get($class_name);
            $this->registerControl($control->getType(), $control);
        }

        // Group Controls
        foreach (self::getGroupsNames() as $group_name) {
            /** @var \Goomento\PageBuilder\Builder\Controls\Groups\Base $group */
            $group = StaticObjectManager::get($group_name);
            $this->control_groups[ $group->getType() ] = $group;
        }

        Hooks::doAction('pagebuilder/controls/controls_registered', $this);
    }

    /**
     * @param $control_id
     * @param Base $control_instance
     */
    public function registerControl($control_id, Base $control_instance)
    {
        $this->controls[ $control_id ] = $control_instance;
    }

    /**
     * @param $control_id
     * @return bool
     */
    public function unregisterControl($control_id)
    {
        if (! isset($this->controls[ $control_id ])) {
            return false;
        }

        unset($this->controls[ $control_id ]);

        return true;
    }

    /**
     * @return null
     */
    public function getControls()
    {
        if (null === $this->controls) {
            $this->registerControls();
        }

        return $this->controls;
    }

    /**
     * @param $control_id
     * @return false|mixed
     */
    public function getControl($control_id)
    {
        $controls = $this->getControls();

        return $controls[$control_id] ?? false;
    }

    /**
     * @return array
     */
    public function getControlsData()
    {
        $controls_data = [];

        foreach ($this->getControls() as $name => $control) {
            $controls_data[ $name ] = $control->getSettings();
        }

        return $controls_data;
    }

    /**
     *
     */
    public function renderControls()
    {
        foreach ($this->getControls() as $control) {
            $control->printTemplate();
        }
    }

    /**
     * @param null $id
     * @return array|mixed|null
     */
    public function getControlGroups($id = null)
    {
        if ($id) {
            return isset($this->control_groups[ $id ]) ? $this->control_groups[ $id ] : null;
        }

        return $this->control_groups;
    }

    /**
     * @param $id
     * @param $instance
     * @return mixed
     */
    public function addGroupControl($id, $instance)
    {
        $this->control_groups[ $id ] = $instance;

        return $instance;
    }

    /**
     * Add Enqueue
     */
    public function enqueueControlScripts()
    {
        foreach ($this->getControls() as $control) {
            $control->enqueue();
        }
    }

    /**
     * @param ControlsStack $controls_stack
     */
    public function openStack(ControlsStack $controls_stack)
    {
        $stack_id = $controls_stack->getUniqueName();

        $this->stacks[ $stack_id ] = [
            'tabs' => [],
            'controls' => [],
        ];
    }

    /**
     * @param ControlsStack $element
     * @param $control_id
     * @param $control_data
     * @param array $options
     * @return bool
     */
    public function addControlToStack(ControlsStack $element, $control_id, $control_data, $options = [])
    {
        $default_options = [
            'overwrite' => false,
            'index' => null,
        ];

        $options = array_merge($default_options, $options);

        $default_args = [
            'type' => self::TEXT,
            'tab' => self::TAB_CONTENT,
        ];

        $control_data['name'] = $control_id;

        $control_data = array_merge($default_args, $control_data);

        $control_type_instance = $this->getControl($control_data['type']);

        if (! $control_type_instance) {
            StaticLogger::error(sprintf('Control type "%s" not found.', $control_data['type']));
            return false;
        }

        if ($control_type_instance instanceof BaseData) {
            $control_default_value = $control_type_instance->getDefaultValue();

            if (is_array($control_default_value)) {
                $control_data['default'] = isset($control_data['default']) ? array_merge($control_default_value, $control_data['default']) : $control_default_value;
            } else {
                $control_data['default'] = isset($control_data['default']) ? $control_data['default'] : $control_default_value;
            }
        }

        $stack_id = $element->getUniqueName();

        if (! $options['overwrite'] && isset($this->stacks[ $stack_id ]['controls'][ $control_id ])) {
            return false;
        }

        $tabs = self::getTabs();

        if (! isset($tabs[ $control_data['tab'] ])) {
            $control_data['tab'] = $default_args['tab'];
        }

        $this->stacks[ $stack_id ]['tabs'][ $control_data['tab'] ] = $tabs[ $control_data['tab'] ];

        $this->stacks[ $stack_id ]['controls'][ $control_id ] = $control_data;

        if (null !== $options['index']) {
            $controls = $this->stacks[ $stack_id ]['controls'];

            $controls_keys = array_keys($controls);

            array_splice($controls_keys, $options['index'], 0, $control_id);

            $this->stacks[ $stack_id ]['controls'] = array_merge(array_flip($controls_keys), $controls);
        }

        return true;
    }
    public function removeControlFromStack($stack_id, $control_id)
    {
        if (is_array($control_id)) {
            foreach ($control_id as $id) {
                $this->removeControlFromStack($stack_id, $id);
            }

            return true;
        }

        if (empty($this->stacks[ $stack_id ]['controls'][ $control_id ])) {
            return new Exception('Cannot remove not-exists control.');
        }

        unset($this->stacks[ $stack_id ]['controls'][ $control_id ]);

        return true;
    }
    public function getControlFromStack($stack_id, $control_id)
    {
        if (empty($this->stacks[ $stack_id ]['controls'][ $control_id ])) {
            return new Exception('Cannot get a not-exists control.');
        }

        return $this->stacks[ $stack_id ]['controls'][ $control_id ];
    }

    /**
     * @param ControlsStack $element
     * @param $control_id
     * @param $control_data
     * @param array $options
     * @return bool
     */
    public function updateControlInStack(ControlsStack $element, $control_id, $control_data, array $options = [])
    {
        $old_control_data = $this->getControlFromStack($element->getUniqueName(), $control_id);

        if (! empty($options['recursive'])) {
            $control_data = array_replace_recursive($old_control_data, $control_data);
        } else {
            $control_data = array_merge($old_control_data, $control_data);
        }

        return $this->addControlToStack($element, $control_id, $control_data, [
            'overwrite' => true,
        ]);
    }

    /**
     * @param null $stack_id
     * @return array|mixed|null
     */
    public function getStacks($stack_id = null)
    {
        if ($stack_id) {
            if (isset($this->stacks[ $stack_id ])) {
                return $this->stacks[ $stack_id ];
            }

            return null;
        }

        return $this->stacks;
    }

    /**
     * @param ControlsStack $controls_stack
     * @return mixed|null
     */
    public function getElementStack(ControlsStack $controls_stack)
    {
        $stack_id = $controls_stack->getUniqueName();

        if (! isset($this->stacks[ $stack_id ])) {
            return null;
        }

        return $this->stacks[ $stack_id ];
    }

    /**
     * @param ControlsStack $controls_stack
     * @throws ReflectionException
     */
    public function addCustomCssControls(ControlsStack $controls_stack)
    {
        $controls_stack->startControlsSection(
            'section_custom_css',
            [
                'label' => __('Custom CSS'),
                'tab' => self::TAB_ADVANCED,
            ]
        );

        $controls_stack->addControl(
            'custom_css_title',
            [
                'raw' => __('Add your own custom CSS here'),
                'type' => self::RAW_HTML,
            ]
        );

        $controls_stack->addControl(
            'custom_css',
            [
                'type' => self::CODE,
                'label' => __('Custom CSS'),
                'language' => 'css',
                'render_type' => 'ui',
                'show_label' => false,
                'separator' => 'none',
            ]
        );

        $controls_stack->addControl(
            'custom_css_description',
            [
                'raw' => 'Use "selector" to target wrapper element.',
                'type' => self::RAW_HTML,
            ]
        );

        $controls_stack->endControlsSection();
    }

    /**
     * @param $post_css
     * @param $element
     */
    public function addContentCss($post_css, $element)
    {
        if ($post_css instanceof DynamicCss) {
            return;
        }

        $element_settings = $element->getSettings();

        if (empty($element_settings['custom_css'])) {
            return;
        }

        $css = trim($element_settings['custom_css']);

        if (empty($css)) {
            return;
        }
        $css = str_replace('selector', $post_css->getElementUniqueSelector($element), $css);

        $post_css->getStylesheet()->addRawCss($css);
    }

    /**
     * @param $post_css
     */
    public function addPageSettingsCss($post_css)
    {
        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);
        $document = $documentManager->get($post_css->getContentId());
        $page = Manager::getSettingsManagers('page');
        $page = $page->getSettingModel($post_css->getContentId());
        $custom_css = $page->getSettings('custom_css');

        $custom_css = trim($custom_css);

        if (empty($custom_css)) {
            return;
        }

        $custom_css = str_replace('selector', $document->getCssWrapperSelector(), $custom_css);

        $post_css->getStylesheet()->addRawCss($custom_css);
    }

    /**
     * Controls constructor.
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/element/parse_css', [$this, 'addContentCss'], 10, 2);
        Hooks::addAction('pagebuilder/css-file/content/parse', [$this, 'addPageSettingsCss']);
    }
}
