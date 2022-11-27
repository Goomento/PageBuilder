<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Ui\Component\Form\Element\Wysiwyg;

use Goomento\PageBuilder\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Element\AbstractElement;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
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
    ) {
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
            $subject->setData('config', $this->addComponent((array) $config));
        } else {
            $proceed();
        }
    }

    /**
     * @param array $config
     * @return array
     */
    private function addComponent(array $config)
    {
        $config['component']   = 'goomento-builder-assistance';
        $config['elementTmpl'] = 'Goomento_PageBuilder/ui/form/element/builder_assistance';
        $config['template']    = 'ui/form/field';
        $config['endpoint']    = $this->urlBuilder->getUrl('pagebuilder/ajax/BuilderAssistance');
        return $config;
    }

    /**
     * @param string $currentUrl
     * @param array $paths
     * @return bool
     */
    public static function urlContains(string $currentUrl, array $paths)
    {
        $currentUrl = explode('/', $currentUrl);
        $currentUrl = array_filter($currentUrl);
        foreach ($paths as $path) {
            $path = explode('/', $path);
            $path = array_filter($path);
            if (array_intersect($path, $currentUrl) === $path) {
                return true;
            }
        }

        return false;
    }
}
