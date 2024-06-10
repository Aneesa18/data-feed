<?php
// tests/Command/ProcessXmlCommandUnitTest.php
namespace App\Tests\Command\Unit;

use Mockery as m;
use App\Command\ProcessXmlCommand;
use App\Storage\StorageInterface;
use App\Storage\StorageRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;
use InvalidArgumentException;
use RuntimeException;

class ProcessXmlCommandUnitTest extends TestCase
{
    private StorageRegistry $storageRegistry;
    private LoggerInterface $logger;
    private ProcessXmlCommand $command;

    protected function setUp(): void
    {
        $this->storageRegistry = m::mock(StorageRegistry::class);
        $this->logger = m::mock(LoggerInterface::class);

        $xmlFile = __DIR__ . '/../../fixtures/feed.xml';
        $this->command = new ProcessXmlCommand($xmlFile, $this->storageRegistry, $this->logger);
        $this->storage = m::mock(StorageInterface::class);
    }

    public function testConfigure()
    {
        $this->assertEquals('app:process-xml', $this->command->getName());
        $this->assertEquals('Processes an XML file and pushes data to the database', $this->command->getDescription());
    }

    public function testExecuteWithInvalidStorage()
    {
        // Setting up the expectation for getStorage method of StorageRegistry to throw an exception
        $this->storageRegistry->shouldReceive('getStorage')->andThrow(new InvalidArgumentException('Invalid storage type'));

        $this->logger->shouldReceive('error')->once()->with('Invalid storage type');

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute(['storage' => 'invalid_storage']);

        // 1 represents that the command has failed (due to invalid storage)
        $this->assertEquals(1, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Invalid storage type', $output);
    }

    public function testExecuteWithNonExistentXmlFile()
    {
        $this->storageRegistry->shouldReceive('getStorage')->andReturn($this->storage);

        // Setting up the expectation for the logger to receive the error method
        $this->logger->shouldReceive('error')
            ->once()
            ->with('XML file does not exist: non_existent_file.xml');

        $command = new ProcessXmlCommand('non_existent_file.xml', $this->storageRegistry, $this->logger);
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(['storage' => 'mysql']);

        // 1 represents that the command has failed (due to non-existent xml file)
        $this->assertEquals(1, $exitCode);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('XML file does not exist', $output);
    }

    public function testExecuteWithInvalidXml()
    {
        $xmlFile = __DIR__ . '/../../fixtures/invalid.xml';

        $this->storageRegistry->shouldReceive('getStorage')->andReturn($this->storage);

        $this->logger->shouldReceive('error')
            ->once()
            ->with("Failed to load XML file: $xmlFile");

        $this->expectException(RuntimeException::class);
        $command = new ProcessXmlCommand($xmlFile, $this->storageRegistry, $this->logger);
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(['storage' => 'mysql']);

        // 1 represents that the command has failed (due to invalid xml)
        $this->assertEquals(1, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Failed to load XML file', $output);
    }

    public function testExecuteSuccessfully()
    {
        $this->storage->shouldReceive('save')->twice();
        $this->storageRegistry->shouldReceive('getStorage')->andReturn($this->storage);

        $xmlFile = __DIR__ . '/../../fixtures/feed.xml';
        $command = new ProcessXmlCommand($xmlFile, $this->storageRegistry, $this->logger);
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(['storage' => 'mysql']);

        // 0 represents success of the command execution
        $this->assertEquals(0, $exitCode);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Data successfully saved to storage.', $output);
    }
}
