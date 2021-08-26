<?php
/**
 * @package Goomento_BuilderWidgets
 * @link https://github.com/Goomento/BuilderWidgets
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;


use Exception;
use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\StaticLogger;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use ReflectionException;

/**
 * Class Block
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Block extends Widget
{

	/**
	 * Get widget name.
	 *
	 * Retrieve shortcode widget name.
	 *
	 *
	 * @return string Widget name.
	 */
	public function getName()
    {
		return 'block';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve shortcode widget title.
	 *
	 *
	 * @return string Widget title.
	 */
	public function getTitle()
    {
		return __( 'Block' );
	}

    /**
     * @return string[]
     */
    public function getStyleDepends()
    {
        return [];
    }

	/**
	 * Get widget icon.
	 *
	 * Retrieve shortcode widget icon.
	 *
	 *
	 * @return string Widget icon.
	 */
	public function getIcon()
    {
		return 'fas fa-cubes';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 *
	 * @return array Widget keywords.
	 */
	public function getKeywords() {
		return [ 'block', 'code' ];
	}

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 *
	 * @return bool Whether the reload preview is required.
	 */
	public function isReloadPreviewRequired() {
		return true;
	}

    /**
     * @throws Exception
     */
	protected function registerControls() {
		$this->startControlsSection(
			'section_block',
			[
				'label' => __( 'Block' ),
			]
		);

		$this->addControl(
			'class',
			[
				'label' => __( 'Block class' ),
				'type' => Controls::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => '\Magento\Framework\View\Element\Template',
				'default' => '',
			]
		);

		$this->addControl(
			'template',
			[
				'label' => __( 'Template' ),
				'type' => Controls::TEXT,
				'placeholder' => 'Your_Module::your_template.phtml',
				'default' => '',
			]
		);

		$this->endControlsSection();
	}

	/**
	 * Render shortcode widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 */
	protected function render() {
		$class = $this->getSettingsForDisplay( 'class' );
        if (!$class) {
            $class = Template::class;
        }
        $html = '';
        if (class_exists($class)) {
            try {
                $template = $this->getSettingsForDisplay( 'template' );
                /** @var LayoutInterface $layout */
                $layout = StaticObjectManager::get(LayoutInterface::class);
                $block = $layout->createBlock($class);
                if ($template) {
                    $block->setTemplate($template);
                }
                $html = $block->toHtml();
            } catch (Exception $e) {
                StaticLogger::error($e);
                $html .= 'ERROR: ' . $e->getMessage();
            }
        }
		?>
		<div class="gmt-block"><?php echo $html; ?></div>
		<?php
	}

	/**
	 * Render shortcode widget as plain content.
	 *
	 * Override the default behavior by printing the shortcode instead of rendering it.
	 *
	 */
	public function renderPlainContent()
    {
		// In plain mode, render without shortcode
		echo $this->getSettings( 'block' );
	}

	/**
	 * Render shortcode widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 */
	protected function contentTemplate() {}
}
