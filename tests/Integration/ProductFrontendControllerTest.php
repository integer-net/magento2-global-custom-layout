<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\src\ProductLayoutUpdateManager;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class ProductFrontendControllerTest extends AbstractFrontendControllerTest
{
    /** @var string */
    const PRODUCT_SKU_FROM_FIXTURE = 'simple_product_1';

    /** @var ProductRepositoryInterface $repository */
    protected $repository;

    /** @var ProductInterface $product */
    protected $product;

    /** @var ProductLayoutUpdateManager $layoutManager */
    protected $layoutManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->layoutManager = $this->objectManager->get(ProductLayoutUpdateManager::class);
        $this->repository = $this->objectManager->create(ProductRepositoryInterface::class);
    }

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
    public function testViewContainsGlobalCustomUpdate(): void
    {
        $this->givenGlobalCustomUpdateSelected();
        $this->whenProductViewed();
        $this->thenContainsGlobalUpdateHandle();
    }

    /**
     * Check that Default Custom Layout Update files still work for Product views.
     *
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     * @return void
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function testViewContainsDefaultCustomUpdate(): void
    {
        $this->givenDefaultCustomUpdateSelected();
        $this->whenProductViewed();
        $this->thenContainsDefaultUpdateHandle();
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    protected function givenGlobalCustomUpdateSelected()
    {
        $this->setCustomUpdate(self::GLOBAL_IDENTIFIER);
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    protected function givenDefaultCustomUpdateSelected()
    {
        $this->setCustomUpdate($this->getProductId());
    }

    /**
     * Viewing the product
     *
     * @param int|null $productId
     * @throws NoSuchEntityException
     */
    protected function whenProductViewed(?int $productId = null): void
    {
        if (!$productId) {
            $productId = $this->getProductId();
        }
        $this->dispatch("catalog/product/view/id/{$productId}");
    }

    protected function thenContainsGlobalUpdateHandle()
    {
        $this->containsUpdateHandle(self::GLOBAL_IDENTIFIER);
    }

    protected function thenContainsDefaultUpdateHandle()
    {
        $this->containsUpdateHandle(self::PRODUCT_SKU_FROM_FIXTURE);
    }

    /**
     * Layout handles must contain the file.
     *
     * @param int|string $identifier
     * @param string $fileName
     */
    protected function containsUpdateHandle(
        $identifier = self::GLOBAL_IDENTIFIER,
        string $fileName = self::TEST_FILE)
    {
        $expectedHandle = "catalog_product_view_selectable_{$identifier}_{$fileName}";

        $handles = $this->layoutInterface->getUpdate()->getHandles();
        $this->assertContains($expectedHandle, $handles);
    }

    /**
     * @param int $forProductId
     * @param string $fileName
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    protected function setCustomUpdate(int $forProductId, string $fileName = self::TEST_FILE)
    {
        $product = $this->getProduct();

        $this->layoutManager->setFakeFiles($forProductId, [$fileName]);

        $product->setCustomAttribute('custom_layout_update_file', $fileName);
        $this->repository->save($product);
    }

    /**
     * @param string $sku
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    protected function getProduct(string $sku = self::PRODUCT_SKU_FROM_FIXTURE): ProductInterface
    {
        if (!$this->product) {
            $this->product = $this->repository->get($sku);
        }
        return $this->product;
    }

    /**
     * @return int|null
     * @throws NoSuchEntityException
     */
    protected function getProductId()
    {
        return (int)$this->getProduct()->getId();
    }
}
