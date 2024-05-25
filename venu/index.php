<?php
include ('../venu/include/function.php');
include('../includes/config.php');


session_start();
error_reporting(E_ALL);

// Debugging: Check if session is started and variables are set
// if (!isset($_SESSION['usersession']) || strlen($_SESSION['usersession']) == 0) {
//     header('location:logout.php'); // Redirect to logout if session is not valid
//     exit();
// }

// Ensure 'usrid' is set in session
if (!isset($_SESSION['usrid'])) {
    // Handle the error, perhaps redirect to a login page or display an error message
    die('User ID is not set. Please log in.');
}

// Fetch the user ID from session
$usrid = $_SESSION['usrid'];

// Fetch categories
$sql = "SELECT id, CategoryName FROM tblcategory WHERE IsActive=1";
$query = $dbh->prepare($sql);
$query->execute();
$categories = $query->fetchAll(PDO::FETCH_OBJ);

// Fetch venue prices
$sql = "SELECT id, VenuPrice, VenuDescription FROM tblvenu WHERE IsActive=1";
$query = $dbh->prepare($sql);
$query->execute();
$venues = $query->fetchAll(PDO::FETCH_OBJ);

if (isset($_POST['submit'])) {
    $venuName = $_POST['venuname'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $venuPrice = $_POST['venu_price'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $totalPrice = $_POST['total_price'];
    $location = $_POST['location'];
    
    // Handle file uploads
    $image1Name = '';
    $image2Name = '';
    $image3Name = '';

    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $image1TmpName = $_FILES['image1']['tmp_name'];
        $image1Name = uniqid('image1_') . '_' . $_FILES['image1']['name'];
        move_uploaded_file($image1TmpName, 'upload/' . $image1Name);
    }

    if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
        $image2TmpName = $_FILES['image2']['tmp_name'];
        $image2Name = uniqid('image2_') . '_' . $_FILES['image2']['name'];
        move_uploaded_file($image2TmpName, 'upload/' . $image2Name);
    }

    if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
        $image3TmpName = $_FILES['image3']['tmp_name'];
        $image3Name = uniqid('image3_') . '_' . $_FILES['image3']['name'];
        move_uploaded_file($image3TmpName, 'upload/' . $image3Name);
    }

    // Insert data into tblcreate_venu
    $sqlInsert = "INSERT INTO `tblcreate_venu`(`venuname`, `category`, `description`, `venu_price`, `start_date`, `end_date`, `total_price`, `location`, `image1`, `image2`, `image3`, `Userid`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($sqlInsert);
    $stmt->execute([$venuName, $category, $description, $venuPrice, $startDate, $endDate, $totalPrice, $location, $image1Name, $image2Name, $image3Name, $usrid]);
    
    // Redirect after insertion
    header('Location: index.php'); // Redirect to a success page
    exit();

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EMS | Create Venue</title>

    <!-- Google Font: Source Sans Pro -->
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
    <link rel="stylesheet" href="<?= url('plugins/summernote/summernote-bs4.min.css') ?>">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <!-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div> -->

        <!-- Navbar -->
        <?php include('../venu/layout/navbar.php')?>

        <!-- Main Sidebar Container -->
        <?php include('../venu/layout/sidebar.php')?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Create Venue</h3>
                                </div>
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-5">
                                                <label for="VenuName">Venue Name</label>
                                                <input type="text" name="venuname" required class="form-control" id="VenuName" placeholder="Venue Name">
                                            </div>
                                            <div class="form-group col-md-7">
                                                <label for="CategoryName">Category Name</label>
                                                <select name="category" required class="form-control" id="CategoryName">
                                                    <option value="">Select Category</option>
                                                    <?php foreach($categories as $category) { ?>
                                                        <option value="<?php echo htmlentities($category->id); ?>"><?php echo htmlentities($category->CategoryName); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <input type="text" name="description" required class="form-control" id="description" placeholder="Venue Description">
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                            <label for="price">Per Head Price</label>
                                            <input type="text" name="price" required class="form-control" id="price" placeholder="Per Head Price">
                                        
                                        </div>
                                            <div class="form-group col-md-4">
                                                <label for="startDate">Start Date</label>
                                                <input type="text" name="start_date" required class="form-control flatpickr" id="startDate" placeholder="Start Date" onchange="calculateTotalPrice()">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="endDate">End Date</label>
                                                <input type="text" name="end_date" required class="form-control flatpickr" id="endDate" placeholder="End Date" onchange="calculateTotalPrice()">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="total-price">Total Price</label>
                                                <input type="text" name="total_price" required class="form-control" id="total-price" placeholder="Complete Price" readonly>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label for="location">Location</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter location">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="image1" class="custom-file-input" id="exampleInputFile">
                                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div><br>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="image2" class="custom-file-input" id="exampleInputFile">
                                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div><br>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="image3" class="custom-file-input" id="exampleInputFile">
                                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Main content -->
           
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include('../venu/layout/footer.php')?>


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
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

    <script>

        $(document).ready(function() {
            $('.flatpickr').flatpickr({
                dateFormat: 'Y-m-d',
                autoclose: true,
                todayHighlight: true
            });
        });

        function calculateTotalPrice() {
        var price = document.getElementById('price').value;
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;

        if (price && startDate && endDate) {
            var start = new Date(startDate);
            var end = new Date(endDate);
            var diffTime = Math.abs(end - start);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Including the end date
            var totalPrice = price * diffDays;

                document.getElementById('total-price').value = totalPrice;
            }
        }
    </script>
</body>

</html>
