<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Controller\AbstractAction;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\Exception\LocalizedException;

/**
 * For client view purpose, therefore, there is all published content will be rendered
 * This page will be cached regarding the PFC configuration
 */
class Published extends AbstractAction
{
    use TraitHttpPage;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $this->registry->register('pagebuilder_content', $this->getContent(true));
            HooksHelper::doAction('pagebuilder/content/published');
            return $this->renderPage();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when render content view.')
            );
        } finally {
            if (!empty($e)) {
                $this->logger->error($e);
            }
        }

        return $this->redirect404Page();
    }

    /**
     * @inheritDoc
     */
    protected function getPageConfig()
    {
        $content = $this->getContent(true);
        $layout = $content->getSetting('layout') ?: 'pagebuilder_content_fullwidth';

        return [
            'handler' => $layout,
        ];
    }
}
