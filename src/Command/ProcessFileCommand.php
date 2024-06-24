<?php

// src/Command/ProcessFileCommand.php
namespace App\Command;

use App\Service\FileProcessing\FileProcessorFactory;
use App\Storage\StorageRegistry;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:process-file',
    description: 'Processes a file and pushes data to the database'
)]
class ProcessFileCommand extends Command
{
    private FileProcessorFactory $fileProcessorFactory;
    private StorageRegistry $storageRegistry;
    private LoggerInterface $fileLogger;

    public function __construct(FileProcessorFactory $fileProcessorFactory, StorageRegistry $storageRegistry, LoggerInterface $fileLogger)
    {
        parent::__construct();

        $this->fileProcessorFactory = $fileProcessorFactory;
        $this->storageRegistry = $storageRegistry;
        $this->fileLogger = $fileLogger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file_path', InputArgument::REQUIRED, 'The location of the file to process')
            ->addArgument('file_type', InputArgument::REQUIRED, 'The type of the file (e.g., xml, csv)')
            ->addArgument('storage', InputArgument::OPTIONAL, 'The type of storage', 'mysql');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = $input->getArgument('file_path');
        $fileType = $input->getArgument('file_type');
        $storageType = $input->getArgument('storage');

        try {
            // file processing logic is separated to support single responsibility principle
            $processor = $this->fileProcessorFactory->create($fileType);
        } catch (InvalidArgumentException $e) {
            $this->fileLogger->error($e->getMessage());
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        try {
            $storage = $this->storageRegistry->getStorage($storageType);
        } catch (InvalidArgumentException $e) {
            $this->fileLogger->error($e->getMessage());
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        try {
            // Using foreach to avoid loading the entire file into memory
            // and saving one data element at a time
            // and thereby increasing memory efficiency
            foreach ($processor->process($filePath) as $data) {
                $storage->save($data);
            }
            $io->success('Data successfully saved to storage.');
        } catch (Exception $e) {
            $this->fileLogger->error($e->getMessage());
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
