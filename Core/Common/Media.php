<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Common;

use Goomento\PageBuilder\Block\Adminhtml\MediaBucket;
use Goomento\PageBuilder\Core\Base\App as BaseApp;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticTemplate;

/**
 * Class Media
 * @package Goomento\PageBuilder\Core\Common
 */
class Media extends BaseApp
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'media';
    }

    /**
     * Print Templates
     *
     * Prints all registered templates.
     *
     */
    public function printTemplates()
    {
        echo StaticTemplate::getHtml(MediaBucket::class);
    }

    /**
     * Media constructor.
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/editor/footer', [ $this, 'printTemplates' ]);
    }
}
