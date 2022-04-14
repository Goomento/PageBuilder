<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Response\Html;

use Goomento\PageBuilder\Api\ModifierInterface;
use Goomento\PageBuilder\Helper\HooksHelper;

class BodyClasses implements ModifierInterface
{
    /**
     * Add body class to HTML output
     *
     * @param $data
     * @return array|object|string
     */
    public function modify($data)
    {
        $data = (string) $data;
        $bodyClasses = HooksHelper::applyFilters('pagebuilder/frontend/body_class', []);
        if (!empty($bodyClasses)) {
            if (preg_match('/<body[^>]+?>/i', $data, $matches)) {
                $bodyClasses = implode(' ', array_values($bodyClasses));
                $bodyTag = $matches[0];
                $parts = explode('class="', $bodyTag);
                if (count($parts)) {
                    $parts[1] = $bodyClasses . ' ' . $parts[1];
                    $newBodyTag = implode('class="', $parts);
                } else {
                    $newBodyTag = substr($bodyTag, strlen($bodyTag) - 1) . 'class="' . $bodyClasses . '" >';
                }

                $data = str_replace($bodyTag, $newBodyTag, $data);
            }
        }
        return $data;
    }
}
