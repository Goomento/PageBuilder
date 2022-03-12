<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\SampleImport;

use Goomento\PageBuilder\Api\Data\SampleImportInterface;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Framework\Exception\LocalizedException;

class SampleImport
{
    /**
     * @var SampleImportInterface[]|[]
     */
    private $samples = [];

    /**
     * @var bool
     */
    private $processed = false;

    /**
     * @param array $samples
     */
    public function __construct(
        array $samples = []
    )
    {
        $this->samples = $samples;
    }

    /**
     * @return SampleImportInterface[]|[]
     * @throws LocalizedException
     */
    public function getAllSamples()
    {
        if ($this->processed === false) {
            $this->processed = true;

            foreach ($this->samples as $sampleName => &$sampleModel) {
                if (is_string($sampleModel)) {
                    $sampleModel = ObjectManagerHelper::get(
                        $sampleModel
                    );
                }

                if (!is_object($sampleModel) || !($sampleModel instanceof SampleImportInterface)) {
                    throw new LocalizedException(
                        __('Invalid sample model')
                    );
                }
            }
        }

        return $this->samples;
    }

    /**
     * @param string $name
     * @return mixed|null
     * @throws LocalizedException
     */
    public function getSample(string $name)
    {
        $this->getAllSamples();
        return $this->samples[$name] ?? null;
    }
}
