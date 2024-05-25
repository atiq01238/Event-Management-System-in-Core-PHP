<?php
session_start();
//datbase connection file
include "includes/config.php";
// error_reporting(0);
if (strlen($_SESSION["usrid"]) == 0) {
    header("location:logout.php");
} else {
    // update Process
    if (isset($_POST["submit"])) {
        $uid = $_SESSION["usrid"];
        $subject = $_POST["subject"];
        $message = $_POST["message"];
        $priority = $_POST["priority"];
        $sql =
            "INSERT INTO tickets (Subject, Message, Priority, User_id, Status) VALUES (:subject, :message, :priority, :uid, 'open')";
        $query = $dbh->prepare($sql);
        $query->bindParam(":subject", $subject, PDO::PARAM_STR);
        $query->bindParam(":message", $message, PDO::PARAM_STR);
        $query->bindParam(":priority", $priority, PDO::PARAM_STR);
        $query->bindParam(":uid", $uid, PDO::PARAM_STR);
        // Execute the query
        if ($query->execute()) {
            echo "<script>alert('Success: Ticket created successfully.');</script>";
            echo "<script>window.location.href='support.php'</script>";
        } else {
            echo "<script>alert('Error: Failed to create ticket.');</script>";
            // Print error information for debugging
            echo "<pre>";
            print_r($query->errorInfo());
            die();
        }
    } 
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <title>Event Management System | user profile </title>
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
    </head>
    <body>
    <!--body-wraper-are-start-->
    <div class="wrapper single-blog">
        
        <!--slider header area are start-->
        <div id="home" class="header-slider-area">
            <!--header start-->
            <?php include_once "includes/header.php"; ?>
            <!-- header End-->
        </div>
        <!--slider header area are end-->
        
        <!--  breadcumb-area start-->
        <div class="breadcumb-area bg-overlay">
            <div class="container">
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Support</li>
                </ol>
            </div>
        </div>
        <!--  breadcumb-area end-->
        <!-- main blog area start-->
            <div class="single-blog-area ptb100 fix">
                <div class="container">
                    <div class="row">
                        <?php //include_once "includes/myaccountbar.php"; ?>
                        <div class="col-md-12 col-sm-11">
                            <div class="single-blog-body">
                                <div class="Leave-your-thought mt50">
                                    <h3 class="aside-title uppercase">Create Ticket</h3>
                                    
                                    <div class="row">
                                        <form name="ticket" method="post">
                                            <div class="col-md-12 col-sm-6 col-xs-12 lyt-left">
                                                <div class="input-box leave-ib">
                                                    <input type="text" placeholder="Subject" class="info" name="subject" required="true">
                                                    <textarea type="text" placeholder="Message" name="message" id="message" cols="30" class="info" rows="5" style="margin-bottom: 20px;"></textarea>
                                                    <select class="info" name="priority" required="true">
                                                        <option value="">Select Priority</option>
                                                        <option value="low">Low</option>
                                                        <option value="medium">Medium</option>
                                                        <option value="high">High</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-xs-12">
                                                <div class="input-box post-comment">
                                                    <input type="submit" value="Submit" id="update" name="submit" class="submit uppercase">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--sidebar-->
                        
                    </div>
                </div>
            </div>
            <!--main blog area start-->
            <!--information area are start-->
            <?php include_once "includes/footer.php"; ?>
            <!--footer area are start-->
        </div>
        <!--body-wraper-are-end-->
                    
        <!--==== all js here====-->
        <!-- jquery latest version -->
        <script src="js/vendor/jquery-3.1.1.min.js"></script>
        <!-- bootstrap js -->
        <script src="js/bootstrap.min.js"></script>
        <!-- owl.carousel js -->
        <script src="js/owl.carousel.min.js"></script>
        <!-- meanmenu js -->
        <script src="js/jquery.meanmenu.js"></script>
        <!-- Nivo js -->
        <script src="js/nivo-slider/jquery.nivo.slider.pack.js"></script>
        <script src="js/nivo-slider/nivo-active.js"></script>
        <!-- wow js -->
        <script src="js/wow.min.js"></script>
        <!-- Youtube Background JS -->
        <script src="js/jquery.mb.YTPlayer.min.js"></script>
        <!-- datepicker js -->
        <script src="js/bootstrap-datepicker.js"></script>
        <!-- waypoint js -->
        <script src="js/waypoints.min.js"></script>
        <!-- onepage nav js -->
        <script src="js/jquery.nav.js"></script>
        <!-- animate text JS -->
        <script src="js/animate-text.js"></script>
        <!-- plugins js -->
        <script src="js/plugins.js"></script>
        <!-- main js -->
        <script src="js/main.js"></script>
    </body>
</html>
<?php } ?>
