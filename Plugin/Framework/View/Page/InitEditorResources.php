<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Framework\View\Page;

use Exception;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\RendererInterface;

class InitEditorResources
{
    /**
     * @var Config
     */
    private $pageConfig;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Config $pageConfig
     * @param Logger $logger
     */
    public function __construct(
        Config $pageConfig,
        Logger $logger
    ) {
        $this->pageConfig = $pageConfig;
        $this->logger = $logger;
    }

    /**
     * @param RendererInterface|null $subject
     * @param $result
     * @return string
     */
    public function afterRenderHeadContent(RendererInterface $subject, $result) : string
    {
        try {
            if (HooksHelper::didAction('pagebuilder/editor/index') ||
                HooksHelper::didAction('pagebuilder/editor/render_widget')) {
                $result = $subject->renderMetadata();
                $result .= $subject->renderTitle();
                $result .= $this->pageConfig->getIncludes();
            }
        } catch (Exception $e) {
            $this->logger->error($e);
        }
        return $result;
    }
}
