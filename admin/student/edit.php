<?php
ob_start(); // Start output buffering to prevent headers being sent before the redirect

include("../../functions.php");
include("../partials/header.php");
include("../partials/side-bar.php");

$errorMessage = null;

$conn = databaseConnection();

// Ensure the student ID exists in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $studentId = $_GET['id'];

    // Fetch the student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $studentCode = $student['student_id'];
        $firstName = $student['first_name'];
        $lastName = $student['last_name'];
    } else {
        // Redirect back if no student found
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}

// Handle the update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);

    // Validate inputs
    if (empty($firstName) || empty($lastName)) {
        $errorMessage = "All fields are required!";
    } else {
        // Update student details
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $firstName, $lastName, $studentId);

        if ($stmt->execute()) {
            // Redirect back to the register page after successful update
            header("Location: register.php");
            exit; // Ensure no further code is executed after redirection
        } else {
            $errorMessage = "Error occurred while updating the student.";
        }
    }
}

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <div class="container">
        <h2>Edit Student</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
            </ol>
        </nav>

        <!-- Error Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $errorMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="studentCode" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="studentCode" name="studentCode" value="<?php echo htmlspecialchars($studentCode); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php
$conn->close();
include("../partials/footer.php");
ob_end_flush(); // Flush and turn off output buffering
?>
