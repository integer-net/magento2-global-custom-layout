<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Plugin;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page\CustomLayoutManagerInterface;
use Magento\Cms\Model\Page\CustomLayout\CustomLayoutManager;
use Magento\Cms\Model\Page\CustomLayout\Data\CustomLayoutSelectedInterface;
use Magento\Cms\Model\Page\IdentityMap;
use Magento\Framework\App\Area;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Model\Layout\Merge as LayoutProcessor;
use Magento\Framework\View\Model\Layout\MergeFactory as LayoutProcessorFactory;
use Magento\Framework\View\Result\Page as PageLayout;

class PageLayoutPlugin {

    /**
     * @var FlyweightFactory
     */
    private $themeFactory;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var LayoutProcessorFactory
     */
    private $layoutProcessorFactory;

    /**
     * @var LayoutProcessor|null
     */
    private $layoutProcessor;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * @param FlyweightFactory $themeFactory
     * @param DesignInterface $design
     * @param PageRepositoryInterface $pageRepository
     * @param LayoutProcessorFactory $layoutProcessorFactory
     * @param IdentityMap $identityMap
     */
    public function __construct(
        FlyweightFactory $themeFactory,
        DesignInterface $design,
        PageRepositoryInterface $pageRepository,
        LayoutProcessorFactory $layoutProcessorFactory,
        IdentityMap $identityMap
    ) {
        $this->themeFactory = $themeFactory;
        $this->design = $design;
        $this->pageRepository = $pageRepository;
        $this->layoutProcessorFactory = $layoutProcessorFactory;
        $this->identityMap = $identityMap;
    }

    /**
     * Get the processor instance.
     *
     * @return LayoutProcessor
     *
     * Unchanged private method copied over from @var CustomLayoutManager
     */
    private function getLayoutProcessor(): LayoutProcessor
    {
        if (!$this->layoutProcessor) {
            $this->layoutProcessor = $this->layoutProcessorFactory->create(
                [
                    'theme' => $this->themeFactory->create(
                        $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND)
                    )
                ]
            );
            $this->themeFactory = null;
            $this->design = null;
        }

        return $this->layoutProcessor;
    }

    /**
     * Fetch list of available global files/handles for the page.
     *
     * @param CustomLayoutManagerInterface $subject
     * @param array $result
     * @param PageInterface $page
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterFetchAvailableFiles(
        CustomLayoutManagerInterface $subject,
        array $result,
        PageInterface $page
    ): array {
        $handles = $this->getLayoutProcessor()->getAvailableHandles();

        return array_merge($result, array_filter(
            array_map(
                function(string $handle) : ?string {
                    preg_match(
                        '/^cms\_page\_view\_selectable\_0\_([a-z0-9]+)/i',
                        $handle,
                        $selectable
                    );
                    if (!empty($selectable[1])) {
                        return $selectable[1];
                    }

                    return null;
                },
                $handles
            )
        ));
    }

    /**
     * @param CustomLayoutManagerInterface $subject
     * @param $result
     * @param PageLayout $layout
     * @param CustomLayoutSelectedInterface $layoutSelected
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterApplyUpdate(
        CustomLayoutManagerInterface $subject,
        $result,
        PageLayout $layout,
        CustomLayoutSelectedInterface $layoutSelected
    ): void {
        $layout->addPageLayoutHandles(
            ['selectable_0' => $layoutSelected->getLayoutFileId()]
        );
    }
}
