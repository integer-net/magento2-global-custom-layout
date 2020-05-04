<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Util;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\LayoutUpdateManager;

/**
 * Easy way to fake available files.
 */
class ProductLayoutUpdateManager extends LayoutUpdateManager
{
    /**
     * @var array Keys are Product IDs, values - file names.
     */
    private $fakeFiles = [];

    /**
     * Supply fake files for a Product.
     *
     * @param int $forProductId
     * @param string[]|null $files Pass null to reset.
     */
    public function setFakeFiles(int $forProductId, ?array $files): void
    {
        if ($files === null) {
            unset($this->fakeFiles[$forProductId]);
        } else {
            $this->fakeFiles[$forProductId] = $files;
        }
    }

    /**
     * Fetches fake/mock files added through $this->setFakeFiles()
     * for current Product and Global (0)
     *
     * If none found, fall back to original method
     *
     * @param ProductInterface $product
     * @return array
     */
    public function fetchAvailableFiles(ProductInterface $product): array
    {
        return array_unique(
            array_merge(
                ($this->fakeFiles[$product->getId()] ?? []),
                ($this->fakeFiles[0] ?? [])
            )
        ) ?: parent::fetchAvailableFiles($product);
    }
}
