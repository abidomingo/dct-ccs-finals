<!-- Template Files here -->
<?php
$title = "Dashboard";
include 'partials/header.php';
include 'partials/side-bar.php';
include("../functions.php");

$connection = databaseConnection();

// Fetch data using the functions
$passedCount = countStudentsPassed($connection);
$failedCount = countStudentsFailed($connection);
$totalStudents = totalRegisteredStudents($connection);
$totalSubjects = totalSubjectsInDatabase($connection);

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        
    
    <div class="row mt-5">
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                <h5 class="card-title"><?php echo htmlspecialchars($totalSubjects); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-success">
                <h5 class="card-title"><?php echo htmlspecialchars($totalStudents); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                <h5 class="card-title"><?php echo htmlspecialchars($failedCount); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                <h5 class="card-title"><?php echo htmlspecialchars($passedCount); ?></h5>
                </div>
            </div>
        </div>
    </div>    
</main>
<!-- Template Files here -->
<?php
 
 include 'partials/footer.php'; 
 ?>