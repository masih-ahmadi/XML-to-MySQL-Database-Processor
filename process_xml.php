<?php

class XmlToDatabaseProcessor
{
    private $config;
    private $db;

    public function __construct($config)
    {
        $this->config = $config;

        // Initialize the database connection
        $this->initDatabase();
    }

    private function initDatabase()
    {
        // Database configuration
        $dbHost = $this->config['database']['host'];
        $dbUser = $this->config['database']['user'];
        $dbPassword = $this->config['database']['password'];
        $dbName = $this->config['database']['name'];

        // Establish a database connection
        $this->db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        // Check the database connection
        if ($this->db->connect_error) {
            // Log errors to the logfile
            error_log("Connection failed: " . $this->db->connect_error, 3, $this->config['logfile']);
            exit(1);
        }
    }

    public function createDynamicTable($xml)
    {
        // Create a unique table name
        $tableName = 'xml_table_' . time();

        // Extract field names from the XML
        $fields = [];
        foreach ($xml->item->children() as $field) {
            $fieldName = $field->getName();
            $fields[] = "`$fieldName` VARCHAR(255)";
        }

        // Create the dynamic table
        $createQuery = "CREATE TABLE $tableName (" . implode(', ', $fields) . ")";
        $this->db->query($createQuery);

        return $tableName;
    }

    public function insertDataIntoDynamicTable($xml, $tableName)
    {
        // Process and insert data into the dynamic table
        foreach ($xml->children() as $item) {
            $values = [];
            foreach ($item->children() as $field) {
                $values[] = "'" . $field . "'";
            }
            $insertQuery = "INSERT INTO $tableName VALUES (" . implode(', ', $values) . ")";
            $this->db->query($insertQuery);
        }
    }

    public function closeDatabase()
    {
        // Close the database connection
        $this->db->close();
    }
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

    // Create an instance of the XmlToDatabaseProcessor class
    $processor = new XmlToDatabaseProcessor($config);

    // Create a dynamic table and get the table name
    $tableName = $processor->createDynamicTable($xml);
    echo "Dynamic table created: $tableName\n</br>";

    // Insert data into the dynamic table
    $processor->insertDataIntoDynamicTable($xml, $tableName);
    echo "Data successfully inserted into the dynamic table.\n";

    // Close the database connection
    $processor->closeDatabase();
}
?>
