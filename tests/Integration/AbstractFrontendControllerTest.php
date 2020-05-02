<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\CategoryLayoutUpdateManager;
use IntegerNet\GlobalCustomLayout\Test\Util\CustomLayoutManager;
use IntegerNet\GlobalCustomLayout\Test\Util\ProductLayoutUpdateManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
 */
abstract class AbstractFrontendControllerTest extends AbstractController
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var LayoutInterface
     */
    protected $layoutInterface;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->layoutInterface = $this->objectManager->get(LayoutInterface::class);

        $this->setUpPreferences();

        parent::setUp();
    }

    private function setUpPreferences(): void
    {
        $this->objectManager->configure(
            [
                'preferences' => [
                    \Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager::class => CategoryLayoutUpdateManager::class,
                    \Magento\Catalog\Model\Product\Attribute\LayoutUpdateManager::class  => ProductLayoutUpdateManager::class,
                    \Magento\Cms\Model\Page\CustomLayoutManagerInterface::class          => CustomLayoutManager::class,
                ]
            ]
        );
    }
}
