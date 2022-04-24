<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Framework\Controller\ResultInterface;

use Exception;
use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Goomento\PageBuilder\Model\Response\HtmlModifier;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class ModifyResponse
{
    /**
     * @var HtmlModifier
     */
    private $htmlModifier;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @param HtmlModifier $htmlModifier
     * @param Data $dataHelper
     * @param Logger $logger
     */
    public function __construct(
        HtmlModifier $htmlModifier,
        Data $dataHelper,
        Logger $logger
    )
    {
        $this->htmlModifier = $htmlModifier;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
    }

    /**
     * Modify the HTML output in order to make it faster to Page Builder.
     *
     * @param ResultInterface $subject
     * @param mixed $result
     * @param ResponseHttp $response
     * @return mixed
     */
    public function afterRenderResult(ResultInterface $subject, ResultInterface $result, ResponseInterface $response)
    {
        try {
            if ($this->dataHelper->addResourceGlobally() || ThemeHelper::hasContentOnPage()) {
                $body = $response->getBody();
                if (!empty($body)) {
                    $html = $this->htmlModifier->modify($body);
                    if ($body !== $html) {
                        $response->setBody($html);
                    }
                }
            }

        } catch (Exception $e) {
            $this->logger->error($e);
        }

        return $result;
    }
}
