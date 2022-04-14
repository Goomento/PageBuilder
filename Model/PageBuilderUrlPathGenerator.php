<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Filter\FilterManager;

class PageBuilderUrlPathGenerator
{
    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @param FilterManager $filterManager
     */
    public function __construct(
        FilterManager $filterManager
    ) {
        $this->filterManager = $filterManager;
    }

    /**
     * @param ContentInterface $content
     *
     * @return string
     */
    public function getUrlPath(ContentInterface $content)
    {
        return $content->getIdentifier();
    }

    /**
     * Get canonical product url path
     *
     * @param ContentInterface $content
     * @return string
     */
    public function getCanonicalUrlPath(ContentInterface $content)
    {
        return 'pagebuilder/content/published/content_id/' . $content->getId();
    }

    /**
     * Generate page url key based on url_key entered by merchant or page title
     *
     * @param ContentInterface $content
     * @return string
     */
    public function generateUrlKey(ContentInterface $content)
    {
        $urlKey = $content->getIdentifier();
        return $this->filterManager->translitUrl($urlKey === '' || $urlKey === null ? $content->getTitle() : $urlKey);
    }
}
