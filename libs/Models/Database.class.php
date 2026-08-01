<?php
class Database {
    public static $conn = null;

    public static function getConnection() {
        if(Database::$conn == null) {
            $servername = 'localhost';
            $username = 'root';
            $password = 'sdfk2RCIfmkosC4CQ1E1E';
            $dbname = 'elite-fort';

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } else {
                // printf("New Connection Establishing....\n");
                Database::$conn = $conn;
                return Database::$conn;
            }
        }else {
            // printf("Returning Existing Connection...\n");
            return Database::$conn;
        }
    }
}