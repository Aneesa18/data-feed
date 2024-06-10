<?php

// src/Command/ProcessXmlCommand.php
namespace App\Command;

use App\Storage\StorageRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;
use InvalidArgumentException;

#[AsCommand(
    name: 'app:process-xml',
    description: 'Processes an XML file and pushes data to the database'
)]
class ProcessXmlCommand extends Command
{
    private string $xmlFile;
    private StorageRegistry $storageRegistry;
    private LoggerInterface $logger;

    public function __construct(string $xmlFile, StorageRegistry $storageRegistry, LoggerInterface $xmlLogger)
    {
        parent::__construct();

        $this->xmlFile = $xmlFile;
        $this->storageRegistry = $storageRegistry;
        $this->logger = $xmlLogger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('storage', InputArgument::OPTIONAL, 'The type of storage', 'mysql');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $storageType = $input->getArgument('storage');

        // command will fail if the storage is invalid
        try {
            $storage = $this->storageRegistry->getStorage($storageType);
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        // command will fail if the xml file does not exist
        if (!file_exists($this->xmlFile)) {
            $this->logger->error("XML file does not exist: $this->xmlFile");
            $io->error("XML file does not exist: $this->xmlFile");
            return Command::FAILURE;
        }

        $xml = simplexml_load_file($this->xmlFile);

        // command will fail if the xml file is not valid
        if ($xml === false) {
            $this->logger->error("Failed to load XML file: $this->xmlFile");
            foreach (libxml_get_errors() as $error) {
                $this->logger->error($error->message);
            }
            $io->error("Failed to load XML file.");
            return Command::FAILURE;
        }

        try {
            // Converting SimpleXMLElement object to associative array
            $array = json_decode(json_encode((array)$xml), TRUE);

            foreach ($array['item'] as $item) {
                $data = [];
                foreach ($item as $key => $value) {
                    $data[$key] = $value;
                }
                $storage->save($data);
            }

            $io->success('Data successfully saved to storage.');
        } catch (Exception $e) {
            $this->logger->error("Failed to save data: " . $e->getMessage());
            $io->error("Failed to save data.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
