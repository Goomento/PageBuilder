<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

/**
 * Use for local import template files, uses to define .json and media files.
 */
interface SampleImporterInterface
{
    /**
     * @param string $dir
     * @return SampleImporterInterface
     */
    public function setMediaDir(string $dir) : SampleImporterInterface;

    /**
     * @param string $dir
     * @return SampleImporterInterface
     */
    public function setSourceDir(string $dir) : SampleImporterInterface;

    /**
     * @param array|string $search
     * @param null $replace
     * @return SampleImporterInterface
     */
    public function setReplacements($search, $replace = null) : SampleImporterInterface;

    /**
     * @return array
     */
    public function getMediaFiles() : array;

    /**
     * @return array
     */
    public function getSourceFiles() : array;

    /**
     * @param Data\SampleImportInterface $sampleImport
     * @return SampleImporterInterface
     */
    public function setSampleImport(Data\SampleImportInterface $sampleImport) : SampleImporterInterface;

    /**
     * Import the bunch of files
     *
     * @param string|null $fileName
     * @return array
     */
    public function import(?string $fileName) : array;
}
