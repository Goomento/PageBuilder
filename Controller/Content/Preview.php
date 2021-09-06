<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticRegistry;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Preview
 * @package Goomento\PageBuilder\Controller\Editor
 */
class Preview extends View
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $this->validateToken();
            Hooks::doAction('pagebuilder/content/preview');
            StaticRegistry::register('current_preview_content', $this->getContent(true));
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
     * @inheritdoc
     */
    protected function getPageConfig()
    {
        return [
            'editable_title' => 'Preview: %1'
        ];
    }
}
