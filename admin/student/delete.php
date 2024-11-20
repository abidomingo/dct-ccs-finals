<?php
ob_start(); // Start output buffering
include("../../functions.php");
include("../partials/header.php");
include("../partials/side-bar.php");

$errorMessage = null;

// Check if ID is passed in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $studentId = $_GET['id'];

    $conn = databaseConnection();
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $errorMessage = "Student not found!";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: register.php?message=invalid_id");
    exit();
}

// Handle form submission to delete the student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $studentIdToDelete = $_POST['id'];

        $conn = databaseConnection();
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $studentIdToDelete);

        if ($stmt->execute()) {
            header("Location: register.php?message=deleted");
            exit();
        } else {
            $errorMessage = "Failed to delete the student record!";
        }

        $stmt->close();
        $conn->close();
    } else {
        $errorMessage = "Invalid student ID provided!";
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <div class="container">
        <h2>Delete a Student</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
            </ol>
        </nav>

        <!-- Alerts -->
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Confirmation Form -->
        <?php if (isset($student) && !$errorMessage): ?>
            <div class="card">
                <div class="card-body">
                    <p>Are you sure you want to delete the following student record?</p>
                    <ul>
                        <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                        <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                        <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                    </ul>
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Student Record</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
ob_end_flush(); // End output buffering
include("../partials/footer.php");
?>
