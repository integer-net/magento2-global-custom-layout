<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\PageLayoutUpdateManager;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\Page\CustomLayoutRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResourceModel;

class PageFrontendControllerTest extends AbstractFrontendControllerTest
{
    /** @var string */
    const PAGE_IDENTIFIER = 'page100';

    /** @var PageLayoutUpdateManager */
    protected $layoutManager;

    /** @var Page */
    protected $page;

    /** @var PageResourceModel $pageResource */
    protected $pageResource;

    /** @var pageFactory $pageFactory */
    protected $pageFactory;

    /** @var CustomLayoutRepositoryInterface $repository */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->layoutManager = $this->objectManager->get(PageLayoutUpdateManager::class);
        $this->pageResource = $this->objectManager->get(PageResourceModel::class);
        $this->pageFactory = $this->objectManager->get(PageFactory::class);
        $this->repository = $this->objectManager->create(
            CustomLayoutRepositoryInterface::class,
            ['manager' => $this->layoutManager]
        );
    }

    /**
     * Check that custom handles are applied when rendering a page.
     *
     * @return void
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testViewWithGlobalCustomUpdate(): void
    {
        $this->givenGlobalCustomUpdateSelected();
        $this->whenPageViewed();
        $this->thenContainsGlobalUpdateHandle();
    }

    /**
     * Check that custom handles are applied when rendering a page.
     *
     * @return void
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testViewWithDefaultCustomUpdate(): void
    {
        $this->givenDefaultCustomUpdateSelected();
        $this->whenPageViewed();
        $this->thenContainsDefaultUpdateHandle();
    }

    protected function givenGlobalCustomUpdateSelected()
    {
        $this->setCustomUpdate(self::GLOBAL_IDENTIFIER);
    }

    protected function givenDefaultCustomUpdateSelected()
    {
        $this->setCustomUpdate($this->getPageId());
    }

    /**
     * Viewing the product
     *
     * @param int|null $pageId
     */
    protected function whenPageViewed(?int $pageId = null): void
    {
        if (!$pageId) {
            $pageId = $this->getPageId();
        }
        $this->dispatch("/cms/page/view/page_id/{$pageId}");
    }

    protected function thenContainsGlobalUpdateHandle()
    {
        $this->containsUpdateHandle(self::GLOBAL_IDENTIFIER);
    }

    protected function thenContainsDefaultUpdateHandle()
    {
        $this->containsUpdateHandle(self::PAGE_IDENTIFIER);
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
        $expectedHandle = "cms_page_view_selectable_{$identifier}_{$fileName}";

        $handles = $this->layoutInterface->getUpdate()->getHandles();
        $this->assertContains($expectedHandle, $handles);
    }

    protected function setCustomUpdate(int $forPageId, string $fileName = self::TEST_FILE)
    {
        $page = $this->getPage();

        $this->layoutManager->setFakeFiles($forPageId, [$fileName]);

        $page->setData('layout_update_selected', $fileName);
        $this->pageResource->save($page);
    }

    /**
     * @param string $pageIdentifier
     * @return Page
     */
    protected function createPage(?string $pageIdentifier = self::PAGE_IDENTIFIER): Page
    {
        $page = $this->pageFactory->create(['customLayoutRepository' => $this->repository]);
        $page->setStoreId(self::STORE_ID);
        $page->load($pageIdentifier, Page::IDENTIFIER);

        return $page;
    }

    /**
     * @return Page
     */
    protected function getPage(): Page
    {
        if (!$this->page || !$this->page->getId()) {
            $this->page = $this->createPage();
        }
        return $this->page;
    }

    /**
     * @return int
     */
    private function getPageId(): int
    {
        return (int)$this->getPage()->getId();
    }
}
