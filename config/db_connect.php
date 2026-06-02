<?php
/**
 * Smart Traffic Violation Monitoring System
 * Database Connection File
 * 
 * This file establishes a connection between PHP and MySQL using the MySQLi extension.
 * It is included in every page that requires database operations.
 */

// 1. Connection Configurations
$db_host = "localhost";
$db_user = "root";
$db_pass = "tiger"; // Configured MySQL root password
$db_name = "traffic_violation_db";

// 2. Establish Connection
// Using the mysqli constructor
$conn = @new mysqli($db_host, $db_user, $db_pass);

// 3. Check for connection failures
if ($conn->connect_error) {
    die("<div style='font-family: Arial, sans-serif; padding: 30px; background-color: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; max-width: 600px; margin: 50px auto;'>
            <h2 style='margin-top: 0;'>⚠️ Database Connection Failed!</h2>
            <p><strong>Error Details:</strong> " . $conn->connect_error . "</p>
            <hr style='border: 0; border-top: 1px solid #fca5a5;'>
            <p><strong>To fix this, please verify:</strong></p>
            <ol style='line-height: 1.6;'>
                <li>Your local MySQL server (e.g., XAMPP, WampServer) is running.</li>
                <li>The username (<code>root</code>) and password are correct.</li>
            </ol>
         </div>");
}

// 4. Select Database and check if it exists
if (!$conn->select_db($db_name)) {
    die("<div style='font-family: Arial, sans-serif; padding: 30px; background-color: #fffbeb; border: 1px solid #fde047; color: #854d0e; border-radius: 8px; max-width: 600px; margin: 50px auto;'>
            <h2 style='margin-top: 0;'>⚠️ Database '" . $db_name . "' Not Found!</h2>
            <p>The connection was successful, but the database does not exist yet.</p>
            <hr style='border: 0; border-top: 1px solid #fde047;'>
            <p><strong>To fix this:</strong></p>
            <ol style='line-height: 1.6;'>
                <li>Open <strong>phpMyAdmin</strong> (usually at <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a>).</li>
                <li>Go to the <strong>SQL</strong> tab.</li>
                <li>Copy the content of <code>db/database.sql</code> and click <strong>Go</strong> to create the database and tables automatically.</li>
                <li>Or run the command: <code>mysql -u root -p &lt; db/database.sql</code> in your terminal.</li>
            </ol>
         </div>");
}

// Set character set to UTF-8 to handle any special characters properly
$conn->set_charset("utf8");

// Export connection variable $conn
?>
