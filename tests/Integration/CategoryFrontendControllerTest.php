<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Tests whether global layout handles are correctly saved on categories
 * and retrieved on the frontend on Category views
 *
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
 * @magentoComponentsDir ../../../../vendor/integer-net/magento2-global-custom-layout/tests/Integration/_files/app/code/IntegerNet
 */
class CategoryFrontendControllerTest extends AbstractFrontendControllerTest
{
    /** @var int */
    const CATEGORY_ID_FROM_FIXTURE = 5;

    /** @var string */
    const DEFAULT_TEST_FILE = 'defaultfile';

    /** @var CategoryRepositoryInterface $repository */
    protected $repository;

    /** @var CategoryInterface $category */
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->objectManager->create(CategoryRepositoryInterface::class);
    }

    /**
     * Check that Global Custom Layout Update files work for Category views.
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     *
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_product_ids.php
     */
    public function testViewContainsGlobalCustomUpdate(): void
    {
        $this->givenGlobalCustomUpdateSelected();
        $this->whenCategoryViewed();
        $this->thenContainsGlobalUpdateHandle();
    }

    /**
     * Check that Default Custom Layout Update files still work for Category views.
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     *
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_product_ids.php
     */
    public function testViewContainsDefaultCustomUpdate(): void
    {
        $this->givenDefaultCustomUpdateSelected();
        $this->whenCategoryViewed();
        $this->thenContainsDefaultUpdateHandle();
    }

    /**
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function givenGlobalCustomUpdateSelected()
    {
        $this->setCustomUpdate(self::GLOBAL_IDENTIFIER);
    }

    /**
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function givenDefaultCustomUpdateSelected()
    {
        $this->setCustomUpdate(self::CATEGORY_ID_FROM_FIXTURE, self::DEFAULT_TEST_FILE);
    }

    /**
     * @param int $forCategoryId
     * @param string $fileName
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function setCustomUpdate(int $forCategoryId, string $fileName = self::GLOBAL_TEST_FILE)
    {
        $category = $this->getCategory();
        $category->setCustomAttribute('custom_layout_update_file', $fileName);
        $this->repository->save($category);
    }

    /**
     * Viewing the category
     *
     * @param int $categoryId
     */
    protected function whenCategoryViewed(?int $categoryId = null)
    {
        if (!$categoryId) {
            $categoryId = self::CATEGORY_ID_FROM_FIXTURE;
        }
        $this->dispatch("catalog/category/view/id/{$categoryId}");
    }

    protected function thenContainsGlobalUpdateHandle()
    {
        $this->containsUpdateHandle(self::GLOBAL_IDENTIFIER, self::GLOBAL_TEST_FILE);
    }

    protected function thenContainsDefaultUpdateHandle()
    {
        $this->containsUpdateHandle(self::CATEGORY_ID_FROM_FIXTURE, self::DEFAULT_TEST_FILE);
    }

    /**
     * Layout handles must contain the file.
     *
     * @param int|string $identifier
     * @param string $fileName
     */
    protected function containsUpdateHandle(
        $identifier = self::GLOBAL_IDENTIFIER,
        string $fileName = self::GLOBAL_TEST_FILE)
    {
        $expectedHandle = "catalog_category_view_selectable_{$identifier}_{$fileName}";

        $handles = $this->layout->getUpdate()->getHandles();
        $this->assertContains($expectedHandle, $handles);
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    protected function getCategory(int $categoryId = self::CATEGORY_ID_FROM_FIXTURE): CategoryInterface
    {
        if (!$this->category) {
            $this->category = $this->repository->get($categoryId);
        }
        return $this->category;
    }
}
