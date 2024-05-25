<?php
include ('../venu/include/function.php');
include('../includes/config.php');

session_start();
error_reporting(0);

if(strlen($_SESSION['adminsession'])==0) {   
    header('location:logout.php');
} else {
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

     if(isset($_POST['submit'])) {
        $venuName = $_POST['venuname'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $venuPrice = $_POST['venu_price'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $totalPrice = $_POST['total_price'];
        $location = $_POST['location'];
        // Assuming image uploads are handled separately

            // Handle file uploads
        $image1Name = '';
        $image2Name = '';
        $image3Name = '';

        if(isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
            $image1TmpName = $_FILES['image1']['tmp_name'];
            $image1Name = uniqid('image1_') . '_' . $_FILES['image1']['name'];
            move_uploaded_file($image1TmpName, 'upload/' . $image1Name);
        }

        if(isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
            $image2TmpName = $_FILES['image2']['tmp_name'];
            $image2Name = uniqid('image2_') . '_' . $_FILES['image2']['name'];
            move_uploaded_file($image2TmpName, 'upload/' . $image2Name);
        }

        if(isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
            $image3TmpName = $_FILES['image3']['tmp_name'];
            $image3Name = uniqid('image3_') . '_' . $_FILES['image3']['name'];
            move_uploaded_file($image3TmpName, 'upload/' . $image3Name);
        }

        // Insert data into tblcreate_venu
        $sqlInsert = "INSERT INTO `tblcreate_venu`(`venuname`, `category`, `description`, `venu_price`, `start_date`, `end_date`, `total_price`, `location`, `image1`, `image2`, `image3`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbh->prepare($sqlInsert);
        $stmt->execute([$venuName, $category, $description, $venuPrice, $startDate, $endDate, $totalPrice, $location, $image1Name, $image2Name, $image3Name]);

        // Redirect after insertion
        header('Location: demo.php'); // Redirect to a success page
        exit();
    }
}
?>

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
                                                <select name="venu_price" required class="form-control" id="price" onchange="calculateTotalPrice()">
                                                    <option value="">Select Price</option>
                                                    <?php foreach($venues as $venue) { ?>
                                                        <option value="<?php echo htmlentities($venue->VenuPrice); ?>"><?php echo htmlentities($venue->VenuPrice); ?></option>
                                                    <?php } ?>
                                                </select>
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
                                                    <input type="text" name="location" required class="form-control" id="location" placeholder="Location" readonly>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-secondary" onclick="getLocation()">Get Current Location</button>
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
        <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else { 
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var apiKey = 'YOUR_OPENCAGE_API_KEY';
            var url = `https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        var address = data.results[0].formatted;
                        document.getElementById("location").value = address;
                    } else {
                        alert('No address found');
                    }
                })
                .catch(error => {
                    alert('Error fetching address: ' + error);
                });
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }

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