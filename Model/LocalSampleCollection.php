<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\SampleImportInterface;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;

class LocalSampleCollection
{
    use TraitComponentsLoader;

    /**
     * @param array $samples
     */
    public function __construct(
        array $samples = []
    ) {
        $this->setComponent($samples);
    }

    /**
     * @return SampleImportInterface[]|[]
     */
    public function getAllSamples()
    {
        if (!HooksHelper::didAction('pagebuilder/samples/sample_registered')) {
            HooksHelper::doAction('pagebuilder/samples/sample_registered', $this);
        }
        return $this->getComponents();
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getSample(string $name)
    {
        return $this->getComponent($name);
    }

    /**
     * @param string $name
     * @param mixed $model
     * @return LocalSampleCollection
     */
    public function setSample(string $name, $model)
    {
        return $this->setComponent($name, $model);
    }
}
