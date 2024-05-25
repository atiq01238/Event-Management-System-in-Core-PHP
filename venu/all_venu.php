<?php
session_start(); // Start the session

include('../venu/include/function.php');
include('../includes/config.php');

if (!isset($_SESSION['usrid'])) {
    header("Location: ../signin.php"); // Redirect to login page if not logged in
    exit();
}

// Check if a message is set in the session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Clear the message after displaying

// Handle deletion if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check permission status
    $permission_sql = "SELECT permission_status FROM tblcreate_venu WHERE id = :id";
    $permission_query = $dbh->prepare($permission_sql);
    $permission_query->bindParam(':id', $id, PDO::PARAM_INT);
    $permission_query->execute();
    $permission_status = $permission_query->fetchColumn();

    // Check if user has permission
    if ($permission_status == 1) {
        // Delete query
        $sql = "DELETE FROM tblcreate_venu WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        // Set success message
        $_SESSION['message'] = "Your request has been sent to the admin.";
    } else {
        // Set message indicating permission required
        $_SESSION['message'] = "You cannot delete this venue without admin permission.";
    }

    // Redirect to the dashboard or venue list page
    header("Location: all_venu.php");
    exit();
}

// Pagination variables
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Fetch data from the database with pagination
$Userid = $_SESSION['usrid'];
$sql = "SELECT * FROM tblcreate_venu WHERE Userid = :Userid AND approved = 1 LIMIT :offset, :results_per_page";
$query = $dbh->prepare($sql);
$query->bindParam(':Userid', $Userid, PDO::PARAM_INT);
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
$query->execute();
$venues = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of rows for pagination
$sql_count = "SELECT COUNT(*) AS count FROM tblcreate_venu WHERE Userid = :Userid AND approved = 1";
$count_query = $dbh->prepare($sql_count);
$count_query->bindParam(':Userid', $Userid, PDO::PARAM_INT);
$count_query->execute();
$row_count = $count_query->fetch(PDO::FETCH_ASSOC);
$total_records = $row_count['count'];
$total_pages = ceil($total_records / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Dashboard</title>

    <!-- Include CSS and JavaScript files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?= url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= url('plugins/jqvmap/jqvmap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url('dist/css/adminlte.min.css') ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= url('plugins/daterangepicker/daterangepicker.css') ?>">
    <!-- summernote -->
    <link rel="stylesheet" href="<?= url('plugins/summernote/summernote-bs4.min.css') ?>"></div>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include('../venu/layout/navbar.php')?>

        <!-- Main Sidebar Container -->
        <?php include('../venu/layout/sidebar.php')?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <!-- Include content header here -->
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Message display -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-12">
                                        <div class="card-header">
                                            <h3 class="card-title">Venue Data</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Venue Name</th>
                                                        <th>Category</th>
                                                        <th>Description</th>
                                                        <!-- <th>Venue Price</th> -->
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Total Price</th>
                                                        <th>Location</th>
                                                        <th>Images</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if ($venues): ?>
                                                    <?php foreach ($venues as $venue): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($venue['venuname']); ?></td>
                                                            <td><?php echo htmlspecialchars($venue['category']); ?></td>
                                                            <td><?php echo htmlspecialchars($venue['description']); ?></td>
                                                            <td><?php echo htmlspecialchars($venue['start_date']); ?></td>
                                                            <td><?php echo htmlspecialchars($venue['end_date']); ?></td>
                                                            <td><?php echo htmlspecialchars($venue['total_price']); ?></td>
                                                            <td><?php echo htmlspecialchars($venue['location']); ?></td>
                                                            <td>
                                                                <?php if (!empty($venue['image1'])): ?>
                                                                    <img src="upload/<?php echo htmlspecialchars($venue['image1']); ?>" alt="Image 1" style="max-width: 100px;">
                                                                <?php endif; ?>
                                                                <?php if (!empty($venue['image2'])): ?>
                                                                    <img src="upload/<?php echo htmlspecialchars($venue['image2']); ?>" alt="Image 2" style="max-width: 100px;">
                                                                <?php endif; ?>
                                                                <?php if (!empty($venue['image3'])): ?>
                                                                    <img src="upload/<?php echo htmlspecialchars($venue['image3']); ?>" alt="Image 3" style="max-width: 100px;">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($venue['permission_status'] == 1): ?>
                                                                    Approved
                                                                <?php else: ?>
                                                                    Pending
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($venue['permission_status'] == 0): ?>
                                                                    <a href="request_permission.php?id=<?php echo $venue['id']; ?>" class="btn btn-primary btn-sm">Request Permission</a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Pagination -->
                            <div class="clearfix">
                                <ul class="pagination pagination-sm m-0 float-right">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php endfor; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Include footer here -->

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <!-- Include JavaScript files -->
    <script src="<?= url('plugins/jquery/jquery.min.js') ?>"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?= url('plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?= url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- ChartJS -->
    <script src="<?= url('plugins/chart.js/Chart.min.js') ?>"></script>
    <!-- Sparkline -->
    <script src="<?= url('plugins/sparklines/sparkline.js') ?>"></script>
    <!-- JQVMap -->
    <script src="<?= url('plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
    <script src="<?= url('plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?= url('plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
    <!-- daterangepicker -->
    <script src="<?= url('plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= url('plugins/daterangepicker/daterangepicker.js') ?>"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
    <!-- Summernote -->
    <script src="<?= url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
    <!-- overlayScrollbars -->
    <script src="<?= url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= url('dist/js/adminlte.js') ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= url('dist/js/demo.js') ?>"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?= url('dist/js/pages/dashboard.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
