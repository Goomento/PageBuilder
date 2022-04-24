<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSizeGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\DataHelper;

class Testimonial extends AbstractWidget
{

    const NAME = 'testimonial';

    protected $template = 'Goomento_PageBuilder::widgets/testimonial.phtml';

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
    public function getTitle()
    {
        return __('Testimonial');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fa fa-comments-o far fa-comments';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'testimonial', 'blockquote' ];
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_testimonial',
            [
                'label' => __('Testimonial'),
            ]
        );

        $this->addControl(
            'testimonial_content',
            [
                'label' => __('Content'),
                'type' => Controls::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'rows' => '10',
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
            ]
        );

        $this->addControl(
            'testimonial_image',
            [
                'label' => __('Choose Image'),
                'type' => Controls::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => DataHelper::getPlaceholderImageSrc(),
                ],
            ]
        );

        $this->addGroupControl(
            ImageSizeGroup::NAME,
            [
                'name' => 'testimonial_image',
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->addControl(
            'testimonial_name',
            [
                'label' => __('Name'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'John Doe',
            ]
        );

        $this->addControl(
            'testimonial_job',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'Designer',
            ]
        );

        $this->addControl(
            'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com'),
            ]
        );

        $this->addControl(
            'testimonial_image_position',
            [
                'label' => __('Image Position'),
                'type' => Controls::SELECT,
                'default' => 'aside',
                'options' => [
                    'aside' => __('Aside'),
                    'top' => __('Top'),
                ],
                'condition' => [
                    'testimonial_image[url]!' => '',
                ],
                'separator' => 'before',
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'testimonial_alignment',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left'    => [
                        'title' => __('Left'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'label_block' => false,
                'style_transfer' => true,
            ]
        );

        $this->endControlsSection();

        // Content.
        $this->startControlsSection(
            'section_style_testimonial_content',
            [
                'label' => __('Content'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'content_content_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-testimonial-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name' => 'content_typography',
                'scheme' => Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .gmt-testimonial-content',
            ]
        );

        $this->endControlsSection();

        // Image.
        $this->startControlsSection(
            'section_style_testimonial_image',
            [
                'label' => __('Image'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'testimonial_image[url]!' => '',
                ],
            ]
        );

        $this->addControl(
            'image_size',
            [
                'label' => __('Image Size'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-testimonial-wrapper .gmt-testimonial-image img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .gmt-testimonial-wrapper .gmt-testimonial-image img',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-testimonial-wrapper .gmt-testimonial-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        // Name.
        $this->startControlsSection(
            'section_style_testimonial_name',
            [
                'label' => __('Name'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'name_text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-testimonial-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name' => 'name_typography',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .gmt-testimonial-name',
            ]
        );

        $this->endControlsSection();

        // Job.
        $this->startControlsSection(
            'section_style_testimonial_job',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'job_text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_2,
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-testimonial-job' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name' => 'job_typography',
                'scheme' => Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .gmt-testimonial-job',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
        <#
        var image = {
            id: settings.testimonial_image.id,
            url: settings.testimonial_image.url,
            size: settings.testimonial_image_size,
            dimension: settings.testimonial_image_custom_dimension,
            model: view.getEditModel()
            };
            var imageUrl = false, hasImage = '';

            if ( '' !== settings.testimonial_image.url ) {
            imageUrl = goomento.imagesManager.getImageUrl( image );
            hasImage = ' gmt-has-image';

            var imageHtml = '<img src="' + imageUrl + '" alt="testimonial" />';
            if ( settings.link.url ) {
                imageHtml = '<a href="' + settings.link.url + '">' + imageHtml + '</a>';
            }
        }

        var testimonial_alignment = settings.testimonial_alignment ? ' gmt-testimonial-text-align-' + settings.testimonial_alignment : '';
        var testimonial_image_position = settings.testimonial_image_position ? ' gmt-testimonial-image-position-' + settings.testimonial_image_position : '';
        #>
        <div class="gmt-testimonial-wrapper{{ testimonial_alignment }}">
            <# if ( '' !== settings.testimonial_content ) {
            view.addRenderAttribute( 'testimonial_content', 'class', 'gmt-testimonial-content' );

            view.addInlineEditingAttributes( 'testimonial_content' );
            #>
            <div {{{ view.getRenderAttributeString( 'testimonial_content' ) }}}>{{{ settings.testimonial_content }}}</div>
        <# } #>
        <div class="gmt-testimonial-meta{{ hasImage }}{{ testimonial_image_position }}">
            <div class="gmt-testimonial-meta-inner">
                <# if ( imageUrl ) { #>
                <div class="gmt-testimonial-image">{{{ imageHtml }}}</div>
                <# } #>

                <div class="gmt-testimonial-details">
                    <# if ( '' !== settings.testimonial_name ) {
                    view.addRenderAttribute( 'testimonial_name', 'class', 'gmt-testimonial-name' );

                    view.addInlineEditingAttributes( 'testimonial_name', 'none' );

                    var testimonialNameHtml = settings.testimonial_name;
                    if ( settings.link.url ) {
                    testimonialNameHtml = '<a href="' + settings.link.url + '">' + testimonialNameHtml + '</a>';
                    }
                    #>
                    <div {{{ view.getRenderAttributeString( 'testimonial_name' ) }}}>{{{ testimonialNameHtml }}}</div>
                <# } #>

                <# if ( '' !== settings.testimonial_job ) {
                view.addRenderAttribute( 'testimonial_job', 'class', 'gmt-testimonial-job' );

                view.addInlineEditingAttributes( 'testimonial_job', 'none' );

                var testimonialJobHtml = settings.testimonial_job;
                if ( settings.link.url ) {
                testimonialJobHtml = '<a href="' + settings.link.url + '">' + testimonialJobHtml + '</a>';
                }
                #>
                <div {{{ view.getRenderAttributeString( 'testimonial_job' ) }}}>{{{ testimonialJobHtml }}}</div>
            <# } #>
            </div>
            </div>
        </div>
        </div>
        <?php
    }
}
