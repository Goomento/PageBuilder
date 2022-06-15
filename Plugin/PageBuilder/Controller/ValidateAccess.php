<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\PageBuilder\Controller;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Helper\Content;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class ValidateAccess
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var Content
     */
    private $contentHelper;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ManagerInterface $messageManager
     * @param Content $contentHelper
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        ManagerInterface $messageManager,
        Content $contentHelper
    )
    {
        $this->request = $request;
        $this->response = $response;
        $this->contentHelper = $contentHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * @param ActionInterface $subject
     * @param RequestInterface $request
     * @return RequestInterface[]
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    )
    {
        try {
            $contentId = (int) $this->request->getParam(ContentInterface::CONTENT_ID);
            if (empty($contentId)) {
                throw new LocalizedException(
                    __('Invalid content Id')
                );
            }

            $content = $this->contentHelper->get($contentId);

            if (!$content || !$content->getId()) {
                throw new LocalizedException(
                    __('Invalid content Id')
                );
            }

            $token = (string) $this->request->getParam(EncryptorHelper::ACCESS_TOKEN);

            if (EncryptorHelper::isAllowed($token, $content) !== true) {
                throw new LocalizedException(
                    __('Invalid access token.')
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong.')
            );
        } finally {
            if (!empty($e)) {
                // Stop dispatching
                $subject->getActionFlag()->set('', $subject::FLAG_NO_DISPATCH, true);
                $this->response->setRedirect(
                    UrlBuilderHelper::getUrl('noroute')
                );
            }
        }

        return [$request];
    }
}
