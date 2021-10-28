<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Animation;
use Goomento\PageBuilder\Builder\Base\AbstractControl;
use Goomento\PageBuilder\Builder\Controls\AbstractControlData;
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
use Goomento\PageBuilder\Builder\Controls\Groups\BackgroundGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSizeGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Controls\Heading;
use Goomento\PageBuilder\Builder\Controls\Hidden;
use Goomento\PageBuilder\Builder\Controls\HoverAnimation;
use Goomento\PageBuilder\Builder\Controls\Icon;
use Goomento\PageBuilder\Builder\Controls\ImageDimensions;
use Goomento\PageBuilder\Builder\Controls\Media;
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
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\LoggerHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Controls
{
    const TAB_CONTENT = 'content';
    const TAB_STYLE = 'style';
    const TAB_ADVANCED = 'advanced';
    const TAB_RESPONSIVE = 'responsive';
    const TAB_LAYOUT = 'layout';
    const TAB_SETTINGS = 'settings';

    /**
     * Control types
     */
    const TEXT = Text::NAME;
    const NUMBER = Number::NAME;
    const TEXTAREA = Textarea::NAME;
    const SELECT = Select::NAME;
    const SWITCHER = Switcher::NAME;
    const BUTTON = Button::NAME;
    const HIDDEN = Hidden::NAME;
    const HEADING = Heading::NAME;
    const RAW_HTML = RawHtml::NAME;
    const DEPRECATED_NOTICE = DeprecatedNotice::NAME;
    const POPOVER_TOGGLE = PopoverToggle::NAME;
    const SECTION = Section::NAME;
    const TAB = Tab::NAME;
    const TABS = Tabs::NAME;
    const DIVIDER = Divider::NAME;
    const COLOR = Color::NAME;
    const MEDIA = Media::NAME;
    const SLIDER = Slider::NAME;
    const DIMENSIONS = Dimensions::NAME;
    const CHOOSE = Choose::NAME;
    const WYSIWYG = Wysiwyg::NAME;
    const CODE = Code::NAME;
    const FONT = Font::NAME;
    const IMAGE_DIMENSIONS = ImageDimensions::NAME;
    const URL = Url::NAME;
    const REPEATER = Repeater::NAME;
    const ICON = Icon::NAME;
    const ICONS = \Goomento\PageBuilder\Builder\Controls\Icons::NAME;
    const GALLERY = Gallery::NAME;
    const STRUCTURE = Structure::NAME;
    const SELECT2 = Select2::NAME;

    /**
     * Date/Time control.
     */
    const DATE_TIME = DateTime::NAME;

    /**
     * Animate control
     */
    const BOX_SHADOW = BoxShadow::NAME;
    const TEXT_SHADOW = TextShadow::NAME;
    const ANIMATION = Animation::NAME;
    const HOVER_ANIMATION = HoverAnimation::NAME;
    const EXIT_ANIMATION = ExitAnimation::NAME;

    /**
     * @var \Goomento\PageBuilder\Builder\Base\AbstractControl[]|null
     */
    private $controls = null;

    /**
     * @var AbstractControlGroup[]|null
     */
    private $controlGroups = [];

    /**
     * @var array
     */
    private $stacks = [];

    /**
     * @var string[]
     */
    private $controlTypes = [
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
        BoxShadow::class,
        TextShadow::class,
        Animation::class,
        HoverAnimation::class,
        ExitAnimation::class,
    ];

    private $groupTypes = [
        BackgroundGroup::class,
        BorderGroup::class,
        TypographyGroup::class,
        ImageSizeGroup::class,
        BoxShadowGroup::class,
        CssFilterGroup::class,
        TextShadowGroup::class,
    ];

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
        foreach ($this->controlTypes as $type) {
            if ($type::ENABLED) {
                /** @var AbstractControl $control */
                $control = ObjectManagerHelper::get($type);
                $this->registerControl($control::NAME, $control);
            }
        }

        // Group Controls
        foreach ($this->groupTypes as $type) {
            if ($type::ENABLED) {
                /** @var AbstractControlGroup $group */
                $group = ObjectManagerHelper::get($type);
                $this->controlGroups[ $group->getName() ] = $group;
            }
        }

        HooksHelper::doAction('pagebuilder/controls/controls_registered', $this);
    }

    /**
     * @param $control_id
     * @param AbstractControl $control_instance
     */
    public function registerControl($control_id, AbstractControl $control_instance)
    {
        $this->controls[ $control_id ] = $control_instance;
    }

    /**
     * @param $controlId
     * @return bool
     */
    public function unregisterControl($controlId): bool
    {
        if (!isset($this->controls[ $controlId ])) {
            return false;
        }

        unset($this->controls[ $controlId ]);

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
     * @param $controlId
     * @return false|mixed
     */
    public function getControl($controlId)
    {
        $controls = $this->getControls();

        return $controls[$controlId] ?? false;
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
        if (!empty($id)) {
            return $this->controlGroups[$id] ?? null;
        }

        return $this->controlGroups;
    }

    /**
     * @param $id
     * @param $instance
     * @return mixed
     */
    public function addGroupControl($id, $instance)
    {
        $this->controlGroups[ $id ] = $instance;

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
    public function addControlToStack(ControlsStack $element, $control_id, $control_data, array $options = [])
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

        if (!$control_type_instance) {
            LoggerHelper::error(
                sprintf('AbstractControl type "%s" not found.', $control_data['type'])
            );
            return false;
        }

        if ($control_type_instance instanceof AbstractControlData) {
            $control_default_value = $control_type_instance::getDefaultValue();

            if (is_array($control_default_value)) {
                $control_data['default'] = isset($control_data['default']) ? array_merge($control_default_value, $control_data['default']) : $control_default_value;
            } else {
                $control_data['default'] = $control_data['default'] ?? $control_default_value;
            }
        }

        $stack_id = $element->getUniqueName();

        if (!$options['overwrite'] && isset($this->stacks[ $stack_id ]['controls'][ $control_id ])) {
            return false;
        }

        $tabs = self::getTabs();

        if (!isset($tabs[ $control_data['tab'] ])) {
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

    /**
     * @throws Exception
     */
    public function removeControlFromStack($stack_id, $control_id)
    {
        if (is_array($control_id)) {
            foreach ($control_id as $id) {
                $this->removeControlFromStack($stack_id, $id);
            }

            return true;
        }

        if (empty($this->stacks[ $stack_id ]['controls'][ $control_id ])) {
            throw new Exception(
                'Cannot remove not-exists control.'
            );
        }

        unset($this->stacks[ $stack_id ]['controls'][ $control_id ]);

        return true;
    }

    /**
     * @param $stack_id
     * @param $control_id
     * @return mixed
     * @throws Exception
     */
    public function getControlFromStack($stack_id, $control_id)
    {
        if (empty($this->stacks[ $stack_id ]['controls'][ $control_id ])) {
            throw new Exception(
                'Cannot get a not-exists control.'
            );
        }

        return $this->stacks[ $stack_id ]['controls'][ $control_id ];
    }

    /**
     * @param ControlsStack $element
     * @param $control_id
     * @param $control_data
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function updateControlInStack(ControlsStack $element, $control_id, $control_data, array $options = [])
    {
        $old_control_data = $this->getControlFromStack($element->getUniqueName(), $control_id);

        if (!empty($options['recursive'])) {
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

        if (!isset($this->stacks[ $stack_id ])) {
            return null;
        }

        return $this->stacks[ $stack_id ];
    }

    /**
     * @param ControlsStack $controlsStack
     * @throws Exception
     */
    public static function addExtendControls(ControlsStack $controlsStack)
    {
        $controlsStack->startControlsSection(
            'section_custom_css',
            [
                'label' => __('Custom CSS'),
                'tab' => self::TAB_ADVANCED,
            ]
        );

        $controlsStack->addControl(
            'custom_css_title',
            [
                'raw' => __('Add your own custom CSS here'),
                'type' => self::RAW_HTML,
            ]
        );

        $controlsStack->addControl(
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

        $controlsStack->addControl(
            'custom_css_description',
            [
                'raw' => 'Use "selector" to target wrapper element.',
                'type' => self::RAW_HTML,
            ]
        );

        $controlsStack->endControlsSection();
    }

    /**
     * @param $post_css
     * @param $element
     */
    public function addContentCss($post_css, $element)
    {
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
        /** @var Documents $documentManager */
        $documentManager = ObjectManagerHelper::get(Documents::class);
        $document = $documentManager->get($post_css->getContentId());
        /** @var Settings $settingsManager */
        $settingsManager = ObjectManagerHelper::get(Settings::class);
        /** @var PageSettings $page */
        $page = $settingsManager->getSettingsManagers(PageSettings::NAME);
        $page = $page->getSettingModel($post_css->getContentId());
        $customCss = (string) $page->getSettings('custom_css');

        $customCss = trim($customCss);

        if (empty($customCss)) {
            return;
        }

        $customCss = str_replace('selector', $document->getCssWrapperSelector(), $customCss);

        $post_css->getStylesheet()->addRawCss($customCss);
    }

    /**
     * Controls constructor.
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/element/parse_css', [$this, 'addContentCss']);
        HooksHelper::addAction('pagebuilder/css-file/content/parse', [$this, 'addPageSettingsCss']);
    }
}
