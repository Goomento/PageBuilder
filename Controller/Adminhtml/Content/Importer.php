<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Exception;
use Goomento\Core\Traits\TraitHttpExecutable;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Api\ContentImportProcessorInterface;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Editor
 * @package Goomento\PageBuilder\Controller\Adminhtml\Content
 */
class Importer extends AbstractAction implements HttpGetActionInterface, HttpPostActionInterface
{
    use TraitHttpPage;
    use TraitHttpExecutable;

    const FILE_NAME = 'file';

    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Goomento_PageBuilder::content_import';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ContentImportProcessorInterface
     */
    protected $importProcessor;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Import constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ContentImportProcessorInterface $importProcessor
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ContentImportProcessorInterface $importProcessor,
        Logger $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->importProcessor = $importProcessor;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function executePost()
    {
        try {
            if (empty($_FILES[self::FILE_NAME])) {
                throw new LocalizedException(__('Import file must specify.'));
            }

            $data = file_get_contents($_FILES[self::FILE_NAME]['tmp_name']);

            if (!trim($data)) {
                throw new LocalizedException(__('Import file should not empty.'));
            }

            $imported = $this->importProcessor->importOnUpload(self::FILE_NAME);
            $linkHtml = '';
            if (!empty($imported)) {
                $contentLinks = [];
                foreach ($imported as $content) {
                    $contentLinks[] = '<a target="_blank" href="' . $content['edit_url'] . '">' . $content['title'] . '</a>';
                }
                $linkHtml = __('Content(s): %1', implode(', ', $contentLinks));
            }

            $this->messageManager->addSuccess(__('Imported %1 content(s) successfully.' . ' ' . $linkHtml, count($imported)));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(__('Something went wrong when importing template.'));
        }

        /** @var Redirect $resultRedirect */
        $result = $this->resultRedirectFactory->create();
        return $result->setRefererUrl();
    }

    /**
     * @inheritdoc
     */
    protected function executeGet()
    {
        return $this->renderPage();
    }

    /**
     * @inheritdoc
     */
    protected function getPageConfig()
    {
        return [
            'title' => __('Import'),
            'active_menu' => 'Goomento_PageBuilder::import',
        ];
    }
}
