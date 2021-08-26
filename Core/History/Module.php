<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\History;

use Goomento\PageBuilder\Core\Base\Module as BaseModule;
use Goomento\PageBuilder\Core\Common\App;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Module
 * @package Goomento\PageBuilder\Core\History
 */
class Module extends BaseModule
{
    /**
     * Get module name.
     *
     * Retrieve the history module name.
     *
     *
     * @return string Module name.
     */
    public function getName()
    {
        return 'history';
    }


    public function addTemplates()
    {
        /** @var App $commonApp */
        $commonApp = StaticObjectManager::get(App::class);
        $commonApp->addTemplate('Goomento_PageBuilder::templates/history-panel-template.phtml');
        $commonApp->addTemplate('Goomento_PageBuilder::templates/revisions-panel-template.phtml');
    }

    /**
     * History module constructor.
     *
     * Initializing SagoTheme history module.
     *
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/editor/init', [ $this,'addTemplates' ]);
    }
}
