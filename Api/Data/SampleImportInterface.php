<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

interface SampleImportInterface
{
    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * Get media dir to import
     * @return array[]|string
     */
    public function getMediaDir();

    /**
     * Get import file
     * @return array[]|string
     */
    public function getSourceFiles();

    /**
     * Get replaces
     * @return array[]|string
     */
    public function getReplacement();
}
