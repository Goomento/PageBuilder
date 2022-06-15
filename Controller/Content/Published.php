<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
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
            $this->validateContent();
            HooksHelper::doAction('pagebuilder/content/published', $this->getContent(true));
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
        } finally {
            if (!empty($e)) {
                if ($this->dataHelper->isDebugMode()) {
                    throw $e;
                }
            }
        }

        return $this->redirect404Page();
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    private function validateContent() : void
    {
        $content = $this->getContent(true);
        if (!$content->getIsActive() || $content->getStatus() !== BuildableContentInterface::STATUS_PUBLISHED) {
            throw new LocalizedException(
                __('Page Content not found')
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function getPageConfig()
    {
        return [
            'handler' => $this->getContentLayout(false) ?: 'pagebuilder_content_1column',
        ];
    }
}
