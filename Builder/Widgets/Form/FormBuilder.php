<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Form;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;

class FormBuilder extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'form';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Form Builder');
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return ['form'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-envelope';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'form', 'builder', 'contact'];
    }

    /**
     * @inheritDoc
     */
    public function isBuildable(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getPreviewHelper(): array
    {
        return [
            'title' => 'Learn More',
            'link' => 'https://goomento.com/magento-form-builder',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        return '';
    }
}
