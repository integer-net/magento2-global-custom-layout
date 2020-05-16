<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Module\Status as ModuleStatus;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
 * @magentoComponentsDir ../../../../vendor/integer-net/magento2-global-custom-layout/tests/Integration/_files/app/code/IntegerNet
 */
abstract class AbstractFrontendControllerTest extends AbstractController
{
    /** @var int */
    const STORE_ID = 0;

    /** @var string */
    const GLOBAL_TEST_FILE = 'globalfile';

    /** @var string */
    const DEFAULT_TEST_FILE = 'defaultfile';

    /** @var int */
    const GLOBAL_IDENTIFIER = 0;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var LayoutInterface */
    protected $layout;

    /** @var ModuleStatus */
    protected $moduleStatus;

    /** @var Reader */
    protected $configReader;

    /** @var Writer */
    protected $configWriter;

    /** @var array */
    protected $initialConfig;

    /**
     * @inheritdoc
     * @throws LocalizedException
     * @magentoComponentsDir ../../../../vendor/integer-net/magento2-global-custom-layout/tests/Integration/_files/app/code/IntegerNet
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->layout = $this->objectManager->get(LayoutInterface::class);
        $this->configWriter = $this->objectManager->get(Writer::class);

        $this->backupInitialConfig();
        $this->enableTestModuleInConfig();

        parent::setUp();
    }

    protected function enableTestModuleInConfig()
    {
        $this->moduleStatus = $this->objectManager->create(ModuleStatus::class);
        $this->moduleStatus->setIsEnabled(true, ['IntegerNet_GlobalCustomLayoutTest']);

        $this->objectManager->removeSharedInstance(\Magento\Framework\Module\Manager::class);
        $this->objectManager->removeSharedInstance(\Magento\Framework\Module\ModuleList::class);
        $this->objectManager->removeSharedInstance(\Magento\Framework\View\Model\Layout\Merge::class);
    }

    /**
     * @throws FileSystemException
     */
    protected function tearDown(): void
    {
        $this->restoreInitialConfig();
    }

    /**
     * @throws FileSystemException
     */
    protected function restoreInitialConfig(): void
    {
        $this->configWriter->saveConfig(
            [ConfigFilePool::APP_CONFIG => ['modules' => $this->initialConfig['modules']]],
            true
        );
    }

    /**
     * @throws FileSystemException
     * @throws RuntimeException
     */
    protected function backupInitialConfig(): void
    {
        if (!$this->initialConfig) {
            $this->configReader = $this->objectManager->get(Reader::class);
            $this->initialConfig = $this->configReader->load();
        }
    }
}
