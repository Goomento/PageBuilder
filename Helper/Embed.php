<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

class Embed
{
    /**
     * Provider match masks.
     *
     * Holds a list of supported providers with their URL structure in a regex format.
     *
     * @var array Provider URL structure regex.
     */
    private static $providerMatchMasks = [
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
    private static $embedPatterns = [
        'youtube' => 'https://www.youtube{NO_COOKIE}.com/embed/{VIDEO_ID}?feature=oembed',
        'vimeo' => 'https://player.vimeo.com/video/{VIDEO_ID}',
        'dailymotion' => 'https://dailymotion.com/embed/video/{VIDEO_ID}',
    ];

    /**
     * Get video properties.
     *
     * Retrieve the video properties for a given video URL.
     *
     *
     * @param string $videoUrl Video URL.
     *
     * @return null|array The video properties, or null.
     */
    public static function getVideoProperties($videoUrl)
    {
        foreach (self::$providerMatchMasks as $provider => $matchMask) {
            preg_match($matchMask, $videoUrl, $matches);

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
     * @param string $videoUrl        Video URL.
     * @param array  $embedUrlParams Optional. Embed parameters. Default is an
     *                                 empty array.
     * @param array  $options          Optional. Embed options. Default is an
     *                                 empty array.
     *
     * @return string The video url.
     */
    public static function getEmbedUrl($videoUrl, array $embedUrlParams = [], array $options = [])
    {
        $videoProperties = self::getVideoProperties($videoUrl);

        if (!$videoProperties) {
            return null;
        }

        $embedPattern = self::$embedPatterns[ $videoProperties['provider'] ];

        $replacements = [
            '{VIDEO_ID}' => $videoProperties['video_id'],
        ];

        if ('youtube' === $videoProperties['provider']) {
            $replacements['{NO_COOKIE}'] = ! empty($options['privacy']) ? '-nocookie' : '';
        } elseif ('vimeo' === $videoProperties['provider']) {
            $timeText = '';

            if (!empty($options['start'])) {
                $timeText = date('H\hi\ms\s', $options['start']);
            }

            $replacements['{TIME}'] = $timeText;
        }

        $url = str_replace(array_keys($replacements), $replacements, $embedPattern);

        return RequestHelper::appendQueryStringToUrl($url, $embedUrlParams);
    }

    /**
     * Get embed HTML.
     *
     * Retrieve the final HTML of the embedded URL.
     *
     *
     * @param string $videoUrl        Video URL.
     * @param array  $embedUrlParams Optional. Embed parameters. Default is an
     *                                 empty array.
     * @param array  $options          Optional. Embed options. Default is an
     *                                 empty array.
     * @param array  $frameAttributes Optional. IFrame attributes. Default is an
     *                                 empty array.
     *
     * @return string The embed HTML.
     */
    public static function getEmbedHtml($videoUrl, array $embedUrlParams = [], array $options = [], array $frameAttributes = [])
    {
        $defaultFrameAttributes = [
            'class' => 'gmt-video-iframe',
            'allowfullscreen',
        ];
        $videoEmbedUrl = self::getEmbedUrl($videoUrl, $embedUrlParams, $options);
        if (!$videoEmbedUrl) {
            return null;
        }
        if (!$options['lazy_load']) {
            $defaultFrameAttributes['src'] = $videoEmbedUrl;
        } else {
            $defaultFrameAttributes['data-lazy-load'] = $videoEmbedUrl;
        }

        $frameAttributes = array_merge($defaultFrameAttributes, $frameAttributes);

        $attributesForPrint = [];

        foreach ($frameAttributes as $attributeKey => $attributeValue) {
            $attributeValue = EscaperHelper::escapeHtml($attributeValue);

            if (is_numeric($attributeKey)) {
                $attributesForPrint[] = $attributeValue;
            } else {
                $attributesForPrint[] = sprintf('%1$s="%2$s"', $attributeKey, $attributeValue);
            }
        }

        $attributesForPrint = implode(' ', $attributesForPrint);

        $iframeHtml = "<iframe $attributesForPrint></iframe>";

        return HooksHelper::applyFilters('oembed_result', $iframeHtml, $videoUrl, $frameAttributes)->getResult();
    }
}
