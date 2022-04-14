<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Response;

use Goomento\PageBuilder\Api\ModifierInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class HtmlModifier implements ModifierInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var array
     */
    private $modifiers;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $modifiers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $modifiers = []
    )
    {
        $this->objectManager = $objectManager;
        if (empty($modifiers)) {
            $modifiers = [
                'body_classes' => Html\BodyClasses::class,
                'header' => Html\Header::class,
                'footer' => Html\Footer::class,
            ];
        }
        $this->modifiers = $modifiers;
    }

    /**
     * Modify HTML output
     *
     * @param $data
     * @return string
     * @throws LocalizedException
     */
    public function modify($data)
    {
        $data = (string) $data;
        foreach ($this->modifiers as $modifier) {
            if (is_string($modifier)) {
                $modifier = $this->objectManager->get($modifier);
            }
            if (!($modifier instanceof ModifierInterface)) {
                throw new LocalizedException(
                    __('Invalid Modifier Class.')
                );
            }

            $data = $modifier->modify($data);
        }
        return $data;
    }
}
