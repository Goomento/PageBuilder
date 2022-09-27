<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

namespace Goomento\PageBuilder\Plugin\Framework\View\Page;

use Exception;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\RendererInterface;

class InitEditorResources
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Repository
     */
    private $assetRepository;
    /**
     * @var Config
     */
    private $pageConfig;

    /**
     * @param Repository $assetRepository
     * @param RequestInterface $request
     * @param Config $pageConfig
     */
    public function __construct(
        Repository $assetRepository,
        RequestInterface $request,
        Config $pageConfig
    )
    {
        $this->assetRepository = $assetRepository;
        $this->pageConfig = $pageConfig;
        $this->request = $request;
    }

    /**
     * @param RendererInterface|null $subject
     * @param $result
     * @return string
     */
    public function afterRenderHeadContent(RendererInterface $subject, $result)
    {
        if (is_string($result)) {
            try {
                if (HooksHelper::didAction('pagebuilder/editor/index') ||
                    HooksHelper::didAction('pagebuilder/editor/render_widget')) {
                    $result = $subject->renderMetadata();
                    $result .= $subject->renderTitle();
                    $result .= $this->pageConfig->getIncludes();
                    $result .= $this->getHtmlHead();
                }
            } catch (Exception $e) {}
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getHtmlHead() : string
    {
        $params = array_merge(['_secure' => $this->request->isSecure()]);
        $requirejsUrl = $this->assetRepository->getUrlWithParams('Goomento_PageBuilder/lib/requirejs/require.min.js', $params);
        $html = sprintf('<script src="%s"></script>', $requirejsUrl);
        $cssPrefix = DataHelper::isCssMinifyFilesEnabled() ? '.min' : '';
        foreach (['css/styles', 'jquery/jstree/themes/default/style', 'Goomento_Core::css/style-m'] as $file) {
            $cssUrl = $this->assetRepository->getUrlWithParams($file . $cssPrefix . '.css', $params);
            $html .= sprintf('<link rel="stylesheet" href="%s" />', $cssUrl);
        }
        $iconUrl = $this->assetRepository->getUrlWithParams('Goomento_Core/images/goomento.ico', $params);
        $html .= sprintf('<link rel="shortcut icon" type="image/x-icon" href="%s" />', $iconUrl);

        return $html;
    }
}
