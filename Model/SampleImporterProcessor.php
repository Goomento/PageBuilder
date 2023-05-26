<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\SampleImportInterface;
use Goomento\PageBuilder\Api\SampleImporterInterface;
use Goomento\PageBuilder\Helper\AssetsHelper;
use Goomento\PageBuilder\Api\ContentImportProcessorInterface;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

// phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
class SampleImporterProcessor implements SampleImporterInterface
{
    /**
     * @var string
     */
    protected $jsonSourceDir;
    /**
     * @var string
     */
    protected $mediaDir;

    /**
     * @var array
     */
    protected $replacements = [];

    /**
     * @var ContentImportProcessorInterface
     */
    protected $importer;

    /**
     * @var State
     */
    protected $state;

    /**
     * ImportProcessor constructor.
     * @param ContentImportProcessorInterface $importer
     * @param State $state
     */
    public function __construct(
        ContentImportProcessorInterface $importer,
        State $state
    ) {
        $this->importer = $importer;
        $this->state = $state;
    }

    /**
     * @param string|array $dir
     * @return $this
     */
    public function setMediaDir(string $dir) : SampleImporterInterface
    {
        $this->mediaDir = $dir;
        return $this;
    }

    /**
     * @param string|array $dir
     * @return $this
     */
    public function setSourceDir(string $dir) : SampleImporterInterface
    {
        $this->jsonSourceDir = $dir;
        return $this;
    }

    /**
     * @param $search
     * @param $replace
     * @return SampleImporterProcessor
     */
    public function setReplacements($search, $replace = null) : SampleImporterInterface
    {
        if ($search === (array) $search) {
            $this->replacements = $search;
        } else {
            $this->replacements[$search] = $replace;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMediaFiles() : array
    {
        $result = [];
        if (is_array($this->mediaDir)) {
            foreach ($this->mediaDir as $dir) {
                $result = array_merge($result, $this->parseMediaDir($dir));
            }
        } else {
            $result = $this->parseMediaDir($this->mediaDir);
        }

        return $result;
    }

    /**
     * @param $dir
     * @return array
     */
    private function parseMediaDir($dir)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        if (!$dir || !file_exists($dir)) {
            return [];
        }

        // phpcs:ignore // phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
        return glob(rtrim($dir, '\\/') . '/*');
    }

    /**
     * @param $dir
     * @return array
     */
    private function parseSourceDir($dir): array
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        if (!$dir || !file_exists($dir)) {
            return [];
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
        return glob(rtrim($dir, '\\/') . '/*.json');
    }

    /**
     * @return array
     */
    public function getSourceFiles() : array
    {
        $result = [];
        if (is_array($this->jsonSourceDir)) {
            foreach ($this->jsonSourceDir as $dir) {
                $result = array_merge($result, $this->parseSourceDir($dir));
            }
        } else {
            $result = $this->parseSourceDir($this->jsonSourceDir);
        }

        return $result;
    }

    /**
     * @param $content
     * @return array|mixed|string|string[]|null
     */
    protected function replace($content)
    {
        foreach ($this->replacements as $search => $replacement) {
            $content = str_replace($search, $replacement, $content);
        }

        return $content;
    }

    /**
     * @return bool
     */
    private function shouldRunImport()
    {
        return !!$this->getSourceFiles();
    }

    /**
     * @param SampleImportInterface $sampleImport
     * @return SampleImporterProcessor
     */
    public function setSampleImport(SampleImportInterface $sampleImport) : SampleImporterInterface
    {
        $this->setMediaDir(
            $sampleImport->getMediaDir()
        )->setSourceDir(
            $sampleImport->getSourceFiles()
        )->setReplacements(
            $sampleImport->getReplacement()
        );

        return $this;
    }

    /**
     * @param null|string $fileName
     * @return array
     * @throws LocalizedException
     */
    public function import(?string $fileName = null): array
    {
        if (!$this->shouldRunImport()) {
            throw new LocalizedException(
                __('Nothing to import')
            );
        }

        return $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($fileName) {
            PageBuilder::initialize();

            $media = $this->getMediaFiles();

            foreach ($media as $file) {
                AssetsHelper::copy($file, 'media');
            }

            $processed = [];
            $fileSources = $this->getSourceFiles();
            if ($fileName !== null) {
                $selectedFiles = array_filter($fileSources, function ($filePath) use ($fileName) {
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                    return basename($filePath) == $fileName;
                });
            } else {
                $selectedFiles = $fileSources;
            }

            if ($selectedFiles) {
                foreach ($selectedFiles as $filePath) {
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                    $data = file_get_contents($filePath);
                    $data = $this->replace($data);
                    $processed = array_merge($processed, $this->importer->import(
                        $data
                    ));
                }
            }

            return $processed;
        });
    }
}
