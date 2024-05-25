<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['adminsession']) == 0) {   
    header('location: logout.php');
    exit;
}

if (isset($_GET['sid']) && isset($_GET['status'])) {
    // Sanitize input
    $eventid = intval($_GET['sid']);
    $status = $_GET['status'] ; // Ensure status is either 0 or 1

    
    try {
        // Update the database
        $sql = "UPDATE tblevents SET IsActive = :status WHERE id = :eventid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_INT);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->execute();

        // Check if the update was successful
        if ($query->rowCount() > 0) {
            // Redirect to a success page
            header('location: manage-events.php?success=true');
            exit;
        } else {
            // Redirect with an error message
            header('location: manage-events.php?error=true');
            exit;
        }
    } catch (PDOException $e) {
        // Redirect to an error page or display an error message
        header('location: error.php?message=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Redirect to an error page
    header('location: error.php');
    exit;
}
?>
