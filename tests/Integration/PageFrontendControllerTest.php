<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\CustomLayoutManager;
use IntegerNet\GlobalCustomLayout\Test\Util\PageLayoutUpdateManager;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Page as PageModel;
use Magento\Cms\Model\Page\CustomLayoutRepositoryInterface;
use Magento\Cms\Model\PageFactory as PageModelFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResourceModel;
use Magento\Framework\Exception\AlreadyExistsException;

class PageFrontendControllerTest extends AbstractFrontendControllerTest
{
    /** @var CustomLayoutManager */
    protected $layoutManager;

    /** @var PageModel */
    protected $page;

    /** @var PageResourceModel */
    protected $pageResource;

    /** @var pageFactory $pageFactory */
    protected $pageFactory;

    protected function getPage(int $storeId, string $pageIdentifier): pageModel
    {
        /** @var CustomLayoutManager $layoutManager */
        $layoutManager = $this->getLayoutManager();

        /** @var PageResourceModel $pageResource */
        $pageResource = $this->getPageResource();

        /** @var CustomLayoutRepositoryInterface $layoutRepo */
        $layoutRepo = $this->objectManager->create(
            CustomLayoutRepositoryInterface::class,
            ['manager' => $layoutManager]
        );

        /** @var PageModelFactory $pageFactory */
        $pageFactory = $this->objectManager->get(PageModelFactory::class);

        /** @var PageModel $page */
        $page = $pageFactory->create(['customLayoutRepository' => $layoutRepo]);

        $page->setStoreId($storeId);
        $pageResource->load($page, $pageIdentifier, PageInterface::IDENTIFIER);

        return $page;
    }

    /**
     * Check that custom handles are applied when rendering a page.
     *
     * @return void
     * @throws AlreadyExistsException
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testViewWithGlobalCustomUpdate(): void
    {
        $file = 'test-file';
        $pageIdentifier = 'page100';
        $storeId = 0;

        /** @var pageModel $page */
        $page = $this->getPage($storeId, $pageIdentifier);
        /** @var CustomLayoutManager $layoutManager */
        $layoutManager = $this->getLayoutManager();

        /** @var PageResourceModel $pageResource */
        $pageResource = $this->getPageResource();

        $pageId = $page->getId();
        $layoutManager->fakeAvailableFiles(0, [$file]);

        //Updating the custom attribute.
        $page->setData('layout_update_selected', $file);
        $pageResource->save($page);

        $this->dispatch('/cms/page/view/page_id/' . $pageId);

        $handles = $this->layoutInterface->getUpdate()->getHandles();
        $this->assertContains("cms_page_view_selectable_0_{$file}", $handles);
    }

    /**
     * @return PageResourceModel
     */
    protected function getPageResource(): PageResourceModel
    {
        if (!$this->pageResource) {
            $this->pageResource = $this->objectManager->get(PageResourceModel::class);
        }
        return $this->pageResource;
    }

    /**
     * @return CustomLayoutManager
     */
    protected function getLayoutManager(): CustomLayoutManager
    {
        if (!$this->layoutManager) {
            $this->layoutManager = $this->objectManager->get(CustomLayoutManager::class);
        }
        return $this->layoutManager;
    }
}
