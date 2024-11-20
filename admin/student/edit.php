<?php
include("../../functions.php");
include("../partials/header.php");
include("../partials/side-bar.php");

$errorMessage = null;
$message = '';
$messageType = '';

$conn = databaseConnection();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $studentId = $_GET['id'];

    // Fetch the student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $studentId = $student['id'];
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
    $studentCode = trim($_POST['studentCode']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);

    if (empty($studentCode) || empty($firstName) || empty($lastName)) {
        $errorMessage = "All fields are required!";
    } else {
        $stmt = $conn->prepare("UPDATE students SET student_id = ?, first_name = ?, last_name = ? WHERE id = ?");
        $stmt->bind_param("sssi", $studentCode, $firstName, $lastName, $studentId);

        if ($stmt->execute()) {
            $message = "Student details successfully updated!";
            $messageType = "success";
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

        <!-- Success Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                        <input type="text" class="form-control" id="studentCode" name="studentCode" value="<?php echo htmlspecialchars($studentCode); ?>">
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
?>
