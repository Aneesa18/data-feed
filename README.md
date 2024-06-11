# data-feed

This is a symfony based project. It contains a command line script to process an xml file and push the data of that XML file to a database.

**Features:**
- Errors are written to a logfile
- The data storage can be easily extended without any interaction with the command line script 
- The application is tested using unit and integration tests

**How to extend data storage:**

MySQL is used as a default database. However, different data storages can be used by
- creating their respective storage files with their save() logic in the src/Storage directory (eg src/Storage/SQLiteStorage.php),
- adding their service to the "services.yaml",
- adding them to the "addStorage" method of the "StorageRegistry" service in the "services.yaml" and
- adding the storage as an argument while executing the command

**Usage:**
1. Clone the repository: ``` git clone https://github.com/Aneesa18/data-feed.git ```
2. In the project directory, install dependencies: ``` composer install ```
3. Update 'DATABASE_URL' in the .env file to an actual database url
4. Execute the command (default argument 'mysql'): ``` php bin/console app:process-xml ```
5. To use a different datastorage that is added to the StorageRegistry, use it as an argument to the command: ``` php bin/console app:process-xml sqlite ```

**Logging:**
- There is a new handler created in monolog.yaml to log the xml errors
- The logfile can be found in var/log/xml_processor.log

**XML file:**
- The xml file can be changed by using the parameter "xml_file" in the services.yaml
- The feed.xml contains elements with mixed cases (snake_case and UpperCamelCase) which are converted to camelCase in the MySQLStorage.php file

**Note**
- The command now fails on execution. This failure is the expected behaviour as there is no actual database connected. After executing the command, the reason for the failure can be checked in the var/log/xml_processor.log.
- Also, the repository has two contributors. This is because I created and edited the README.md file on the web in github using my Aneesa18 account. However, my PHPStorm which I used to push the code was logged into my other github account Aneesa-18. This can be checked in the commits.
