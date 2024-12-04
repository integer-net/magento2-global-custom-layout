<?php
declare(strict_types=1);

namespace IntegerNet\GlobalCustomLayout\Test\Integration;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
 * @magentoComponentsDir ../../../../vendor/integer-net/magento2-global-custom-layout/tests/Integration/_files/app/code/IntegerNet
 */
class ModuleTest extends TestCase
{
    private const MODULE_NAME = 'IntegerNet_GlobalCustomLayout';
    private const TEST_MODULE_NAME = 'IntegerNet_GlobalCustomLayoutTest';
    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    public function testModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $paths = $registrar->getPaths(ComponentRegistrar::MODULE);
        $this->assertArrayHasKey(self::MODULE_NAME, $paths);
    }

    public function testTestModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $paths = $registrar->getPaths(ComponentRegistrar::MODULE);
        $this->assertArrayHasKey(self::TEST_MODULE_NAME, $paths);
    }

    public function testModuleIsActive()
    {
        /** @var ModuleList $moduleList */
        $moduleList = $this->objectManager->create(ModuleList::class);
        $this->assertTrue(
            $moduleList->has(self::MODULE_NAME),
            sprintf('The module %s should be enabled', self::MODULE_NAME)
        );
    }
}
