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

// Login function
function login($email, $password) {
    $conn = databaseConnection ();

    // Use MD5 for password hashing
    $hashed_password = md5($password);

    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, start a session
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = $result->fetch_assoc(); // Store user data
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}
// Function to display alerts with multiple errors
function displayAlert($errors, $type = 'danger') {
    $message = "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                    <strong>System Errors:</strong> Please correct the following errors.<ul>";
    foreach ($errors as $error) {
        $message .= "<li>$error</li>";
    }
    $message .= "</ul>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    return $message;
}


?>