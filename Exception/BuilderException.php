<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

namespace Goomento\PageBuilder\Exception;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class BuilderException extends LocalizedException
{
    /**
     * @param string|Phrase $phrase
     * @param Exception|null $cause
     * @param $code
     */
    public function __construct($phrase = '', Exception $cause = null, $code = 0)
    {
        parent::__construct($phrase instanceof Phrase ? $phrase : __($phrase), $cause, $code);
    }
}
