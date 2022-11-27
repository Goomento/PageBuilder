<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

abstract class AbstractActions
{
    /**
     * Go through each action then pass the data into
     *
     * @param $actionData
     * @param array $params
     * @return array
     */
    abstract public function doAction($actionData, $params = []);
}
