<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Ui\Component\Form\Element\Wysiwyg;

use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Element\AbstractElement;

class BuilderAssistance
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Data $dataHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Data $dataHelper,
        UrlInterface $urlBuilder
    )
    {
        $this->dataHelper = $dataHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param AbstractElement $subject
     * @param callable $proceed
     * @return void
     */
    public function aroundPrepare(AbstractElement $subject, callable $proceed)
    {
        $isAssistanceActive = $this->dataHelper->isBuilderAssistanceActive();
        if ($isAssistanceActive) {
            $isAssistanceActive = $this->dataHelper->isBuilderAssistanceOnAllPage()
                || self::urlContains($this->urlBuilder->getCurrentUrl(), $this->dataHelper->getBuilderAssistanceCustomPages());
        }

        if ($isAssistanceActive) {
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

    /**
     * @param string $currentUrl
     * @param array $paths
     * @return bool
     */
    public static function urlContains(string $currentUrl, array $paths)
    {
        foreach ($paths as $path) {
            if (strpos($currentUrl, $path) !== false) {
                return true;
            }
        }

        return false;
    }
}
