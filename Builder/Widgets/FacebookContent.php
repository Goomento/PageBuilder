<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

class FacebookContent extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    const NAME = 'facebook_content';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/facebook_content.phtml';

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fab fa-facebook';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'facebook', 'content', 'page', 'fan page', 'embed'];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Facebook Content');
    }

    /**
     * Share facebook like interface
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerFbContentInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'page',
                'options' => [
                    'page' => __('Page'),
                    'post' => __('Post'),
                    'video' => __('Video'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'tabs',
            [
                'label' => __('Layout'),
                'type' => Controls::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'default' => [
                    'timeline',
                ],
                'options' => [
                    'timeline' => __('Timeline'),
                    'events' => __('Events'),
                    'messages' => __('Messages'),
                ],
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'small_header',
            [
                'label' => __('Small Header'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'show_cover',
            [
                'label' => __('Cover Photo'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'show_facepile',
            [
                'label' => __('Profile Photos'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'show_cta',
            [
                'label' => __('Custom CTA Button'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'height',
            [
                'label' => __('Height'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'range' => [
                    'px' => [
                        'min' => 70,
                        'max' => 1000,
                    ],
                ],
                'size_units' => [ 'px' ],
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'width',
            [
                'label' => __('Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'range' => [
                    'px' => [
                        'min' => 180,
                        'max' => 500,
                    ],
                ],
                'size_units' => [ 'px' ],
            ]
        );

        $widget->addControl(
            $prefix . 'show_text',
            [
                'label' => __('Full Post'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'description' => __('Applied to photo post, video.'),
                'condition' => [
                    $prefix . 'type' => [ 'post', 'video' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'video_allowfullscreen',
            [
                'label' => __('Allow Full Screen'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'condition' => [
                    $prefix . 'type' => 'video',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'video_autoplay',
            [
                'label' => __('Autoplay'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'condition' => [
                    $prefix . 'type' => 'video',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'video_show_captions',
            [
                'label' => __('Captions'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'description' => __('By default, captions are only available on desktop'),
                'condition' => [
                    $prefix . 'type' => 'video',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'page_url',
            [
                'label' => __('Page URL'),
                'placeholder' => __('https://your-link.com'),
                'default' => 'https://www.facebook.com/facebook/',
                'description' => __('The URL of the Facebook Page.'),
                'type' => Controls::TEXT,
                'separator' => 'before',
                'condition' => [
                    $prefix . 'type' => 'page',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'post_url',
            [
                'label' => __('Post URL'),
                'placeholder' => __('https://your-link.com'),
                'default' => 'https://www.facebook.com/20531316728/posts/10154009990506729/',
                'description' => __('The absolute URL of the post.'),
                'separator' => 'before',
                'type' => Controls::TEXT,
                'condition' => [
                    $prefix . 'type' => 'post',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'video_url',
            [
                'label' => __('Video URL'),
                'placeholder' => __('https://your-link.com'),
                'default' => 'https://www.facebook.com/facebook/videos/10153231379946729/',
                'description' => __('The absolute URL of the video.'),
                'separator' => 'before',
                'type' => Controls::TEXT,
                'condition' => [
                    $prefix . 'type' => 'video',
                ],
            ]
        );
    }
    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'facebook_content_section',
            [
                'label' => __('Content'),
            ]
        );

        self::registerFbContentInterface($this);

        $this->endControlsSection();
    }
}
