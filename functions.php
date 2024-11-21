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

//Logout function 
function logout() {
    session_destroy();
    header("Location:/index.php");
}


// Function to get selected student data
function getSelectedStudentData($student_id) {
    $connection = databaseConnection();
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    $stmt->close();
    $connection->close();

    return $student;
}


function countStudentsPassed($connection) {
    $query = "
        SELECT COUNT(*) AS passed_count
        FROM (
            SELECT student_id, AVG(grade) AS avg_grade
            FROM students_subjects
            WHERE grade IS NOT NULL
            GROUP BY student_id
            HAVING avg_grade >= 75
        ) AS passed_students";
    
    $result = $connection->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return (int) ($row['passed_count'] ?? 0);
    }

    return 0; // Return 0 if the query fails
}


function countStudentsFailed($connection) {
    $query = "
        SELECT COUNT(*) AS failed_count
        FROM (
            SELECT student_id, AVG(grade) AS avg_grade
            FROM students_subjects
            WHERE grade IS NOT NULL
            GROUP BY student_id
            HAVING avg_grade < 75
        ) AS failed_students";
    
    $result = $connection->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return (int) ($row['failed_count'] ?? 0);
    }

    return 0; // Return 0 if the query fails
}


function totalRegisteredStudents($db) {
    $query = "SELECT COUNT(*) AS total_students FROM students";
    $result = $db->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return (int) ($row['total_students'] ?? 0);
    }

    return 0; // Return 0 if the query fails
}


function totalSubjectsInDatabase($db) {
    $query = "SELECT COUNT(*) AS total_subjects FROM subjects";
    $result = $db->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return (int) ($row['total_subjects'] ?? 0);
    }

    return 0; // Return 0 if the query fails
}



?>