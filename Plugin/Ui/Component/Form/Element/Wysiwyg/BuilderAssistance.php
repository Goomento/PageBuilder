<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Ui\Component\Form\Element\Wysiwyg;

use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Helper\Data;
use Magento\Ui\Component\Form\Element\AbstractElement;

class BuilderAssistance
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @param Data $dataHelper
     */
    public function __construct(
        Data $dataHelper
    )
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param AbstractElement $subject
     * @param callable $proceed
     * @return void
     */
    public function aroundPrepare(AbstractElement $subject, callable $proceed)
    {
        if ($this->dataHelper->isBuilderAssistanceActive()) {
            $config = $subject->getData('config');
            $config['component']   = 'Goomento_PageBuilder/js/ui/form/element/builderAssistance';
            $config['elementTmpl'] = 'Goomento_PageBuilder/ui/form/element/builder_assistance';
            $config['template'] = 'ui/form/field';
            $config['endpoint']    = UrlBuilderHelper::getUrl('pagebuilder/ajax/BuilderAssistance');
            $subject->setData('config', (array)$config);
        } else {
            $proceed();
        }
    }
}
