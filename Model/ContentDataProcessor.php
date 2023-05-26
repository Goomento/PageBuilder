<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\Exception\LocalizedException;

class ContentDataProcessor
{
    /**
     * @var array
     */
    private $cached = [];
    /**
     * @var array
     */
    private $settings = [];
    /**
     * @var string[]
     */
    private $elements = [];
    /**
     * @var BuildableContentInterface[]
     */
    private $isProcessing = [];

    /**
     * @param BuildableContentInterface $content
     * @return string
     * @throws LocalizedException
     */
    private function getContentHtml(BuildableContentInterface $content) : string
    {
        $key = $content->getUniqueIdentity();

        foreach ($this->isProcessing as $model) {
            if ($model->getUniqueIdentity() === $key) {
                throw new LocalizedException(
                    __('Page Builder renderer looping detected')
                );
            }
        }

        PageBuilder::initialize();

        array_unshift($this->isProcessing, $content);

        $html = HooksHelper::applyFilters('pagebuilder/content/html', $content)->getResult();

        array_shift($this->isProcessing);

        return $html;
    }

    /**
     * @param BuildableContentInterface $content
     * @return string
     * @throws LocalizedException
     */
    public function getHtml(BuildableContentInterface $content) : string
    {
        $key = $content->getUniqueIdentity();

        if (!isset($this->cached[$key])) {
            $this->cached[$key] = $this->getContentHtml($content);
        }

        return (string) $this->cached[$key];
    }

    /**
     * @param BuildableContentInterface $content
     * @param array $data
     * @return string
     */
    public function getElementHtml(BuildableContentInterface $content, array $data) : string
    {
        $key = EncryptorHelper::uniqueStringId(DataHelper::encode($data));

        if (!isset($this->elements[$key])) {

            PageBuilder::initialize();

            $this->elements[$key] = HooksHelper::applyFilters(
                'pagebuilder/document/render_element',
                $content,
                $data
            )->getResult();
        }

        return (string) $this->elements[$key];
    }

    /**
     * Get settings with default config
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function getSettingsForDisplay(array $data) : array
    {
        if (!isset($data['elType'])) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new Exception('Missing element type for getting settings.');
        }

        $key = EncryptorHelper::uniqueStringId(DataHelper::encode($data));

        if (!isset($this->settings[$key])) {

            PageBuilder::initialize();

            $this->settings[$key] = HooksHelper::applyFilters('pagebuilder/elements/parse_settings_for_display', $data)->getResult();
        }

        return (array) $this->settings[$key];
    }
}
