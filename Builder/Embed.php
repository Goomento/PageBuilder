<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;

class Embed
{

    /**
     * Provider match masks.
     *
     * Holds a list of supported providers with their URL structure in a regex format.
     *
     * @var array Provider URL structure regex.
     */
    private static $provider_match_masks = [
        'youtube' => '/^.*(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/',
        'vimeo' => '/^.*vimeo\.com\/(?:[a-z]*\/)*([‌​0-9]{6,11})[?]?.*/',
        'dailymotion' => '/^.*dailymotion.com\/(?:video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/',
    ];

    /**
     * Embed patterns.
     *
     * Holds a list of supported providers with their embed patters.
     *
     * @var array Embed patters.
     */
    private static $embed_patterns = [
        'youtube' => 'https://www.youtube{NO_COOKIE}.com/embed/{VIDEO_ID}?feature=oembed',
        'vimeo' => 'https://player.vimeo.com/video/{VIDEO_ID}#t={TIME}',
        'dailymotion' => 'https://dailymotion.com/embed/video/{VIDEO_ID}',
    ];

    /**
     * Get video properties.
     *
     * Retrieve the video properties for a given video URL.
     *
     *
     * @param string $video_url Video URL.
     *
     * @return null|array The video properties, or null.
     */
    public static function getVideoProperties($video_url)
    {
        foreach (self::$provider_match_masks as $provider => $match_mask) {
            preg_match($match_mask, $video_url, $matches);

            if ($matches) {
                return [
                    'provider' => $provider,
                    'video_id' => $matches[1],
                ];
            }
        }

        return null;
    }

    /**
     * Get embed URL.
     *
     * Retrieve the embed URL for a given video.
     *
     *
     * @param string $video_url        Video URL.
     * @param array  $embed_url_params Optional. Embed parameters. Default is an
     *                                 empty array.
     * @param array  $options          Optional. Embed options. Default is an
     *                                 empty array.
     *
     * @return null|array The video properties, or null.
     */
    public static function getEmbedUrl($video_url, array $embed_url_params = [], array $options = [])
    {
        $video_properties = self::getVideoProperties($video_url);

        if (!$video_properties) {
            return null;
        }

        $embed_pattern = self::$embed_patterns[ $video_properties['provider'] ];

        $replacements = [
            '{VIDEO_ID}' => $video_properties['video_id'],
        ];

        if ('youtube' === $video_properties['provider']) {
            $replacements['{NO_COOKIE}'] = ! empty($options['privacy']) ? '-nocookie' : '';
        } elseif ('vimeo' === $video_properties['provider']) {
            $time_text = '';

            if (!empty($options['start'])) {
                $time_text = date('H\hi\ms\s', $options['start']);
            }

            $replacements['{TIME}'] = $time_text;
        }

        return str_replace(array_keys($replacements), $replacements, $embed_pattern);
    }

    /**
     * Get embed HTML.
     *
     * Retrieve the final HTML of the embedded URL.
     *
     *
     * @param string $video_url        Video URL.
     * @param array  $embed_url_params Optional. Embed parameters. Default is an
     *                                 empty array.
     * @param array  $options          Optional. Embed options. Default is an
     *                                 empty array.
     * @param array  $frame_attributes Optional. IFrame attributes. Default is an
     *                                 empty array.
     *
     * @return string The embed HTML.
     */
    public static function getEmbedHtml($video_url, array $embed_url_params = [], array $options = [], array $frame_attributes = [])
    {
        $default_frame_attributes = [
            'class' => 'gmt-video-iframe',
            'allowfullscreen',
        ];

        $video_embed_url = self::getEmbedUrl($video_url, $embed_url_params, $options);
        if (!$video_embed_url) {
            return null;
        }
        if (!$options['lazy_load']) {
            $default_frame_attributes['src'] = $video_embed_url;
        } else {
            $default_frame_attributes['data-lazy-load'] = $video_embed_url;
        }

        $frame_attributes = array_merge($default_frame_attributes, $frame_attributes);

        $attributes_for_print = [];

        foreach ($frame_attributes as $attribute_key => $attribute_value) {
            $attribute_value = EscaperHelper::escapeHtml($attribute_value);

            if (is_numeric($attribute_key)) {
                $attributes_for_print[] = $attribute_value;
            } else {
                $attributes_for_print[] = sprintf('%1$s="%2$s"', $attribute_key, $attribute_value);
            }
        }

        $attributes_for_print = implode(' ', $attributes_for_print);

        $iframe_html = "<iframe $attributes_for_print></iframe>";

        return HooksHelper::applyFilters('oembed_result', $iframe_html, $video_url, $frame_attributes);
    }
}
