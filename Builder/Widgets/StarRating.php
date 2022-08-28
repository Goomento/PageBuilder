<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
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

        $this->addControl(
            'rating_scale',
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

        $this->addControl(
            'rating',
            [
                'label' => __('Rating'),
                'type' => Controls::NUMBER,
                'min' => 0,
                'max' => 10,
                'step' => 0.1,
                'default' => 5,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'star_style',
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

        $this->addControl(
            'unmarked_star_style',
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

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'separator' => 'before',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addResponsiveControl(
            'align',
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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-star-rating__title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .gmt-star-rating__title',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->addResponsiveControl(
            'title_gap',
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
                    'body:not(.rtl) {{WRAPPER}}:not(.gmt-star-rating--align-justify) .gmt-star-rating__title' => 'margin-right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}}:not(.gmt-star-rating--align-justify) .gmt-star-rating__title' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_stars_style',
            [
                'label' => __('Stars'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'icon_size',
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
                    '{{WRAPPER}} .gmt-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_space',
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
                    'body:not(.rtl) {{WRAPPER}} .gmt-star-rating i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} .gmt-star-rating i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'stars_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-star-rating i:before' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'stars_unmarked_color',
            [
                'label' => __('Unmarked Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-star-rating i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * @return array
     */
    public function getRating()
    {
        $settings = $this->getSettingsForDisplay();
        $ratingScale = (int) $settings['rating_scale'];
        $rating = (float) $settings['rating'] > $ratingScale ? $ratingScale : $settings['rating'];

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

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<#
			var getRating = function() {
				var ratingScale = parseInt( settings.rating_scale, 10 ),
					rating = settings.rating > ratingScale ? ratingScale : settings.rating;

				return [ rating, ratingScale ];
			},
			ratingData = getRating(),
			rating = ratingData[0],
			textualRating = ratingData[0] + '/' + ratingData[1],
			renderStars = function( icon ) {
				var starsHtml = '',
					flooredRating = Math.floor( rating );

				for ( var stars = 1; stars <= ratingData[1]; stars++ ) {
					if ( stars <= flooredRating  ) {
						starsHtml += '<i class="gmt-star-full">' + icon + '</i>';
					} else if ( flooredRating + 1 === stars && rating !== flooredRating ) {
						starsHtml += '<i class="gmt-star-' + ( rating - flooredRating ).toFixed( 1 ) * 10 + '">' + icon + '</i>';
					} else {
						starsHtml += '<i class="gmt-star-empty">' + icon + '</i>';
					}
				}

				return starsHtml;
			},
			icon = '';

			if ( 'star_unicode' === settings.star_style ) {
				icon = '&#9733;';

				if ( 'outline' === settings.unmarked_star_style ) {
					icon = '&#9734;';
				}
			}

			view.addRenderAttribute( 'title', 'class', 'gmt-star-rating__title' );
            view.addInlineEditingAttributes( 'title', 'none' );

			view.addRenderAttribute( 'iconWrapper', 'class', 'gmt-star-rating' );
			view.addRenderAttribute( 'iconWrapper', 'class', 'gmt-star-unmarked-' + settings.unmarked_star_style );
			view.addRenderAttribute( 'iconWrapper', 'itemtype', 'http://schema.org/Rating' );
			view.addRenderAttribute( 'iconWrapper', 'title', textualRating );
			view.addRenderAttribute( 'iconWrapper', 'itemscope', '' );
			view.addRenderAttribute( 'iconWrapper', 'itemprop', 'reviewRating' );

			var stars = renderStars( icon );
		#>

		<div class="gmt-star-rating__wrapper">
			<# if ( ! _.isEmpty( settings.title ) ) { #>
				<div {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</div>
			<# } #>
			<div {{{ view.getRenderAttributeString( 'iconWrapper' ) }}} >
				{{{ stars }}}
				<span itemprop="ratingValue" class="gmt-screen-only">{{ textualRating }}</span>
			</div>
		</div>

		<?php
    }
}
