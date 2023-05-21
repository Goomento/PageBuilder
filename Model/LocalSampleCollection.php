<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\SampleImportInterface;
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
        $this->components = $samples;
    }

    /**
     * @return SampleImportInterface[]|[]
     */
    public function getAllSamples()
    {
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
}
