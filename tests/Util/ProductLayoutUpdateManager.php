<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Util;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Easy way to fake available files.
 */
class ProductLayoutUpdateManager extends \Magento\TestFramework\Catalog\Model\ProductLayoutUpdateManager
{
    /**
     * @var array Keys are product IDs, values - file names.
     */
    private $fakeFiles = [];

    /**
     * Supply fake files for a product.
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
     *
     * @param ProductInterface $product
     * @return array
     */
    public function fetchAvailableFiles(ProductInterface $product): array
    {
        if (array_key_exists(0, $this->fakeFiles)) {
            return $this->fakeFiles[0];
        }

        return parent::fetchAvailableFiles($product);
    }
}
