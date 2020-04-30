<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\CategoryLayoutUpdateManager;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;
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
    protected   $layoutInterface;

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
                    LayoutUpdateManager::class => CategoryLayoutUpdateManager::class,
                ]
            ]
        );
        $this->objectManager->removeSharedInstance(LayoutUpdateManager::class);
    }
}
