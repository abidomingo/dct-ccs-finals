<?php    
      function databaseConnection (): mysqli{ 
       $host = "localhost";
       $user = "root";
       $password = ""; // Default password for Laragon
       $dbname = "dct_ccs_finals";
    
       $conn = new mysqli($host, $user, $password, $dbname);
    
    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>