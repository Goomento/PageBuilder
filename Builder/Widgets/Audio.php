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

class Audio extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'audio';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/audio.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('SoundCloud');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-headphones';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'audio', 'player', 'soundcloud', 'embed' ];
    }

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerSoundCloudInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'default' => [
                    'url' => 'https://soundcloud.com/cloudkid/confetti-ghost',
                ],
                'show_external' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'sc_visual',
            [
                'label' => __('Visual Player'),
                'type' => Controls::SELECT,
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'sc_options',
            [
                'label' => __('Additional Options'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_auto_play',
            [
                'label' => __('Autoplay'),
                'type' => Controls::SWITCHER,
            ]
        );

        $widget->addControl(
            $prefix . 'sc_buying',
            [
                'label' => __('Buy Button'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_liking',
            [
                'label' => __('Like Button'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_download',
            [
                'label' => __('Download Button'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_show_artwork',
            [
                'label' => __('Artwork'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'condition' => [
                    $prefix . 'sc_visual' => 'no',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'sc_sharing',
            [
                'label' => __('Share Button'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_show_comments',
            [
                'label' => __('Comments'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_show_playcount',
            [
                'label' => __('Play Counts'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_show_user',
            [
                'label' => __('Username'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'sc_color',
            [
                'label' => __('Controls Color'),
                'type' => Controls::COLOR,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_audio',
            [
                'label' => __('SoundCloud'),
            ]
        );

        self::registerSoundCloudInterface($this);

        $this->endControlsSection();
    }
}
