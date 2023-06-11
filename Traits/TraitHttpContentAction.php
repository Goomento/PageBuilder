<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\Core\Traits\TraitHttpAction;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @property ContentInterface $content
 */
trait TraitHttpContentAction
{
    use TraitHttpAction;

    /**
     * @var BuildableContentInterface|null
     */
    protected $content;

    /**
     * @var BuildableContentInterface|null
     */
    protected $contentRevision;

    /**
     * @return BuildableContentInterface|null
     * @throws LocalizedException
     */
    protected function getContent(bool $force = false)
    {
        if (null === $this->content) {
            $this->content = false;
            $contentId = (int) $this->getRequest()->getParam('content_id');
            if ($contentId !== 0) {
                $this->content = BuildableContentHelper::getContent($contentId);
            }
        }

        if (true === $force && !$this->content) {
            throw new LocalizedException(
                __('Page Builder content\'s not found.')
            );
        }

        return $this->content;
    }

    /**
     * @param $inLastRevision
     * @return string|null
     * @throws LocalizedException
     */
    protected function getContentLayout($inLastRevision = false)
    {
        if ($inLastRevision && $this->getContent(true)->getLastRevision(true)) {
            return $this->getContent(true)->getLastRevision()->getSetting('layout');
        } elseif (!$inLastRevision) {
            return $this->getContent(true)->getSetting('layout');
        }

        return null;
    }
}
