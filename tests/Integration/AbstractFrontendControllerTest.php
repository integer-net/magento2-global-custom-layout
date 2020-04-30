<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use IntegerNet\GlobalCustomLayout\Test\Util\CategoryLayoutUpdateManager;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;
use Magento\Catalog\Model\Session;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
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
    private $objectManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->setUpPreferences();

        parent::setUp();

        $this->registry = $this->objectManager->get(Registry::class);
        $this->layout = $this->objectManager->get(LayoutInterface::class);
        $this->session = $this->objectManager->get(Session::class);
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
