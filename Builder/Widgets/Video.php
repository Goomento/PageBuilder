<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Helper\Embed;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class Video extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'video';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/video.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Video');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fa-youtube fab';
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheirtDoc
     */
    public function getScriptDepends()
    {
        return ['goomento-widget-video'];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'video', 'player', 'embed', 'youtube', 'vimeo', 'dailymotion' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerVideoInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'type',
            [
                'label' => __('Source'),
                'type' => Controls::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => __('YouTube'),
                    'vimeo' => __('Vimeo'),
                    'dailymotion' => __('Dailymotion'),
                    'other' => __('Self Host'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'youtube_url',
            [
                'label' => __('Link'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your URL') . ' (YouTube)',
                'default' => 'https://www.youtube.com/watch?v=og_1u8RFmuI',
                'label_block' => true,
                'condition' => [
                    $prefix . 'type' => 'youtube',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'vimeo_url',
            [
                'label' => __('Link'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your URL (Vimeo)'),
                'default' => 'https://vimeo.com/242163982',
                'label_block' => true,
                'condition' => [
                    $prefix . 'type' => 'vimeo',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'dailymotion_url',
            [
                'label' => __('Link'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your URL') . ' (Dailymotion)',
                'default' => 'https://www.dailymotion.com/video/x2jtzvr',
                'label_block' => true,
                'condition' => [
                    $prefix . 'type' => 'dailymotion',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'external_url',
            [
                'label' => __('URL'),
                'type' => Controls::TEXT,
                'media_type' => 'video',
                'placeholder' => __('Enter your URL'),
                'condition' => [
                    $prefix . 'type' => 'other',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'start',
            [
                'label' => __('Start Time'),
                'type' => Controls::NUMBER,
                'description' => __('Specify a start time (in seconds)'),
                'condition' => [
                    $prefix . 'loop' => '',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'end',
            [
                'label' => __('End Time'),
                'type' => Controls::NUMBER,
                'description' => __('Specify an end time (in seconds)'),
                'condition' => [
                    $prefix . 'loop' => '',
                    $prefix . 'type' => [ 'youtube', 'other' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'options',
            [
                'label' => __('Video Options'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'autoplay',
            [
                'label' => __('Autoplay'),
                'type' => Controls::SWITCHER,
            ]
        );

        $widget->addControl(
            $prefix . 'mute',
            [
                'label' => __('Mute'),
                'type' => Controls::SWITCHER,
            ]
        );

        $widget->addControl(
            $prefix . 'loop',
            [
                'label' => __('Loop'),
                'type' => Controls::SWITCHER,
                'condition' => [
                    $prefix . 'type!' => 'dailymotion',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'controls',
            [
                'label' => __('Player Controls'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type!' => 'vimeo',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'showinfo',
            [
                'label' => __('Video Info'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => [ 'dailymotion' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'modestbranding',
            [
                'label' => __('Modest Branding'),
                'type' => Controls::SWITCHER,
                'condition' => [
                    $prefix . 'type' => [ 'youtube' ],
                    $prefix . 'controls' => 'yes',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'logo',
            [
                'label' => __('Logo'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => [ 'dailymotion' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'color',
            [
                'label' => __('Controls Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'type' => [ 'vimeo', 'dailymotion' ],
                ],
            ]
        );

        // YouTube.
        $widget->addControl(
            $prefix . 'yt_privacy',
            [
                'label' => __('Privacy Mode'),
                'type' => Controls::SWITCHER,
                'description' => __('When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.'),
                'condition' => [
                    $prefix . 'type' => 'youtube',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'rel',
            [
                'label' => __('Suggested Videos'),
                'type' => Controls::SELECT,
                'options' => [
                    '' => __('Current Video Channel'),
                    'yes' => __('Any Video'),
                ],
                'condition' => [
                    $prefix . 'type' => 'youtube',
                ],
            ]
        );

        // Vimeo.
        $widget->addControl(
            $prefix . 'vimeo_title',
            [
                'label' => __('Intro Title'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => 'vimeo',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'vimeo_portrait',
            [
                'label' => __('Intro Portrait'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => 'vimeo',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'vimeo_byline',
            [
                'label' => __('Intro Byline'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => 'vimeo',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'download_button',
            [
                'label' => __('Download Button'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'condition' => [
                    $prefix . 'type' => 'other',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'poster',
            [
                'label' => __('Poster'),
                'type' => Controls::MEDIA,
                'condition' => [
                    $prefix . 'type' => 'other',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerVideoImageOverlayInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'show_image_overlay',
            [
                'label' => __('Image Overlay'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
            ]
        );

        $widget->addControl(
            $prefix . 'image_overlay',
            [
                'label' => __('Choose Image'),
                'type' => Controls::MEDIA,
                'default' => [
                    'url' => DataHelper::getPlaceholderImageSrc(),
                ],
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'lazy_load',
            [
                'label' => __('Lazy Load'),
                'type' => Controls::SWITCHER,
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'type!' => 'other',
                ],
            ]
        );


        $widget->addControl(
            $prefix . 'show_play_icon',
            [
                'label' => __('Play Icon'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'image_overlay.url!' => '',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'lightbox',
            [
                'label' => __('Lightbox'),
                'type' => Controls::SWITCHER,
                'frontend_available' => true,
                'label_off' => __('Off'),
                'label_on' => __('On'),
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'image_overlay.url!' => '',
                ],
                'separator' => 'before',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerVideoStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'aspect_ratio',
            [
                'label' => __('Aspect Ratio'),
                'type' => Controls::SELECT,
                'options' => [
                    '169' => '16:9',
                    '219' => '21:9',
                    '43' => '4:3',
                    '32' => '3:2',
                    '11' => '1:1',
                    '916' => '9:16',
                ],
                'default' => '169',
                'prefix_class' => 'gmt-aspect-ratio-',
                'frontend_available' => true,
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters',
                'selector' => '{{WRAPPER}} .gmt-wrapper',
            ]
        );

        $widget->addControl(
            $prefix . 'play_icon_title',
            [
                'label' => __('Play Icon'),
                'type' => Controls::HEADING,
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'show_play_icon' => 'yes',
                ],
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'play_icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-custom-embed-play i' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'show_play_icon' => 'yes',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'play_icon_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-custom-embed-play i' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'show_play_icon' => 'yes',
                ],
            ]
        );

        $widget->addGroupControl(
            TextShadowGroup::NAME,
            [
                'name' => $prefix . 'play_icon_text_shadow',
                'selector' => '{{WRAPPER}} .gmt-custom-embed-play i',
                'fields_options' => [
                    'text_shadow_type' => [
                        'label' => __('Shadow'),
                    ],
                ],
                'condition' => [
                    $prefix . 'show_image_overlay' => 'yes',
                    $prefix . 'show_play_icon' => 'yes',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerVideoLightBoxStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'lightbox_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '#gmt-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'lightbox_ui_color',
            [
                'label' => __('UI Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '#gmt-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'lightbox_ui_color_hover',
            [
                'label' => __('UI Hover Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '#gmt-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
                ],
                'separator' => 'after',
            ]
        );

        $widget->addControl(
            $prefix . 'lightbox_video_width',
            [
                'label' => __('Content Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 30,
                    ],
                ],
                'selectors' => [
                    '(desktop+)#gmt-lightbox-{{ID}} .gmt-video-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'lightbox_content_position',
            [
                'label' => __('Content Position'),
                'type' => Controls::SELECT,
                'frontend_available' => true,
                'options' => [
                    '' => __('Center'),
                    'top' => __('Top'),
                ],
                'selectors' => [
                    '#gmt-lightbox-{{ID}} .gmt-video-container' => '{{VALUE}}; transform: translateX(-50%);',
                ],
                'selectors_dictionary' => [
                    'top' => 'top: 60px',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'lightbox_content_animation',
            [
                'label' => __('Entrance Animation'),
                'type' => Controls::ANIMATION,
                'frontend_available' => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_video',
            [
                'label' => __('Video'),
            ]
        );

        self::registerVideoInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_image_overlay',
            [
                'label' => __('Image Overlay'),
            ]
        );

        self::registerVideoImageOverlayInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_video_style',
            [
                'label' => __('Video'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerVideoStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_lightbox_style',
            [
                'label' => __('Lightbox'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_show_image_overlay' => 'yes',
                    self::NAME . '_image_overlay.url!' => '',
                    self::NAME . '_lightbox' => 'yes',
                ],
            ]
        );

        self::registerVideoLightBoxStyle($this);

        $this->endControlsSection();
    }

    /**
     * Get embed params.
     *
     * Retrieve video widget embed parameters.
     *
     *
     * @return array Video embed parameters.
     */
    public function getEmbedParams()
    {
        $settings = $this->getSettingsForDisplay();

        $params = [];

        if ($settings['video_autoplay'] && ! $this->hasImageOverlay()) {
            $params['autoplay'] = '1';
        }

        $paramsDictionary = [];

        if ('youtube' === $settings['video_type']) {
            $paramsDictionary = [
                'loop',
                'controls',
                'mute',
                'rel',
                'modestbranding',
            ];

            if ($settings['video_loop']) {
                $videoProperties = Embed::getVideoProperties($settings['video_youtube_url']);

                $params['playlist'] = $videoProperties['video_id'];
            }

            $params['start'] = $settings['video_start'];

            $params['end'] = $settings['video_end'];

            $params['wmode'] = 'opaque';
        } elseif ('vimeo' === $settings['video_type']) {
            $paramsDictionary = [
                'loop',
                'mute' => 'muted',
                'vimeo_title' => 'title',
                'vimeo_portrait' => 'portrait',
                'vimeo_byline' => 'byline',
            ];

            $params['color'] = str_replace('#', '', $settings['video_color']);

            $params['autopause'] = '0';
        } elseif ('dailymotion' === $settings['video_type']) {
            $paramsDictionary = [
                'controls',
                'mute',
                'showinfo' => 'ui-start-screen-info',
                'logo' => 'ui-logo',
            ];

            $params['ui-highlight'] = str_replace('#', '', $settings['video_color']);

            $params['start'] = $settings['video_start'];

            $params['endscreen-enable'] = '0';
        }

        foreach ($paramsDictionary as $key => $paramName) {
            $settingName = $paramName;

            if (is_string($key)) {
                $settingName = $key;
            }

            $settingName = 'video_' . $settingName;

            $settingValue = $settings[ $settingName ] ? '1' : '0';

            $params[ $paramName ] = $settingValue;
        }

        return $params;
    }

    /**
     * Whether the video widget has an overlay image or not.
     *
     * Used to determine whether an overlay image was set for the video.
     *
     *
     * @return bool Whether an image overlay was set for the video.
     */
    public function hasImageOverlay()
    {
        $settings = $this->getSettingsForDisplay();

        return ! empty($settings['video_image_overlay']['url']) && 'yes' === $settings['video_show_image_overlay'];
    }

    /**
     * @return array
     */
    public function getEmbedOptions()
    {
        $settings = $this->getSettingsForDisplay();

        $embedOptions = [];

        if ('youtube' === $settings['video_type']) {
            $embedOptions['privacy'] = $settings['video_yt_privacy'];
        } elseif ('vimeo' === $settings['video_type']) {
            $embedOptions['start'] = $settings['video_start'];
        }

        $embedOptions['lazy_load'] = ! empty($settings['video_lazy_load']);

        return $embedOptions;
    }

    /**
     * @return array
     */
    public function getExternalParams()
    {
        $settings = $this->getSettingsForDisplay();

        $videoParams = [];

        foreach ([ 'video_autoplay', 'video_loop', 'video_controls' ] as $optionName) {
            if ($settings[ $optionName ]) {
                $videoParams[ $optionName ] = '';
            }
        }

        if ($settings['video_mute']) {
            $videoParams['muted'] = 'muted';
        }

        if (!$settings['video_download_button']) {
            $videoParams['controlsList'] = 'nodownload';
        }

        if ($settings['video_poster']['url']) {
            $videoParams['poster'] = $settings['video_poster']['url'];
        }

        return $videoParams;
    }

    /**
     * @return string
     */
    public function getExternalVideoUrl()
    {
        $settings = $this->getSettingsForDisplay();

        $videoUrl = $settings['video_external_url'];

        if (empty($videoUrl)) {
            return '';
        }

        if ($settings['video_start'] || $settings['video_end']) {
            $videoUrl .= '#t=';
        }

        if ($settings['video_start']) {
            $videoUrl .= $settings['video_start'];
        }

        if ($settings['video_end']) {
            $videoUrl .= ',' . $settings['video_end'];
        }

        return $videoUrl;
    }

    /**
     * @return void
     */
    public function renderExternalVideo() : string
    {
        $videoUrl = $this->getExternalVideoUrl();
        if (empty($videoUrl)) {
            return '';
        }

        $videoParams = $this->getExternalParams();

        return '<video class="gmt-video" src="' . $videoUrl . '" ' . DataHelper::renderHtmlAttributes($videoParams) .'></video>';
    }
}
