<?php

// Include the main processing script
include 'process_xml.php';


// Provide test XML data
$xmlString = '<data><item><entity_id>340</entity_id><CategoryName><![CDATA[Green Mountain Ground Coffee]]></CategoryName><sku>20</sku><name><![CDATA[Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag]]></name><description></description><shortdesc><![CDATA[Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag steeps cup after cup of smoky-sweet, complex dark roast coffee from Green Mountain Ground Coffee.]]></shortdesc><price>41.6000</price><link>http://www.coffeeforless.com/green-mountain-coffee-french-roast-ground-coffee-24-2-2oz-bag.html</link><image>http://mcdn.coffeeforless.com/media/catalog/product/images/uploads/intro/frac_box.jpg</image><Brand><![CDATA[Green Mountain Coffee]]></Brand><Rating>0</Rating><CaffeineType>Caffeinated</CaffeineType><Count>24</Count><Flavored>No</Flavored><Seasonal>No</Seasonal><Instock>Yes</Instock><Facebook>1</Facebook><IsKCup>0</IsKCup></item></data>';

// Load XML data from the string
$xml = simplexml_load_string($xmlString);

// Check if XML parsing was successful
if ($xml === false) {
    // Log errors to the logfile
    error_log("Error: Failed to parse XML data from string", 3, $config['logfile']);
    exit(1);
}

//Mock the database configuration for testing
$config['database']['host'] = 'localhost';
$config['database']['user'] = 'root';
$config['database']['password'] = '';
$config['database']['name'] = 'xml';
$config['database']['table'] = 'xml';


// Error logging configuration for testing
$config['logfile'] = 'error.log';

// Mock the XML file path for testing
$config['xmlfile'] = 'test_feed.xml';

// Create a dynamic table and get the table name
$tableName = createDynamicTable($config, $xml);
echo "Dynamic table created: $tableName\n</br>";

// Insert data into the dynamic table
insertDataIntoDynamicTable($config, $xml, $tableName);
echo "Data successfully inserted into the dynamic table.\n";

echo "Test completed.\n";
?>
