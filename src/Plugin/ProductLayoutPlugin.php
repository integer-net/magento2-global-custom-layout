<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Plugin;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\LayoutUpdateManager;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Model\Layout\Merge as LayoutProcessor;
use Magento\Framework\View\Model\Layout\MergeFactory as LayoutProcessorFactory;

class ProductLayoutPlugin {

    /**
     * @var FlyweightFactory
     */
    private $themeFactory;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var LayoutProcessorFactory
     */
    private $layoutProcessorFactory;

    /**
     * @var LayoutProcessor|null
     */
    private $layoutProcessor;

    /**
     * @param FlyweightFactory $themeFactory
     * @param DesignInterface $design
     * @param LayoutProcessorFactory $layoutProcessorFactory
     */
    public function __construct(
        FlyweightFactory $themeFactory,
        DesignInterface $design,
        LayoutProcessorFactory $layoutProcessorFactory
    ) {
        $this->themeFactory = $themeFactory;
        $this->design = $design;
        $this->layoutProcessorFactory = $layoutProcessorFactory;
    }

    /**
     * Get the processor instance.
     *
     * @return LayoutProcessor
     *
     * Unchanged private method copied over from @var LayoutUpdateManager
     */
    private function getLayoutProcessor(): LayoutProcessor
    {
        if (!$this->layoutProcessor) {
            $this->layoutProcessor = $this->layoutProcessorFactory->create(
                [
                    'theme' => $this->themeFactory->create(
                        $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND)
                    )
                ]
            );
            $this->themeFactory = null;
            $this->design = null;
        }

        return $this->layoutProcessor;
    }

    /**
     * Fetch list of available global files/handles for the product.
     *
     * @param LayoutUpdateManager $subject
     * @param array $result
     * @param ProductInterface $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterFetchAvailableFiles(
        LayoutUpdateManager $subject,
        array $result,
        ProductInterface $product
    ): array {
        if (!$product->getSku()) {
            return [];
        }

        $handles = $this->getLayoutProcessor()->getAvailableHandles();

        return array_merge($result, array_filter(
            array_map(
                function(string $handle) : ?string {
                    preg_match(
                        '/^catalog\_product\_view\_selectable\_0\_([a-z0-9]+)/i',
                        $handle,
                        $selectable
                    );
                    if (!empty($selectable[1])) {
                        return $selectable[1];
                    }

                    return null;
                },
                $handles
            )
        ));
    }

    /**
     * Extract selected global custom layout settings.
     *
     * If no update is selected none will apply.
     *
     * @param LayoutUpdateManager $subject
     * @param $result
     * @param ProductInterface $product
     * @param DataObject $intoSettings
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExtractCustomSettings(
        LayoutUpdateManager $subject,
        $result,
        ProductInterface $product,
        DataObject $intoSettings
    ): void {
        if ($product->getSku() && $value = $this->extractAttributeValue($product)) {
            $handles = $intoSettings->getPageLayoutHandles() ?? [];
            $handles = array_merge_recursive(
                $handles,
                ['selectable_0' => $value]
            );
            $intoSettings->setPageLayoutHandles($handles);
        }
    }

    /**
     * Extract custom layout attribute value.
     *
     * @param ProductInterface $product
     * @return mixed
     *
     * Unchanged private method copied over from @var LayoutUpdateManager
     */
    private function extractAttributeValue(ProductInterface $product)
    {
        if ($product instanceof Product && !$product->hasData(ProductInterface::CUSTOM_ATTRIBUTES)) {
            return $product->getData('custom_layout_update_file');
        }
        if ($attr = $product->getCustomAttribute('custom_layout_update_file')) {
            return $attr->getValue();
        }

        return null;
    }
}
