<?php
// tests/Command/Integration/ProcessFileCommandIntegrationTest.php
namespace App\Tests\Command\Integration;

use App\Command\ProcessFileCommand;
use App\Service\FileProcessing\FileProcessorFactory;
use App\Service\FileProcessing\XmlFileProcessor;
use App\Storage\StorageInterface;
use App\Storage\StorageRegistry;
use Mockery as m;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessFileCommandIntegrationTest extends KernelTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $xmlFile = __DIR__ . '/../../fixtures/test.xml';

        // Mocking dependencies
        $storageRegistry = m::mock(StorageRegistry::class);
        $storage = m::mock(StorageInterface::class);
        $fileProcessorFactory = m::mock(FileProcessorFactory::class);
        $xmlProcessor = m::mock(XmlFileProcessor::class);

        $fileProcessorFactory->shouldReceive('create')->with('xml')->andReturn($xmlProcessor);
        $xmlProcessor->shouldReceive('process')
            ->with($xmlFile)
            ->andReturn(new \ArrayIterator([['test' => 'data']]));

        $storageRegistry->shouldReceive('getStorage')->andReturn($storage);
        $storage->shouldReceive('save')->atLeast()->once();

        $logger = new NullLogger();

        $command = new ProcessFileCommand($fileProcessorFactory, $storageRegistry, $logger);
        // Adding the command to the application
        $application->add($command);

        // Finding the command using its name
        $command = $application->find('app:process-file');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file_path' => $xmlFile,
            'file_type' => 'xml',
            'storage' => 'mysql'
        ]);

        // Asserting the command output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Data successfully saved to storage.', $output);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
