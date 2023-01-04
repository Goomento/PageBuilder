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

class FacebookLike extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    const NAME = 'facebook_like';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/facebook_like.phtml';

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-thumbs-up';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'facebook', 'like', 'comment', 'share', 'recommend'];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Facebook Like');
    }

    /**
     * Share facebook like interface
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerFbLikeInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'like',
                'options' => [
                    'like' => __('Like'),
                    'recommend' => __('Recommend'),
                    'comment' => __('Comment'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'layout',
            [
                'label' => __('Layout'),
                'type' => Controls::SELECT,
                'default' => 'standard',
                'options' => [
                    'standard' => __('Standard'),
                    'button' => __('Button'),
                    'button_count' => __('Button Count'),
                    'box_count' => __('Box Count'),
                ],
                'condition' => [
                    $prefix . 'type!' => 'comment'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'size',
            [
                'label' => __('Size'),
                'type' => Controls::SELECT,
                'default' => 'small',
                'options' => [
                    'small' => __('Small'),
                    'large' => __('Large'),
                ],
                'condition' => [
                    $prefix . 'type!' => 'comment'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'colorscheme',
            [
                'label' => __('Color Scheme'),
                'type' => Controls::SELECT,
                'default' => 'light',
                'options' => [
                    'light' => __('Light'),
                    'dark' => __('Dark'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'show_share',
            [
                'label' => __('Share Button'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'condition' => [
                    $prefix . 'type!' => 'comment'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'show_faces',
            [
                'label' => __('Faces'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'condition' => [
                    $prefix . 'type!' => 'comment'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'num_posts',
            [
                'label' => __('Number Of Comments'),
                'type' => Controls::NUMBER,
                'default' => 5,
                'description' => __('The number of comments to show by default. The minimum value is 1.'),
                'condition' => [
                    $prefix . 'type' => 'comment'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'order_by',
            [
                'label' => __('Order By'),
                'description' => __('The order to use when displaying comments.'),
                'type' => Controls::SELECT,
                'default' => 'reverse-time',
                'options' => [
                    'reverse-time' => __('Reverse Time'),
                    'time' => __('Time'),
                ],
                'condition' => [
                    $prefix . 'type' => 'comment'
                ]
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
                        'min' => 100,
                        'max' => 500,
                    ],
                ],
                'size_units' => [ 'px' ],
            ]
        );

        $widget->addControl(
            $prefix . 'url_id',
            [
                'label' => __('URL ID'),
                'placeholder' => __('https://your-link.com'),
                'default' => 'https://goomento.com/',
                'description' => __('The URL of the webpage that will be liked.'),
                'separator' => 'before',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'facebook_like_section',
            [
                'label' => __('Like Button'),
            ]
        );

        self::registerFbLikeInterface($this);

        $this->endControlsSection();
    }
}
