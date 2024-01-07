<?php
 

function createDynamicTable($config, $xml)
{
    // Create a unique table name
    $tableName = 'xml_table_' . time();

   
    // Database configuration
    $dbHost = $config['database']['host'];
    $dbUser = $config['database']['user'];
    $dbPassword = $config['database']['password'];
    $dbName = $config['database']['name'];
    $dbTableName = $config['database']['table'];

    // Error logging configuration
    $logFile = $config['logfile'];

    // Connect to the database
    $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    // Check the database connection
    if ($db->connect_error) {
        // Log errors to the logfile
        error_log("Connection failed: " . $db->connect_error, 3, $logFile);
        exit(1);
    }
    // Extract field names from the XML
    $fields = [];
    foreach ($xml->item->children() as $field) {
        $fieldName = $field->getName();
        $fields[] = "`$fieldName` VARCHAR(255)";
    }
    // Create the dynamic table
   $createQuery = "CREATE TABLE $tableName (" . implode(', ', $fields) . ")";
    $db->query($createQuery);

    return $tableName;
}

function insertDataIntoDynamicTable($config, $xml, $tableName)
{
	 // Database configuration
    $dbHost = $config['database']['host'];
    $dbUser = $config['database']['user'];
    $dbPassword = $config['database']['password'];
    $dbName = $config['database']['name'];
    // Establish a database connection (replace with your database credentials)
    $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    // Check the database connection
    if ($conn->connect_error) {
       // Log errors to the logfile
        error_log("Connection failed: " . $db->connect_error, 3, $logFile);
        exit(1);
    }

    // Process and insert data into the dynamic table
    foreach ($xml->children() as $item) {
        $values = [];
        foreach ($item->children() as $field) {
            $values[] = "'" . $field . "'";
        }
        $insertQuery = "INSERT INTO $tableName VALUES (" . implode(', ', $values) . ")";
        $conn->query($insertQuery);
    }

    // Close the database connection
    $conn->close();
}

// Check if the script is being run directly or included in another script
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
$config = include 'config.php';
// Load XML data from file 
$xmlFilePath = $config['xmlfile'];
$xml = simplexml_load_file($xmlFilePath);

if ($xml === false) {
    // Log errors to the logfile
        error_log("Error: Failed to parse XML file $xmlFilePath", 3, $config['logfile']);
        exit(1);
}

// Create a dynamic table and get the table name
$tableName = createDynamicTable($config, $xml);
echo "Dynamic table created: $tableName\n</br>";

// Insert data into the dynamic table
insertDataIntoDynamicTable($config, $xml, $tableName);
echo "Data successfully inserted into the dynamic table.\n";
}
?>
