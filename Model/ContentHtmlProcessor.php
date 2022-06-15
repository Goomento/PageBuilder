<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Helper\HooksHelper;
use Magento\Framework\Exception\LocalizedException;

class ContentHtmlProcessor
{
    /**
     * @var array
     */
    private $cached = [];

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
                    __('Page Builder renderer lopping detected')
                );
            }
        }

        array_unshift($this->isProcessing, $content);

        /** @var BuildableContentInterface $result */
        $result = HooksHelper::applyFilters('pagebuilder/content/html', $content);

        array_shift($this->isProcessing);

        return $result->getRenderContent();
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
}
