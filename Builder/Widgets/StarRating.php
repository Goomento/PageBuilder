<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class StarRating extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'start-rating';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/star_rating.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Star Rating');
    }

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-star-half-alt';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'star', 'rating', 'rate', 'review' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerStarRatingInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'rating_scale',
            [
                'label' => __('Rating Scale'),
                'type' => Controls::SELECT,
                'options' => [
                    '5' => '0-5',
                    '10' => '0-10',
                ],
                'default' => '5',
            ]
        );

        $widget->addControl(
            $prefix . 'rating',
            [
                'label' => __('Rating'),
                'type' => Controls::NUMBER,
                'min' => 0,
                'max' => 10,
                'step' => 0.1,
                'default' => 5,
            ]
        );

        $widget->addControl(
            $prefix . 'star_style',
            [
                'label' => __('Icon'),
                'type' => Controls::SELECT,
                'options' => [
                    'star_fontawesome' => 'Font Awesome',
                    'star_unicode' => 'Unicode',
                ],
                'default' => 'star_fontawesome',
                'render_type' => 'template',
                'prefix_class' => 'gmt--star-style-',
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'unmarked_star_style',
            [
                'label' => __('Unmarked Style'),
                'type' => Controls::CHOOSE,
                'label_block' => false,
                'options' => [
                    'solid' => [
                        'title' => __('Solid'),
                        'icon' => 'fas fa-star',
                    ],
                    'outline' => [
                        'title' => __('Outline'),
                        'icon' => 'far fa-star',
                    ],
                ],
                'default' => 'solid',
            ]
        );

        $widget->addControl(
            $prefix . 'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'separator' => 'before'
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'align',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'prefix_class' => 'gmt-star-rating%s--align-',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}}',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerStarRatingTitleStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-star-rating__title'
    ) {
        $widget->addControl(
            $prefix . 'title_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}}',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'title_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'title_gap',
            [
                'label' => __('Gap'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}}:not(.gmt-star-rating--align-justify) ' . $cssTarget => 'margin-right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}}:not(.gmt-star-rating--align-justify) ' . $cssTarget => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerStarRatingIconStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-star-rating'
    ) {
        $widget->addResponsiveControl(
            $prefix . 'icon_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_space',
            [
                'label' => __('Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} ' . $cssTarget . ' i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} ' . $cssTarget . ' i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'stars_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i:before' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'stars_unmarked_color',
            [
                'label' => __('Unmarked Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i' => 'color: {{VALUE}}',
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
            'section_rating',
            [
                'label' => __('Rating'),
            ]
        );

        self::registerStarRatingInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_title!' => '',
                ],
            ]
        );

        self::registerStarRatingTitleStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_stars_style',
            [
                'label' => __('Stars'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerStarRatingIconStyle($this);

        $this->endControlsSection();
    }

    /**
     * @return array
     */
    public function getRating()
    {
        $settings = $this->getSettingsForDisplay();
        $ratingScale = (int) $settings['start-rating_rating_scale'];
        $rating = (float) $settings['start-rating_rating'] > $ratingScale ? $ratingScale : $settings['start-rating_rating'];

        return [ $rating, $ratingScale ];
    }

    /**
     * Print the actual stars and calculate their filling.
     *
     * Rating type is float to allow stars-count to be a fraction.
     * Floored-rating type is int, to represent the rounded-down stars count.
     * In the `for` loop, the index type is float to allow comparing with the rating value.
     *
     */
    public function renderStars($icon)
    {
        $ratingData = $this->getRating();
        $rating = (float) $ratingData[0];
        $flooredRating = floor($rating);
        $starsHtml = '';

        for ($stars = 1.0; $stars <= $ratingData[1]; $stars++) {
            if ($stars <= $flooredRating) {
                $starsHtml .= '<i class="gmt-star-full">' . $icon . '</i>';
            } elseif ($flooredRating + 1 === $stars && $rating !== $flooredRating) {
                $starsHtml .= '<i class="gmt-star-' . ($rating - $flooredRating) * 10 . '">' . $icon . '</i>';
            } else {
                $starsHtml .= '<i class="gmt-star-empty">' . $icon . '</i>';
            }
        }

        return $starsHtml;
    }
}
