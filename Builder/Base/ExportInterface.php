<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

interface ExportInterface
{
    /**
     * @param array $data
     * @param array $extraData
     * @return array|void
     */
    public function onExport($data, $extraData = []);
}
