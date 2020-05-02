<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Util;

use Magento\Cms\Api\Data\PageInterface;

/**
 * Manager allowing to fake available files.
 */
class CustomLayoutManager extends \Magento\TestFramework\Cms\Model\CustomLayoutManager
{
    /**
     * @var string[][]
     */
    private $files = [];

    /**
     * Fake available files for given page.
     *
     * Pass null to unassign fake files.
     *
     * @param int $forPageId
     * @param string[]|null $files
     * @return void
     */
    public function fakeAvailableFiles(int $forPageId, ?array $files): void
    {
        if ($files === null) {
            unset($this->files[$forPageId]);
        } else {
            $this->files[$forPageId] = $files;
        }
    }

    /**
     * Fetches fake/mock files added through $this->fakeAvailableFiles()
     *
     * @param PageInterface $page
     * @return array
     */
    public function fetchAvailableFiles(PageInterface $page): array
    {
        if (array_key_exists(0, $this->files)) {
            return $this->files[0];
        }

        return parent::fetchAvailableFiles($page);
    }
}
