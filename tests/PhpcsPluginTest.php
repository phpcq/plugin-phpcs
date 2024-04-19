<?php

declare(strict_types=1);

namespace Phpcq\PhpcsPluginTest;

use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationBuilderInterface;
use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationInterface;
use Phpcq\PluginApi\Version10\DiagnosticsPluginInterface;
use Phpcq\PluginApi\Version10\EnvironmentInterface;
use Phpcq\PluginApi\Version10\ProjectConfigInterface;
use Phpcq\PluginApi\Version10\Task\TaskInterface;
use PHPUnit\Framework\TestCase;

use function dirname;
use function sys_get_temp_dir;
use function tempnam;

/**
 * @coversNothing
 */
final class PhpcsPluginTest extends TestCase
{
    private function instantiate(): DiagnosticsPluginInterface
    {
        return include dirname(__DIR__) . '/src/phpcs.php';
    }

    public function testPluginName(): void
    {
        self::assertSame('phpcs', $this->instantiate()->getName());
    }

    public function testPluginDescribesConfig(): void
    {
        $configOptionsBuilder = $this->getMockForAbstractClass(PluginConfigurationBuilderInterface::class);

        $this->instantiate()->describeConfiguration($configOptionsBuilder);

        // We assume it worked out as the plugin did execute correctly.
        $this->addToAssertionCount(1);
    }

    public function testPluginCreatesDiagnosticTasks(): void
    {
        $config = $this->getMockForAbstractClass(PluginConfigurationInterface::class);
        $environment = $this->getMockForAbstractClass(EnvironmentInterface::class);

        $this->instantiate()->createDiagnosticTasks($config, $environment);

        foreach ($this->instantiate()->createDiagnosticTasks($config, $environment) as $task) {
            $this->assertInstanceOf(TaskInterface::class, $task);
        }
    }

    public function testConfigureAutoloadPaths(): void
    {
        $config = $this->getMockForAbstractClass(PluginConfigurationInterface::class);
        $config->method('has')->willReturnCallback(static function (string $key) {
            if ($key === 'autoload_paths') {
                return true;
            }

            return false;
        });

        $config->method('getStringList')->willReturnCallback(static function (string $key): array {
            if ($key === 'autoload_paths') {
                return ['autoload.php'];
            }

            return [];
        });

        $configuration = $this->getMockForAbstractClass(ProjectConfigInterface::class);
        $configuration->method('getProjectRootPath')->willReturn(__DIR__ . '/fixtures');

        $environment = $this->getMockForAbstractClass(EnvironmentInterface::class);
        $environment->method('getUniqueTempFile')->willReturn(tempnam(sys_get_temp_dir(), 'phpcq-phpcs'));
        $environment->method('getProjectConfiguration')->willReturn($configuration);

        foreach ($this->instantiate()->createDiagnosticTasks($config, $environment) as $task) {
            $this->assertInstanceOf(TaskInterface::class, $task);
        }
    }
}
