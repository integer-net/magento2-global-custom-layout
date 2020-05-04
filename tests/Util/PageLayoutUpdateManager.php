<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Util;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Page\CustomLayout\CustomLayoutManager;

/**
 * Manager allowing to fake available files.
 */
class PageLayoutUpdateManager extends CustomLayoutManager
{
    /**
     * @var array Keys are Page IDs, values - file names.
     */
    private $fakeFiles = [];

    /**
     * Supply fake files for a Page.
     *
     * @param int $forPageId
     * @param string[]|null $files Pass null to reset.
     */
    public function setFakeFiles(int $forPageId, ?array $files): void
    {
        if ($files === null) {
            unset($this->fakeFiles[$forPageId]);
        } else {
            $this->fakeFiles[$forPageId] = $files;
        }
    }

    /**
     * Fetches fake/mock files added through $this->setFakeFiles()
     * for current Page and Global (0)
     *
     * If none found, fall back to original method
     *
     * @param PageInterface $page
     * @return array
     */
    public function fetchAvailableFiles(PageInterface $page): array
    {
        return array_unique(
            array_merge(
                ($this->fakeFiles[$page->getId()] ?? []),
                ($this->fakeFiles[0] ?? [])
            )
        ) ?: parent::fetchAvailableFiles($page);
    }
}
