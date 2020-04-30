<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\CategoryLayoutUpdateManager;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Tests whether global layout handles are correctly saved on categories
 * and retrieved on the frontend on Category views
 */
class CategoryFrontendControllerTest extends AbstractFrontendControllerTest
{
    /**
     * Check that Global Custom Layout Update files work for Category views.
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     *
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_product_ids.php
     */
    public function testViewWithGlobalCustomUpdate(): void
    {
        //Setting a fake file for the category.
        $file = 'test-file';
        $categoryId = 5;

        /** @var CategoryLayoutUpdateManager $layoutManager */
        $layoutManager = $this->objectManager->get(CategoryLayoutUpdateManager::class);
        $layoutManager->setCategoryFakeFiles(0, [$file]);

        /** @var CategoryRepositoryInterface $categoryRepo */
        $categoryRepo = $this->objectManager->create(CategoryRepositoryInterface::class);
        $category = $categoryRepo->get($categoryId);

        //Updating the custom attribute.
        $category->setCustomAttribute('custom_layout_update_file', $file);
        $categoryRepo->save($category);

        //Viewing the category
        $this->dispatch("catalog/category/view/id/$categoryId");

        //Layout handles must contain the file.
        $handles = $this->layoutInterface
            ->getUpdate()
            ->getHandles();
        $this->assertContains("catalog_category_view_selectable_0_{$file}", $handles);
    }
}
