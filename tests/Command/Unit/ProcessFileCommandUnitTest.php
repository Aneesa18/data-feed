<?php
// tests/Command/ProcessFileCommandUnitTest.php
namespace App\Tests\Command\Unit;

use App\Command\ProcessFileCommand;
use App\Service\FileProcessing\FileProcessorFactory;
use App\Service\FileProcessing\XmlFileProcessor;
use App\Storage\StorageInterface;
use App\Storage\StorageRegistry;
use InvalidArgumentException;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessFileCommandUnitTest extends TestCase
{
    private FileProcessorFactory $fileProcessorFactory;
    private StorageRegistry $storageRegistry;
    private LoggerInterface $fileLogger;
    private ProcessFileCommand $command;

    protected function setUp(): void
    {
        $this->fileProcessorFactory = m::mock(FileProcessorFactory::class);
        $this->storageRegistry = m::mock(StorageRegistry::class);
        $this->fileLogger = m::mock(LoggerInterface::class);
        $this->storage = m::mock(StorageInterface::class);
        $this->xmlFileProcessor = m::mock(XmlFileProcessor::class);

        $this->command = new ProcessFileCommand($this->fileProcessorFactory, $this->storageRegistry, $this->fileLogger);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testConfigure()
    {
        $this->assertEquals('app:process-file', $this->command->getName());
        $this->assertEquals('Processes a file and pushes data to the database', $this->command->getDescription());
    }

    public function testExecuteWithInvalidStorage()
    {
        $storageType = 'invalid_storage';
        $this->fileProcessorFactory->shouldReceive('create')->once()->with('xml');
        // Setting up the expectation for getStorage method of StorageRegistry to throw an exception
        $this->storageRegistry->shouldReceive('getStorage')->andThrow(new InvalidArgumentException("Storage type '$storageType' is not registered."));

        $this->fileLogger->shouldReceive('error')->once()->with("Storage type '$storageType' is not registered.");

        $exitCode = $this->commandTester->execute([
            'file_path' => 'test_file.xml',
            'file_type' => 'xml',
            'storage' => $storageType
        ]);

        // 1 represents that the command has failed (due to invalid storage)
        $this->assertEquals(1, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Storage type '$storageType' is not registered.", $output);
    }

    public function testExecuteWithInvalidFileType()
    {
        $fileType = 'invalid_file_type';

        $this->fileProcessorFactory->shouldReceive('create')
            ->with($fileType)
            ->andThrow(new InvalidArgumentException("File type '$fileType' is not registered."));

        // Setting up the expectation for the logger to receive the error method
        $this->fileLogger->shouldReceive('error')
            ->once()
            ->with("File type '$fileType' is not registered.");

        $exitCode = $this->commandTester->execute([
            'file_path' => 'test_file.xml',
            'file_type' => $fileType,
            'storage' => 'mysql'
        ]);

        // 1 represents that the command has failed (due to invalid file type)
        $this->assertEquals(1, $exitCode);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("File type '$fileType' is not registered.", $output);
    }

    public function testExecuteWithInvalidXmlFile()
    {
        $filePath = __DIR__ . '/../../fixtures/invalid.xml';

        // Setting up mock expectations for error case
        $this->fileProcessorFactory->shouldReceive('create')->once()->with('xml')->andReturn($this->xmlFileProcessor);
        $this->xmlFileProcessor->shouldReceive('process')->once()->with($filePath)->andThrow(new InvalidArgumentException("Failed to open XML file: $filePath"));

        $this->storageRegistry->shouldReceive('getStorage')->once()->with('mysql')->andReturn($this->storage);

        $this->fileLogger->shouldReceive('error')->once()->with("Failed to open XML file: $filePath");

        $exitCode = $this->commandTester->execute([
            'file_path' => $filePath,
            'file_type' => 'xml',
            'storage' => 'mysql'
        ]);

        // 1 represents that the command has failed (due to invalid or non-existent xml file)
        $this->assertEquals(1, $exitCode);
    }

    public function testExecuteSuccessfully()
    {
        $file = __DIR__.'/../../fixtures/feed.xml';
        $this->fileProcessorFactory->shouldReceive('create')->once()->with('xml')->andReturn($this->xmlFileProcessor);
        $this->xmlFileProcessor->shouldReceive('process')->once()->with($file)->andReturn([['data1'], ['data2']]);
        $this->storageRegistry->shouldReceive('getStorage')->once()->with('mysql')->andReturn($this->storage);
        $this->storage->shouldReceive('save')->twice();

        $exitCode = $this->commandTester->execute([
            'file_path' => $file,
            'file_type' => 'xml',
            'storage' => 'mysql'
        ]);

        // 0 represents success of the command execution
        $this->assertEquals(0, $exitCode);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Data successfully saved to storage.', $output);
    }
}
