<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Goomento\PageBuilder\Api\SampleImporterInterface;
use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\PageBuilder\Helper\RegistryHelper;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Goomento\PageBuilder\SampleImport\SampleImport;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;

class SampleImporter extends AbstractAction implements HttpGetActionInterface
{
    use TraitHttpPage;

    const ADMIN_RESOURCE = 'Goomento_PageBuilder::import';
    /**
     * @var SampleImport
     */
    private $sampleImport;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var SampleImporterInterface
     */
    private $sampleImporter;

    /**
     * @param Context $context
     * @param SampleImport $sampleImport
     * @param SampleImporterInterface $sampleImporter
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        SampleImport $sampleImport,
        SampleImporterInterface $sampleImporter,
        Logger $logger
    )
    {
        $this->sampleImport = $sampleImport;
        $this->logger = $logger;
        $this->sampleImporter = $sampleImporter;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $allSamples = $this->sampleImport->getAllSamples();
            RegistryHelper::register('all_import_samples', $allSamples);
            $isImport = (bool) $this->getRequest()->getParam('import');
            if ($isImport === true) {
                $sample = (string) $this->getRequest()->getParam('sample');
                $fileName = (string) $this->getRequest()->getParam('filename');
                $sampleModel = $allSamples[$sample] ?? null;
                if ($sampleModel) {
                    $importedData = $this->sampleImporter
                        ->setSampleImport($sampleModel)
                        ->import(!empty($fileName) ? $fileName : null);
                    foreach ($importedData as $datum) {
                        $this->messageManager->addSuccess(
                            sprintf(
                                'Imported: %s <u><a href="%s" target="_blank">Edit</a></u> | <u><a target="_blank" href="%s">View</a></u>',
                                $datum['title'],
                                $datum['edit_url'],
                                $datum['url']
                            )
                        );
                    }
                }
            } else {
                return $this->renderPage();
            }

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when importing templates/samples.')
            );
        }

        return $this->resultRedirectFactory->create()->setUrl(
            '*/*/sampleImporter'
        );
    }

    /**
     * @inheritDoc
     */
    protected function getPageConfig()
    {
        return [
            'title' => __('Import Templates/Samples'),
            'active_menu' => 'Goomento_PageBuilder::import',
        ];
    }
}
