<?php
ob_start();
include("../../functions.php");
include("../partials/header.php");
include("../partials/side-bar.php");

// Initialize variables
$errorMessage = null;
$successMessage = null;
$subject = null;

// Fetch subject details based on ID from query parameters
if (isset($_GET['id'])) {
    $subjectId = $_GET['id'];
    $conn = databaseConnection ();

    $stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $subjectId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subject = $result->fetch_assoc();
    } else {
        $errorMessage = "Subject not found!";
    }

    $stmt->close();
    $conn->close();
}

// Handle form submission for updating subject details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectId = $_POST['id'];
    $updatedSubjectName = trim($_POST['subjectName']);

    if (empty($updatedSubjectName)) {
        $errorMessage = "Subject name is required!";
    } else {
        $conn = databaseConnection ();

        // Check if subject name already exists
        $checkStmt = $conn->prepare("SELECT * FROM subjects WHERE subject_name = ? AND id != ?");
        $checkStmt->bind_param("si", $updatedSubjectName, $subjectId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $errorMessage = "The subject name already exists!";
        } else {
            // Update subject details if no duplicates are found
            $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
            $stmt->bind_param("si", $updatedSubjectName, $subjectId);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: add.php?message=updated");
                exit();
            } else {
                $errorMessage = "Failed to update subject details!";
            }

            $stmt->close();
        }

        $checkStmt->close();
        $conn->close();
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <div>
        <h2>Edit Subject</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
            </ol>
        </nav>

        <!-- Display Error Messages -->
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $errorMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Subject Edit Form -->
        <?php if ($subject): ?>
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                        <div class="mb-3">
                            <label for="subjectCode" class="form-label">Subject Code</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="subjectCode" 
                                name="subjectCode" 
                                value="<?php echo $subject['subject_code']; ?>" 
                                readonly
                            >
                        </div>
                        <div class="mb-3">
                            <label for="subjectName" class="form-label">Subject Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="subjectName" 
                                name="subjectName" 
                                value="<?php echo $subject['subject_name']; ?>" 
                                placeholder="Enter Subject Name"
                            >
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary w-100">Update Subject</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include("../partials/footer.php");
ob_end_flush();
?>
