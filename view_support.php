<?php
session_start();
//datbase connection file
include "includes/config.php";
error_reporting(0);

if (strlen($_SESSION["usrid"]) == 0) {
    header("location:logout.php");
}else{

    $ticket_id = $_GET['id'];
    $sql = "SELECT * FROM tickets WHERE id = :ticket_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        echo "<script>alert('Ticket Not Found.');</script>";
        echo "<script>window.location.href='support.php'</script>";
    }
    if ($ticket['user_id'] != $_SESSION["usrid"]) {
        echo "<script>alert('This Ticket is not associated with your user..');</script>";
        echo "<script>window.location.href='support.php'</script>";
    }


    // update Process
    if (isset($_POST["submit"])) {
        $tid = $_POST["ticket_id"];
        $subject = $_POST["subject"];
        $message = $_POST["message"];
        $sql =
            "INSERT INTO reply (Subject, Message, ticket_id, created_by) VALUES (:subject, :message, :tid, 'user')";
        $query = $dbh->prepare($sql);
        $query->bindParam(":subject", $subject, PDO::PARAM_STR);
        $query->bindParam(":message", $message, PDO::PARAM_STR);
        $query->bindParam(":tid", $tid, PDO::PARAM_STR);
        // Execute the query
        if ($query->execute()) {
            echo "<script>alert('Success: Ticket Reply Created successfully.');</script>";
            echo "<script>window.location.href='view_support.php?id=". $_GET['id'] ."  '</script>";
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
        <title>Event Management System | View Support </title>
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
    <style>
        .message-container {
            margin: 20px auto;
            padding: 20px 0px;
        }
        .message {
            margin-bottom: 20px;
            overflow: hidden;
        }
        .message .avatar {
            float: right;
            margin-left: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .message .content {
            padding: 10px;
            border-radius: 5px;
            background-color: #e0e0e0;
            display: inline-block;
            float: right;
            min-width: 450px;
            max-width: 500px;
        }
        .message.admin .avatar {
            float: left;
            margin-right: 10px;
        }
        .message.admin .content {
            float: left;
            background-color: #c7edfc;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
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
                                    <style type="text/css">
                                        .custom-ulli ul li{
                                            display: block;
                                        }
                                    </style>
                                <div class="Leave-your-thought mt50 custom-ulli">
                                    <?php
                                        // Check if the $_GET['id'] variable is set
                                    if (!isset($_GET['id'])) {
                                        echo "Ticket ID not provided.";
                                        exit;
                                    }

                                    // Get the ticket ID from the URL parameter

                                    // Assuming $dbh is your PDO connection object
                                    // Check if the ticket exists
                                    if ($ticket) {
                                        // Output the ticket data in an unordered list format
                                        echo "<ul style='display: block;        '>";
                                        echo "<li><strong>ID:</strong> " . $ticket['id'] . "</li>";
                                        echo "<li><strong>Subject:</strong> " . $ticket['subject'] . "</li>";
                                        //echo "<li><strong>Message:</strong> " . $ticket['message'] . "</li>";
                                        echo "<li><strong>Priority:</strong> " . $ticket['priority'] . "</li>";
                                        $status = $ticket['status'];
                                        $badgeClass = '';

                                        if ($status == 'closed') {
                                            $badgeClass = 'label-success';
                                            $statusText = 'Closed';
                                        } elseif ($status == 'open') {
                                            $badgeClass = 'label-default';
                                            $statusText = 'Open';
                                        } elseif ($status == 'in progress') {
                                            $badgeClass = 'label-info';
                                            $statusText = 'In Progress';
                                        } else {
                                            $statusText = 'Open';
                                            $badgeClass = 'label-info';
                                        }

                                        echo "<li><strong>Status:</strong> <span class='label $badgeClass'>$statusText</span></li>";
                                        echo "</ul>";
                                    } else {
                                        echo "Ticket not found.";
                                    }
                                    ?>
                                    <div class="message-container">
                                    <?php
                                    try {
                                        echo '<div class="message user clearfix">
                                                            <img src="img/user-avatar.png" alt="User Avatar" class="avatar">
                                                            <div class="content">
                                                                <h3><small>(User) '.(date('d M, Y', strtotime($ticket['created_at']))) .'</small></h3>
                                                                <p>'.$ticket['message'].'</p>
                                                            </div>
                                                        </div>';
                                                        
                                        $sql = "SELECT * FROM reply WHERE ticket_id = :ticket_id";

                                        // Prepare the SQL statement
                                        $stmt = $dbh->prepare($sql);

                                        // Bind the ticket ID parameter
                                        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);

                                        // Execute the query
                                        $stmt->execute();

                                        // Check if there are any rows returned
                                        if($stmt->rowCount() > 0) {
                                            // Fetch all the rows as objects
                                            $replies = $stmt->fetchAll(PDO::FETCH_OBJ);
                                            
                                            // Loop through the replies
                                            foreach($replies as $row) {
                                                if ($row->created_by == 'user') {
                                                    echo '<div class="message user clearfix">
                                                            <img src="img/user-avatar.png" alt="User Avatar" class="avatar">
                                                            <div class="content">
                                                                <h3><small>(User) '.(date('d M, Y', strtotime($row->created_at))) .'</small></h3>
                                                                <p>'.$row->message.'</p>
                                                            </div>
                                                        </div>';
                                                }else{
                                                    echo '<div class="message admin clearfix">
                                                            <img src="img/admin-avatar.png" alt="Admin Avatar" class="avatar">
                                                            <div class="content">
                                                                <h3><small>(Admin) '.(date('d M, Y', strtotime($row->created_at))) .'</small></h3>
                                                                <p>'.$row->message.'</p>
                                                            </div>
                                                        </div>';
                                                }
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        // Handle any errors
                                        echo "Error: " . $e->getMessage();
                                    }

                                    ?>
                                    </div>
                                    <h3 class="aside-title uppercase">Create Reply</h3>
                                    
                                    <div class="row">

                                        <form name="ticket" method="post">
                                            <div class="col-md-12 col-sm-6 col-xs-12 lyt-left">
                                                <input type="hidden" value=" " placeholder="Subject" class="info" name="subject">
                                                <div class="input-box leave-ib">
                                                    <input type="hidden" name="ticket_id" value="<?php echo $_GET['id']?>">
                                                    <textarea type="text" placeholder="Message" name="message" id="message" cols="50" class="info" rows="5" style="margin-bottom: 20px;"></textarea>
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
