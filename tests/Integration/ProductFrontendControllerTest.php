<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\ProductLayoutUpdateManager;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class ProductFrontendControllerTest extends AbstractFrontendControllerTest
{
    /**
     * Check that Global Custom Layout Update files work for Product views.
     *
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     * @return void
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function testViewWithGlobalCustomUpdate(): void
    {
        //Setting a fake file for the product.
        $file = 'test-file';

        /** @var ProductRepositoryInterface $repository */
        $repository = $this->objectManager->create(ProductRepositoryInterface::class);
        $sku = 'simple_product_1';
        $product = $repository->get($sku);
        $productId = $product->getId();

        /** @var ProductLayoutUpdateManager $layoutManager */
        $layoutManager = $this->objectManager->get(ProductLayoutUpdateManager::class);
        $layoutManager->setFakeFiles(0, [$file]);

        //Updating the custom attribute.
        $product->setCustomAttribute('custom_layout_update_file', $file);
        $repository->save($product);

        //Viewing the product
        $this->dispatch("catalog/product/view/id/$productId");

        //Layout handles must contain the file.
        $handles = $this->layoutInterface
            ->getUpdate()
            ->getHandles();
        $this->assertContains("catalog_product_view_selectable_0_{$file}", $handles);
    }
}
