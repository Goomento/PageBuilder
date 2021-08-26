<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSize;
use Goomento\PageBuilder\Builder\Embed;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Core\DynamicTags\Module as TagsModule;
use Goomento\PageBuilder\Core\Editor\Editor;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Video
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Video extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve video widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'video';
    }

    /**
     * Get widget title.
     *
     * Retrieve video widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Video');
    }

    /**
     * Get widget icon.
     *
     * Retrieve video widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fa-youtube fab';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the video widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     *
     * @return array Widget categories.
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
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     *
     * @return array Widget keywords.
     */
    public function getKeywords()
    {
        return [ 'video', 'player', 'embed', 'youtube', 'vimeo', 'dailymotion' ];
    }

    /**
     * Register video widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_video',
            [
                'label' => __('Video'),
            ]
        );

        $this->addControl(
            'video_type',
            [
                'label' => __('Source'),
                'type' => Controls::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => __('YouTube'),
                    'vimeo' => __('Vimeo'),
                    'dailymotion' => __('Dailymotion'),
                    'other' => __('Other'),
                ],
            ]
        );

        $this->addControl(
            'youtube_url',
            [
                'label' => __('Link'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'placeholder' => __('Enter your URL') . ' (YouTube)',
                'default' => 'https://www.youtube.com/watch?v=og_1u8RFmuI',
                'label_block' => true,
                'condition' => [
                    'video_type' => 'youtube',
                ],
            ]
        );

        $this->addControl(
            'vimeo_url',
            [
                'label' => __('Link'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'placeholder' => __('Enter your URL') . ' (Vimeo)',
                'default' => 'https://vimeo.com/242163982',
                'label_block' => true,
                'condition' => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->addControl(
            'dailymotion_url',
            [
                'label' => __('Link'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'placeholder' => __('Enter your URL') . ' (Dailymotion)',
                'default' => 'https://www.dailymotion.com/video/x2jtzvr',
                'label_block' => true,
                'condition' => [
                    'video_type' => 'dailymotion',
                ],
            ]
        );

        $this->addControl(
            'external_url',
            [
                'label' => __('URL'),
                'type' => Controls::URL,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'media_type' => 'video',
                'placeholder' => __('Enter your URL'),
                'condition' => [
                    'video_type' => 'other',
                ],
            ]
        );

        $this->addControl(
            'start',
            [
                'label' => __('Start Time'),
                'type' => Controls::NUMBER,
                'description' => __('Specify a start time (in seconds)'),
                'condition' => [
                    'loop' => '',
                ],
            ]
        );

        $this->addControl(
            'end',
            [
                'label' => __('End Time'),
                'type' => Controls::NUMBER,
                'description' => __('Specify an end time (in seconds)'),
                'condition' => [
                    'loop' => '',
                    'video_type' => [ 'youtube', 'other' ],
                ],
            ]
        );

        $this->addControl(
            'video_options',
            [
                'label' => __('Video Options'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'autoplay',
            [
                'label' => __('Autoplay'),
                'type' => Controls::SWITCHER,
            ]
        );

        $this->addControl(
            'mute',
            [
                'label' => __('Mute'),
                'type' => Controls::SWITCHER,
            ]
        );

        $this->addControl(
            'loop',
            [
                'label' => __('Loop'),
                'type' => Controls::SWITCHER,
                'condition' => [
                    'video_type!' => 'dailymotion',
                ],
            ]
        );

        $this->addControl(
            'controls',
            [
                'label' => __('Player Controls'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    'video_type!' => 'vimeo',
                ],
            ]
        );

        $this->addControl(
            'showinfo',
            [
                'label' => __('Video Info'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    'video_type' => [ 'dailymotion' ],
                ],
            ]
        );

        $this->addControl(
            'modestbranding',
            [
                'label' => __('Modest Branding'),
                'type' => Controls::SWITCHER,
                'condition' => [
                    'video_type' => [ 'youtube' ],
                    'controls' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'logo',
            [
                'label' => __('Logo'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    'video_type' => [ 'dailymotion' ],
                ],
            ]
        );

        $this->addControl(
            'color',
            [
                'label' => __('Controls Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    'video_type' => [ 'vimeo', 'dailymotion' ],
                ],
            ]
        );

        // YouTube.
        $this->addControl(
            'yt_privacy',
            [
                'label' => __('Privacy Mode'),
                'type' => Controls::SWITCHER,
                'description' => __('When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.'),
                'condition' => [
                    'video_type' => 'youtube',
                ],
            ]
        );

        $this->addControl(
            'rel',
            [
                'label' => __('Suggested Videos'),
                'type' => Controls::SELECT,
                'options' => [
                    '' => __('Current Video Channel'),
                    'yes' => __('Any Video'),
                ],
                'condition' => [
                    'video_type' => 'youtube',
                ],
            ]
        );

        // Vimeo.
        $this->addControl(
            'vimeo_title',
            [
                'label' => __('Intro Title'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->addControl(
            'vimeo_portrait',
            [
                'label' => __('Intro Portrait'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->addControl(
            'vimeo_byline',
            [
                'label' => __('Intro Byline'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->addControl(
            'download_button',
            [
                'label' => __('Download Button'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'condition' => [
                    'video_type' => 'other',
                ],
            ]
        );

        $this->addControl(
            'poster',
            [
                'label' => __('Poster'),
                'type' => Controls::MEDIA,
                'condition' => [
                    'video_type' => 'other',
                ],
            ]
        );

        $this->addControl(
            'view',
            [
                'label' => __('View'),
                'type' => Controls::HIDDEN,
                'default' => 'youtube',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_image_overlay',
            [
                'label' => __('Image Overlay'),
            ]
        );

        $this->addControl(
            'show_image_overlay',
            [
                'label' => __('Image Overlay'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
            ]
        );

        $this->addControl(
            'image_overlay',
            [
                'label' => __('Choose Image'),
                'type' => Controls::MEDIA,
                'default' => [
                    'url' => Utils::getPlaceholderImageSrc(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'show_image_overlay' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'lazy_load',
            [
                'label' => __('Lazy Load'),
                'type' => Controls::SWITCHER,
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'video_type!' => 'other',
                ],
            ]
        );

        $this->addGroupControl(
            ImageSize::getType(),
            [
                'name' => 'image_overlay', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_overlay_size` and `image_overlay_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
                'condition' => [
                    'show_image_overlay' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'show_play_icon',
            [
                'label' => __('Play Icon'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'image_overlay[url]!' => '',
                ],
            ]
        );

        $this->addControl(
            'lightbox',
            [
                'label' => __('Lightbox'),
                'type' => Controls::SWITCHER,
                'frontend_available' => true,
                'label_off' => __('Off'),
                'label_on' => __('On'),
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'image_overlay[url]!' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_video_style',
            [
                'label' => __('Video'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'aspect_ratio',
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

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\CssFilter::getType(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .gmt-wrapper',
            ]
        );

        $this->addControl(
            'play_icon_title',
            [
                'label' => __('Play Icon'),
                'type' => Controls::HEADING,
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'show_play_icon' => 'yes',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'play_icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-custom-embed-play i' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'show_play_icon' => 'yes',
                ],
            ]
        );

        $this->addResponsiveControl(
            'play_icon_size',
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
                    'show_image_overlay' => 'yes',
                    'show_play_icon' => 'yes',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TextShadow::getType(),
            [
                'name' => 'play_icon_text_shadow',
                'selector' => '{{WRAPPER}} .gmt-custom-embed-play i',
                'fields_options' => [
                    'text_shadow_type' => [
                        'label' => __('Shadow'),
                    ],
                ],
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'show_play_icon' => 'yes',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_lightbox_style',
            [
                'label' => __('Lightbox'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'show_image_overlay' => 'yes',
                    'image_overlay[url]!' => '',
                    'lightbox' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'lightbox_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '#gmt-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'lightbox_ui_color',
            [
                'label' => __('UI Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '#gmt-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'lightbox_ui_color_hover',
            [
                'label' => __('UI Hover Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '#gmt-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
                ],
                'separator' => 'after',
            ]
        );

        $this->addControl(
            'lightbox_video_width',
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

        $this->addControl(
            'lightbox_content_position',
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

        $this->addResponsiveControl(
            'lightbox_content_animation',
            [
                'label' => __('Entrance Animation'),
                'type' => Controls::ANIMATION,
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render video widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $video_url = $settings[ $settings['video_type'] . '_url' ];

        if ('other' === $settings['video_type']) {
            $video_url = $this->getExternalVideoUrl();
        }

        if (empty($video_url)) {
            return;
        }

        if ('other' === $settings['video_type']) {
            ob_start();

            $this->renderExternalVideo();

            $video_html = ob_get_clean();
        } else {
            $embed_params = $this->getEmbedParams();

            $embed_options = $this->getEmbedOptions();

            $video_html = Embed::getEmbedHtml($video_url, $embed_params, $embed_options);
        }

        if (empty($video_html)) {
            echo $video_url;

            return;
        }

        $this->addRenderAttribute('video-wrapper', 'class', 'gmt-wrapper');

        if (! $settings['lightbox']) {
            $this->addRenderAttribute('video-wrapper', 'class', 'gmt-fit-aspect-ratio');
        }

        $this->addRenderAttribute('video-wrapper', 'class', 'gmt-open-' . ($settings['lightbox'] ? 'lightbox' : 'inline')); ?>
		<div <?= $this->getRenderAttributeString('video-wrapper'); ?>>
			<?php
            if (! $settings['lightbox']) {
                echo $video_html; // XSS ok.
            }

        if ($this->hasImageOverlay()) {
            $this->addRenderAttribute('image-overlay', 'class', 'gmt-custom-embed-image-overlay');

            if ($settings['lightbox']) {
                if ('other' === $settings['video_type']) {
                    $lightbox_url = $video_url;
                } else {
                    $lightbox_url = Embed::getEmbedUrl($video_url, $embed_params, $embed_options);
                }

                $lightbox_options = [
                        'type' => 'video',
                        'videoType' => $settings['video_type'],
                        'url' => $lightbox_url,
                        'modalOptions' => [
                            'id' => 'gmt-lightbox-' . $this->getId(),
                            'entranceAnimation' => $settings['lightbox_content_animation'],
                            'entranceAnimation_tablet' => $settings['lightbox_content_animation_tablet'],
                            'entranceAnimation_mobile' => $settings['lightbox_content_animation_mobile'],
                            'videoAspectRatio' => $settings['aspect_ratio'],
                        ],
                    ];

                if ('other' === $settings['video_type']) {
                    $lightbox_options['videoParams'] = $this->getExternalParams();
                }

                $this->addRenderAttribute('image-overlay', [
                        'data-gmt-open-lightbox' => 'yes',
                        'data-gmt-lightbox' => \Zend_Json::encode($lightbox_options),
                    ]);

                if (StaticObjectManager::get(Editor::class)->isEditMode()) {
                    $this->addRenderAttribute('image-overlay', [
                            'class' => 'gmt-clickable',
                        ]);
                }
            } else {
                $this->addRenderAttribute('image-overlay', 'style', 'background-image: url(' . ImageSize::getAttachmentImageSrc($settings['image_overlay']['id'], 'image_overlay', $settings) . ');');
            } ?>
				<div <?= $this->getRenderAttributeString('image-overlay'); ?>>
					<?php if ($settings['lightbox']) : ?>
						<?= ImageSize::getAttachmentImageHtml($settings, 'image_overlay'); ?>
					<?php endif; ?>
					<?php if ('yes' === $settings['show_play_icon']) : ?>
						<div class="gmt-custom-embed-play" role="button">
                            <i class="far fa-play-circle"></i>
							<span class="gmt-screen-only"><?= __('Play Video'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php
        } ?>
		</div>
		<?php
    }

    /**
     * Render video widget as plain content.
     *
     * Override the default behavior, by printing the video URL insted of rendering it.
     *
     */
    public function renderPlainContent()
    {
        $settings = $this->getSettingsForDisplay();

        if ('other' !== $settings['video_type']) {
            $url = $settings[ $settings['video_type'] . '_url' ];
        } else {
            $url = $this->getExternalVideoUrl();
        }

        echo $url;
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

        if ($settings['autoplay'] && ! $this->hasImageOverlay()) {
            $params['autoplay'] = '1';
        }

        $params_dictionary = [];

        if ('youtube' === $settings['video_type']) {
            $params_dictionary = [
                'loop',
                'controls',
                'mute',
                'rel',
                'modestbranding',
            ];

            if ($settings['loop']) {
                $video_properties = Embed::getVideoProperties($settings['youtube_url']);

                $params['playlist'] = $video_properties['video_id'];
            }

            $params['start'] = $settings['start'];

            $params['end'] = $settings['end'];

            $params['wmode'] = 'opaque';
        } elseif ('vimeo' === $settings['video_type']) {
            $params_dictionary = [
                'loop',
                'mute' => 'muted',
                'vimeo_title' => 'title',
                'vimeo_portrait' => 'portrait',
                'vimeo_byline' => 'byline',
            ];

            $params['color'] = str_replace('#', '', $settings['color']);

            $params['autopause'] = '0';
        } elseif ('dailymotion' === $settings['video_type']) {
            $params_dictionary = [
                'controls',
                'mute',
                'showinfo' => 'ui-start-screen-info',
                'logo' => 'ui-logo',
            ];

            $params['ui-highlight'] = str_replace('#', '', $settings['color']);

            $params['start'] = $settings['start'];

            $params['endscreen-enable'] = '0';
        }

        foreach ($params_dictionary as $key => $param_name) {
            $setting_name = $param_name;

            if (is_string($key)) {
                $setting_name = $key;
            }

            $setting_value = $settings[ $setting_name ] ? '1' : '0';

            $params[ $param_name ] = $setting_value;
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
    protected function hasImageOverlay()
    {
        $settings = $this->getSettingsForDisplay();

        return ! empty($settings['image_overlay']['url']) && 'yes' === $settings['show_image_overlay'];
    }


    private function getEmbedOptions()
    {
        $settings = $this->getSettingsForDisplay();

        $embed_options = [];

        if ('youtube' === $settings['video_type']) {
            $embed_options['privacy'] = $settings['yt_privacy'];
        } elseif ('vimeo' === $settings['video_type']) {
            $embed_options['start'] = $settings['start'];
        }

        $embed_options['lazy_load'] = ! empty($settings['lazy_load']);

        return $embed_options;
    }


    private function getExternalParams()
    {
        $settings = $this->getSettingsForDisplay();

        $video_params = [];

        foreach ([ 'autoplay', 'loop', 'controls' ] as $option_name) {
            if ($settings[ $option_name ]) {
                $video_params[ $option_name ] = '';
            }
        }

        if ($settings['mute']) {
            $video_params['muted'] = 'muted';
        }

        if (! $settings['download_button']) {
            $video_params['controlsList'] = 'nodownload';
        }

        if ($settings['poster']['url']) {
            $video_params['poster'] = $settings['poster']['url'];
        }

        return $video_params;
    }

    /**
     * @return string
     */
    private function getExternalVideoUrl()
    {
        $settings = $this->getSettingsForDisplay();

        if (! empty($settings['insert_url'])) {
            $video_url = $settings['external_url']['url'];
        }

        if (empty($video_url)) {
            return '';
        }

        if ($settings['start'] || $settings['end']) {
            $video_url .= '#t=';
        }

        if ($settings['start']) {
            $video_url .= $settings['start'];
        }

        if ($settings['end']) {
            $video_url .= ',' . $settings['end'];
        }

        return $video_url;
    }

    /**
     *
     */
    private function renderExternalVideo()
    {
        $video_url = $this->getExternalVideoUrl();
        if (empty($video_url)) {
            return;
        }

        $video_params = $this->getExternalParams(); ?>
		<video class="gmt-video" src="<?= $video_url; ?>" <?= Utils::renderHtmlAttributes($video_params); ?>></video>
		<?php
    }
}
