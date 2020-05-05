<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\src;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;

/**
 * Easy way to fake available files.
 */
class CategoryLayoutUpdateManager extends LayoutUpdateManager
{
    /**
     * @var array Keys are category IDs, values - file names.
     */
    private $fakeFiles = [];

    /**
     * Supply fake files for a Category.
     *
     * @param int $forCategoryId
     * @param string[]|null $files Pass null to reset.
     */
    public function setFakeFiles(int $forCategoryId, ?array $files): void
    {
        if ($files === null) {
            unset($this->fakeFiles[$forCategoryId]);
        } else {
            $this->fakeFiles[$forCategoryId] = $files;
        }
    }

    /**
     * Fetches fake/mock files added through $this->setCategoryFakeFiles()
     * for current Category and Global (0)
     *
     * If none found, fall back to original method
     *
     * @param CategoryInterface $category
     * @return array
     */
    public function fetchAvailableFiles(CategoryInterface $category): array
    {
        return array_unique(
            array_merge(
                ($this->fakeFiles[$category->getId()] ?? []),
                ($this->fakeFiles[0] ?? [])
            )
        ) ?: parent::fetchAvailableFiles($category);
    }
}
