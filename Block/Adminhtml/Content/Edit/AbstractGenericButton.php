<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Content\Edit;

use Goomento\PageBuilder\Model\Content;
use Magento\Backend\Block\Widget\Context;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Magento\Framework\App\RequestInterface;

abstract class AbstractGenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Content
     */
    protected $content;
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * @param Context $context
     * @param ContentRegistryInterface $contentRegistry
     */
    public function __construct(
        Context $context,
        ContentRegistryInterface $contentRegistry
    ) {
        $this->context = $context;
        $this->contentRegistry = $contentRegistry;
    }

    /**
     * Get content ID
     *
     * @return int|null
     */
    protected function getContentId()
    {
        return (int) $this->context->getRequest()->getParam('content_id');
    }

    /**
     * Get content type
     *
     * @return string
     */
    protected function getContentType()
    {
        return (string) $this->context->getRequest()->getParam('type');
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest()
    {
        return $this->context->getRequest();
    }

    /**
     * Get content
     */
    protected function getContent()
    {
        if ($this->getContentId() && is_null($this->content)) {
            $this->content = $this->contentRegistry->getById(
                $this->getContentId()
            );
        }
        return $this->content;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    protected function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
