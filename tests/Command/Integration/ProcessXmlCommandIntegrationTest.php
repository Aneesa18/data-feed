<?php
// tests/Command/Integration/ProcessXmlCommandIntegrationTest.php
namespace App\Tests\Command\Integration;

use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Psr\Log\NullLogger;
use App\Command\ProcessXmlCommand;
use App\Storage\StorageRegistry;
use App\Storage\StorageInterface;

class ProcessXmlCommandIntegrationTest extends KernelTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $xmlFile = __DIR__ . '/../../fixtures/feed.xml';

        //Mocking dependencies
        $storageRegistry = m::mock(StorageRegistry::class);
        $storage = m::mock(StorageInterface::class);

        $storage->shouldReceive('save')->atLeast()->once();

        $storageRegistry->shouldReceive('getStorage')->andReturn($storage);

        $logger = new NullLogger();

        $command = new ProcessXmlCommand($xmlFile, $storageRegistry, $logger);
        // Adding the command to the application
        $application->add($command);

        // Finding the command using its name
        $command = $application->find('app:process-xml');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['storage' => 'mysql']);

        // Asserting the command output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Data successfully saved to storage.', $output);
    }
}
