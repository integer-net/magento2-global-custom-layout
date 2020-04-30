<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Util;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Easy way to fake available files.
 */
class CategoryLayoutUpdateManager extends \Magento\TestFramework\Catalog\Model\CategoryLayoutUpdateManager
{
    /**
     * @var array Keys are category IDs, values - file names.
     */
    private $fakeFiles = [];

    /**
     * Supply fake files for a category.
     *
     * @param int $forCategoryId
     * @param string[]|null $files Pass null to reset.
     */
    public function setCategoryFakeFiles(int $forCategoryId, ?array $files): void
    {
        if ($files === null) {
            unset($this->fakeFiles[$forCategoryId]);
        } else {
            $this->fakeFiles[$forCategoryId] = $files;
        }
    }

    /**
     * Fetches fake/mock files added through $this->setCategoryFakeFiles()
     *
     * @param CategoryInterface $category
     * @return array
     */
    public function fetchAvailableFiles(CategoryInterface $category): array
    {
        if (array_key_exists(0, $this->fakeFiles)) {
            return $this->fakeFiles[0];
        }
        return parent::fetchAvailableFiles($category);
    }
}
