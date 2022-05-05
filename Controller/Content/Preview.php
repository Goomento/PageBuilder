<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Helper\HooksHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * For Editor Preview purpose, therefore, there is no content should be rendered
 * This page will not be cached
 */
class Preview extends View
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            // Set current model
            $this->registry->register('current_preview_content', $this->getContent(true));

            HooksHelper::doAction('pagebuilder/content/preview');
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
                if ($this->dataHelper->isDebugMode()) {
                    throw $e;
                }
            }
        }

        return $this->redirect404Page();
    }

    /**
     * @inheritdoc
     */
    protected function getPageConfig()
    {
        $layout = $this->getRequest()->getParam('layout', 'pagebuilder_content_1column');

        return [
            'editable_title' => 'Preview: %1',
            'handler' => $layout,
        ];
    }
}
