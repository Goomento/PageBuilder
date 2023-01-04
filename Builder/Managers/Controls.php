<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;
use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Base\DataCache;
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
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\LoggerHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
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
     * @var AbstractControl[]|null
     */
    private $controls = null;

    /**
     * @var AbstractControlGroup[]|null
     */
    private $controlGroups = [];

    /**
     * @var DataCache[]
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
     * @param $tabName
     * @param $tabLabel
     */
    public static function addTab($tabName, $tabLabel)
    {
        if (! self::$tabs) {
            self::initTabs();
        }

        if (isset(self::$tabs[ $tabName ])) {
            return;
        }

        self::$tabs[ $tabName ] = $tabLabel;
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
     * @param $controlId
     * @param AbstractControl $controlInstance
     */
    public function registerControl($controlId, AbstractControl $controlInstance)
    {
        $this->controls[ $controlId ] = $controlInstance;
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
        $controlsData = [];

        foreach ($this->getControls() as $name => $control) {
            $controlsData[ $name ] = $control->getSettings();
        }

        return $controlsData;
    }

    /**
     * Render content
     */
    public function renderControls()
    {
        foreach ($this->getControls() as $control) {
            $control->printTemplate();
        }
    }

    /**
     * @param string|null $id
     * @return array|mixed|null
     */
    public function getControlGroups(?string $id)
    {
        $this->getControls();
        return $this->controlGroups[$id] ?? $this->controlGroups;
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
     * @param ControlsStack $controlsStack
     */
    public function openStack(ControlsStack $controlsStack)
    {
        $stackId = $controlsStack->getUniqueName();

        $this->stacks[ $stackId ] = new DataCache([
            'tabs' => [],
            'controls' => [],
        ]);

        $this->stacks[ $stackId ]->setId($controlsStack->getUniqueName());
    }

    /**
     * @param ControlsStack $element
     * @param $controlId
     * @param $controlData
     * @param array $options
     * @return bool
     */
    public function addControlToStack(ControlsStack $element, $controlId, $controlData, array $options = [])
    {
        $defaultOptions = [
            'overwrite' => false,
            'index' => null,
        ];

        $options = array_merge($defaultOptions, $options);

        $defaultArgs = [
            'type' => self::TEXT,
            'tab' => self::TAB_CONTENT,
        ];

        $controlData['name'] = $controlId;

        $controlData = array_merge($defaultArgs, $controlData);

        $controlTypeInstance = $this->getControl($controlData['type']);

        if (!$controlTypeInstance) {
            LoggerHelper::error(sprintf('AbstractControl type "%s" not found.', $controlData['type']));
            return false;
        }

        if ($controlTypeInstance instanceof AbstractControlData) {
            $controlDefaultValue = $controlTypeInstance::getDefaultValue();

            if (is_array($controlDefaultValue)) {
                $controlData['default'] = isset($controlData['default']) ? array_merge($controlDefaultValue, $controlData['default']) : $controlDefaultValue;
            } else {
                $controlData['default'] = $controlData['default'] ?? $controlDefaultValue;
            }
        }

        $stackId = $element->getUniqueName();

        if (!isset($this->stacks[ $stackId ])) {
            $this->openStack($element);
        }

        if (!$options['overwrite'] && isset($this->stacks[ $stackId ]['controls'][ $controlId ])) {
            return false;
        }

        $tabs = self::getTabs();

        if (!isset($tabs[ $controlData['tab'] ])) {
            $controlData['tab'] = $defaultArgs['tab'];
        }

        $this->stacks[ $stackId ]->setDataByPath('tabs/'. $controlData['tab'], $tabs[ $controlData['tab'] ]);
        $this->stacks[ $stackId ]->setDataByPath('controls/' . $controlId, $controlData);

        if (null !== $options['index']) {
            $controls = $this->stacks[ $stackId ]['controls'];

            $controlsKeys = array_keys($controls);

            array_splice($controlsKeys, $options['index'], 0, $controlId);

            $this->stacks[ $stackId ]->setData('controls', array_merge(array_flip($controlsKeys), $controls));
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function removeControlFromStack($stackId, $controlId)
    {
        if (is_array($controlId)) {
            foreach ($controlId as $id) {
                $this->removeControlFromStack($stackId, $id);
            }

            return true;
        }

        if (empty($this->stacks[ $stackId ]['controls'][ $controlId ])) {
            throw new BuilderException(
                sprintf('Cannot remove not-exists control: %s', $controlId)
            );
        }

        $this->stacks[ $stackId ]->unsetDataByPath('controls/' . $controlId);

        return true;
    }

    /**
     * @param $stackId
     * @param $controlId
     * @return mixed
     * @throws Exception
     */
    public function getControlFromStack($stackId, $controlId)
    {
        if (empty($this->stacks[ $stackId ]['controls'][ $controlId ])) {
            throw new BuilderException(
                'Cannot get a not-exists control.'
            );
        }

        return $this->stacks[ $stackId ]['controls'][ $controlId ];
    }

    /**
     * @param ControlsStack $element
     * @param $controlId
     * @param $controlData
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function updateControlInStack(ControlsStack $element, $controlId, $controlData, array $options = [])
    {
        $oldControlData = $this->getControlFromStack($element->getUniqueName(), $controlId);

        if (!empty($options['recursive'])) {
            $controlData = array_replace_recursive($oldControlData, $controlData);
        } else {
            $controlData = array_merge($oldControlData, $controlData);
        }

        return $this->addControlToStack($element, $controlId, $controlData, [
            'overwrite' => true,
        ]);
    }

    /**
     * @param null $stackId
     * @return array|mixed|null
     */
    public function getStacks($stackId = null)
    {
        if ($stackId) {
            if (isset($this->stacks[ $stackId ])) {
                return $this->stacks[ $stackId ]->toArray();
            }

            return null;
        }

        return $this->stacks;
    }

    /**
     * @param ControlsStack $controlsStack
     * @return mixed|null
     */
    public function getElementStack(ControlsStack $controlsStack)
    {
        $stackId = $controlsStack->getUniqueName();

        if (!isset($this->stacks[ $stackId ])) {
            $stack = new DataCache();
            $stack->load($stackId);
            if (!$stack->isEmpty()) {
                $this->stacks[ $stackId ] = $stack;
            } else {
                return null;
            }
        }

        return $this->stacks[ $stackId ]->toArray();
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
     * @param $postCss
     * @param $element
     */
    public function addContentCss($postCss, $element)
    {
        $elementSettings = $element->getSettings();

        if (empty($elementSettings['custom_css'])) {
            return;
        }

        $css = trim($elementSettings['custom_css']);

        if (empty($css)) {
            return;
        }
        $css = str_replace('selector', $postCss->getElementUniqueSelector($element), $css);

        $postCss->getStylesheet()->addRawCss($css);
    }

    /**
     * @param ContentCss $contentCss
     */
    public function addPageSettingsCustomCss(ContentCss $contentCss)
    {
        $customCss = (string) $contentCss->getModel()->getSetting('custom_css');

        $documentManager = ObjectManagerHelper::getDocumentsManager();
        $document = $documentManager->getByContent($contentCss->getModel());

        $customCss = trim($customCss);

        if (empty($customCss)) {
            return;
        }

        $customCss = str_replace('selector', $document->getCssWrapperSelector(), $customCss);

        $contentCss->getStylesheet()->addRawCss($customCss);
    }

    /**
     * Controls constructor.
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/element/parse_css', [$this, 'addContentCss']);
        HooksHelper::addAction('pagebuilder/css-file/content/parse', [$this, 'addPageSettingsCustomCss']);
    }
}
