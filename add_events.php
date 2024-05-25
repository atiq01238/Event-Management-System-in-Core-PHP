<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['usrid'])==0)
{   
header('location:logout.php');
}
else{ 
if(isset($_POST['add']))
{
// Posted Values
$catid=$_POST['category'];
$spnserid=$_POST['sponser'];
$ename=$_POST['eventname'];
$ediscription=$_POST['evetndescription'];
$esdate=$_POST['eventstartdate'];
$eedate=$_POST['eventenddate'];
$elocation=$_POST['eventlocation'];
$entimage=$_FILES["eventimage"]["name"];
$price_of_tickets = $_POST['price_of_tickets'];
$attendi_no_of_tickets = $_POST['attendi_no_of_tickets'];
$status=0;
// get the image extension
$extension = substr($entimage,strlen($entimage)-4,strlen($entimage));
// allowed extensions
$allowed_extensions = array(".jpg","jpeg",".png",".gif");
// Validation for allowed extensions .in_array() function searches an array for a specific value.
if(!in_array($extension,$allowed_extensions))
{
echo "<script>alert('Invalid format. Only jpg / jpeg/ png /gif format allowed');</script>";
}
else
{
//rename the image file
$eventimage=md5($entimage).$extension;
// Code for move image into directory
move_uploaded_file($_FILES["eventimage"]["tmp_name"],"admin/eventimages/".$eventimage);
// Query for insertion data into database
$sql="INSERT INTO  tblevents(CategoryId,SponserId,EventName,EventDescription,EventStartDate,EventEndDate,EventLocation,EventImage,IsActive,attendi_no_of_tickets,price_of_tickets) VALUES(:catid,:spnserid,:ename,:ediscription,:esdate,:eedate,:elocation,:eventimage,:status,:attendi_no_of_tickets,:price_of_tickets)";
$query = $dbh->prepare($sql);
$query->bindParam(':catid',$catid,PDO::PARAM_STR);
$query->bindParam(':spnserid',$spnserid,PDO::PARAM_STR);
$query->bindParam(':ename',$ename,PDO::PARAM_STR);
$query->bindParam(':ediscription',$ediscription,PDO::PARAM_STR);
$query->bindParam(':esdate',$esdate,PDO::PARAM_STR);
$query->bindParam(':eedate',$eedate,PDO::PARAM_STR);
$query->bindParam(':elocation',$elocation,PDO::PARAM_STR);
$query->bindParam(':eventimage',$eventimage,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->bindParam(':attendi_no_of_tickets',$attendi_no_of_tickets,PDO::PARAM_STR);
$query->bindParam(':price_of_tickets',$attendi_no_of_tickets,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo '<script>alert("Event created successfully")</script>';
echo "<script>window.location.href='manage-events.php</script>";  
}
else 
{
echo '<script>alert("Something went wrong. Please try again")</script>';   
}

}
}    
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <title>EMS | Add Event</title>

    <!-- bootstrap v3.3.6 css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- animate css -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- meanmenu css -->
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <!-- icofont css -->
    <link rel="stylesheet" href="css/icofont.css">
    <!-- Nivo css -->
    <link rel="stylesheet" href="css/nivo-slider.css">
    <!-- animaton text css -->
    <link rel="stylesheet" href="css/animate-text.css">
    <!-- Metrial iconic fonts css -->
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="style.css">
    <!-- responsive css -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- color css -->
    <link href="css/color/skin-default.css" rel="stylesheet">

    <!-- modernizr css -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>


    <style>
    .errorWrap {
        padding: 10px;
        margin: 0 0 20px 0;
        background: #fff;
        border-left: 4px solid #dd3d36;
        -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
    }

    .succWrap {
        padding: 10px;
        margin: 0 0 20px 0;
        background: #fff;
        border-left: 4px solid #5cb85c;
        -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
    }
    </style>
</head>

<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <!-- / Header -->
            <?php include_once('includes/header.php');?>
            <!-- / Leftbar -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="page-header"> Add Event</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Event
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">

                                    <!-- Success / Error Message -->
                                    <?php if($error){?><div class="errorWrap"><strong>ERROR</strong> :
                                        <?php echo htmlentities($error); ?> </div><?php } 
                                     else if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>

                                    <form role="form" method="post" enctype="multipart/form-data">


                                        <!--categrory Name -->
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control" name="category" autocomplete="off" required>
                                                <option>Select</option>
                                                <?php
                                                $sql = "SELECT id,CategoryName,CategoryDescription,CreationDate,IsActive from tblcategory";
                                                $query = $dbh -> prepare($sql);
                                                $query->execute();
                                                $results=$query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt=1;
                                                if($query->rowCount() > 0)
                                                {
                                                foreach($results as $row)
                                                { ?>
                                                <option value="<?php echo htmlentities($row->id);?>">
                                                    <?php echo htmlentities($row->CategoryName);?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>


                                        <!--Sponser logo -->
                                        <div class="form-group">
                                            <label>Event Sponsors : </label>

                                            <select class="form-control" name="sponser" autocomplete="off" required>
                                                <option>Select</option>
                                                <?php
                                $sql = "SELECT id,sponserName from tblsponsers";
                                $query = $dbh -> prepare($sql);
                                $query->execute();
                                $results=$query->fetchAll(PDO::FETCH_OBJ);
                                $cnt=1;
                                if($query->rowCount() > 0)
                                {
                                foreach($results as $row)
                                { ?>
                                                                                <option value="<?php echo htmlentities($row->id);?>">
                                                    <?php echo htmlentities($row->sponserName);?></option>
                                                <?php }} ?>
                                            </select>

                                        </div>

                                        <!--Event name -->
                                        <div class="form-group">
                                            <label>Event Name</label>
                                            <input class="form-control" type="text" name="eventname" autocomplete="off"
                                                required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <label>Attende No of Tickets</label>
                                            <input class="form-control" type="text" name="attendi_no_of_tickets" autocomplete="off"
                                                required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <label>Price of Tickets</label>
                                            <input class="form-control" type="text" name="price_of_tickets" autocomplete="off"
                                                required autofocus>
                                        </div>

                                        <!--Event Description -->
                                        <div class="form-group">
                                            <label>Event Description</label>
                                            <textarea class="form-control" type="text" name="evetndescription" rows="5"
                                                autocomplete="off" required autofocus></textarea>
                                        </div>

                                        <!--Event Start date -->
                                        <div class="form-group">
                                            <label>Event Start Date</label>
                                            <input class="form-control" type="date" name="eventstartdate"
                                                autocomplete="off" required autofocus />
                                        </div>

                                        <!--Event End Date -->
                                        <div class="form-group">
                                            <label>Event End Date</label>
                                            <input class="form-control" type="date" name="eventenddate"
                                                autocomplete="off" required autofocus />
                                        </div>

                                        <!--Event Location -->
                                        <div class="form-group">
                                            <label>Event location</label>
                                            <input class="form-control" type="text" name="eventlocation"
                                                autocomplete="off" required autofocus />
                                        </div>

                                        <!--Event Featured Image -->
                                        <div class="form-group">
                                            <label>Event Featured Image</label>
                                            <input class="form-control" type="file" name="eventimage" autocomplete="off"
                                                required autofocus />
                                        </div>

                                        <!--Button -->
                                        <button type="submit" class="btn btn-default" name="add">Add Event</button>
                                    </form>
                                </div>

                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <div class="col-md-2"></div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
</body>

</html>
<?php } ?>