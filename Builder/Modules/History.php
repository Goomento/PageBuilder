<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Builder\Base\AbstractModule as BaseModule;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\TemplateHelper;

class History extends BaseModule
{
    const NAME = 'history';

    /**
     * @return void
     */
    public function addTemplates()
    {
        echo TemplateHelper::getHtml('Goomento_PageBuilder::templates/revisions_panel_template.phtml');
        echo TemplateHelper::getHtml('Goomento_PageBuilder::templates/history_panel_template.phtml');
    }

    /**
     * History module constructor.
     *
     * Initializing SagoTheme history module.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/editor/footer', [ $this,'addTemplates' ]);
    }
}
