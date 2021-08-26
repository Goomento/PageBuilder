<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Controller\AbstractAction;
use Goomento\PageBuilder\Helper\StaticAccessToken;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Preview
 * @package Goomento\PageBuilder\Controller\Editor
 */
class View extends AbstractAction
{
    use TraitHttpPage;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $this->validateToken();
            Hooks::doAction('pagebuilder/content/view');

            $this->registry->register('pagebuilder_content', $this->getContent(true));
            return $this->renderPage();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when render content view.')
            );
        }

        return $this->redirect404Page();
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    protected function validateToken()
    {
        $content = $this->getContent(true);
        $token = $this->getRequest()->getParam(StaticAccessToken::ACCESS_TOKEN_PARAM);
        $isValid = StaticAccessToken::isAllowed($token, $content);

        if ($isValid !== true) {
            throw new LocalizedException(
                __('Invalid access token.')
            );
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function getPageConfig()
    {
        return [
            'editable_title' => '%1',
        ];
    }
}
