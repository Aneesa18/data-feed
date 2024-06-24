# data-feed

This is a symfony based project. It contains a command line script to process a file and push the data of that file to a database.

**Features:**
- Errors are written to a logfile
- The file used to read data from can be easily extended without any interaction with the command line script
- The data storage can be easily extended without any interaction with the command line script 
- The application is tested using unit and integration tests
- Supports large files

**Alignment to SOLID principles:**
- Single Responsibility Principle: Each class in the project has a single responsibility. The responsibility for reading and processing the data is kept in the FileProcessors, the responsibility for persisting the storage of data is in the Storage class. All these responsibilities are kept out of the command.
- Open/Closed Principle: To extend new file types, it is only required to create new FileProcessors and add them to the service.yaml with a tag and there is no need for any modifications to the existing code. Hence, it is open for extension and closed for modification.
- Liskov Substitution Principle: Classes that implement "FileProcessorInterface" can be used interchangeably without breaking the application.
- Interface Segregation Principle: The interfaces are smaller and more focused without any unnecessary methods.
- Dependency Inversion Principle: The high-level modules depend on the interfaces i.e., abstractions rather than concrete implementations.

**How to extend data storage:**

MySQL is used as a default database. However, different data storages can be used by
- creating their respective storage files with their save() logic in the src/Storage directory (eg src/Storage/SQLiteStorage.php),
- adding their service to the "services.yaml",
- injecting them into the "StorageRegistry" service in the "services.yaml" and
- adding the storage as an argument while executing the command

**How to extend the file to be processed:**

Currently, the project supports XML. However, different file types can be registered by

- creating their respective file processors by extending the "FileProcessingInterface" in the FileProcessing service (eg src/Service/FileProcessing/CSVFileProcessor.php)
- adding their service to the "services.yaml"
- using the tag "app.file_processor" so that they are directly linked to the FileProcessorFactory
- adding the file_path and file_type as an argument while executing the command

**Usage:**
1. Clone the repository: ``` git clone https://github.com/Aneesa18/data-feed.git ```
2. In the project directory, install dependencies: ``` composer install ```
3. Update 'DATABASE_URL' in the .env file to an actual database url
4. The command has three arguments ```file_path = location of the file to process```, ```file_type = type of the file to process``` and ```storage = type of the storage to use```
5. Execute the command (default value for argument 'storage' is 'mysql'): ``` php bin/console app:process-file /home/PhPStormProjects/DataFeed/src/fixtures/feed.xml xml```
6. To use a different file type and data storage that are registered, use them as an argument to the command: ``` php bin/console app:process-file /home/PhPStormProjects/DataFeed/src/fixtures/feed.csv csv sqlite ```

**Logging:**
- There is a new handler created in monolog.yaml to log the errors
- The logfile can be found in var/log/file_processor.log

**Testing:**
- Integration test can be executed using ``` vendor/bin/phpunit tests/Command/Integration/ProcessXmlCommandIntegrationTest.php ```
- Unit test can be executed using ``` vendor/bin/phpunit tests/Command/Unit/ProcessXmlCommandUnitTest.php ```


**Note**
- The command now fails on execution. This failure is the expected behaviour as there is no actual database connected. After executing the command, the reason for the failure can be checked in the var/log/file_processor.log.
