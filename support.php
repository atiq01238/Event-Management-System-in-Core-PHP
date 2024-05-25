<?php
session_start();
//datbase connection file
include('includes/config.php');
error_reporting(0);
if(strlen($_SESSION['usrid'])==0)
    {   
header('location:logout.php');
}
else{


?>

<!doctype html>
<html class="no-js" lang="en">
    <head>

        <title>Event Management System | My Tickets </title>
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
        <link rel="stylesheet" href="css/faicons.css">
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
                   <?php include_once('includes/header.php');?>
                <!-- header End-->
            </div>
           <!--slider header area are end-->
            
            <!--  breadcumb-area start-->
            <div class="breadcumb-area bg-overlay">
                <div class="container">
                    <ol class="breadcrumb">
                        <li><a href="index.html">Home</a></li>
                        <li class="active">My Support Tickets</li>
                    </ol>
                </div>
            </div> 
            <!--  breadcumb-area end-->    

            <!-- main blog area start-->
            <div class="single-blog-area ptb100 fix">
               <div class="container">
                   <div class="row">
                    <?php //include_once('includes/myaccountbar.php');?>
                       <div class="col-md-12 col-sm-11">
                           <div class="single-blog-body">
                                <div class="row" style="float: inline-end;">
                                    <a href="create_support.php" class="btn btn-primary">Add Ticket</a>
                                </div>
                        
                                <div class="Leave-your-thought mt50">
                                    <h3 class="aside-title uppercase">Support Tickets</h3>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-6 col-xs-12 lyt-left">
                                                <div class="input-box leave-ib" style="width: 100%;">
                                                    <div class="table-responsive">
                                                        <table border="2" class="table" style=" ">
                                                            <tr>
                                                              <th>#</th>  
                                                              <th>Id</th> 
                                                              <th>Subject</th> 
                                                              <th>Message</th> 
                                                              <th>Priority</th> 
                                                              <th>Status</th> 
                                                              <th>Action</th> 
                                                            </tr>
                                                        <?php
                                                        // Fetching Booking Details
                                                        $uid=$_SESSION['usrid'];
                                                        $sql = "SELECT ticket.id as tid,ticket.priority,ticket.subject,ticket.message,ticket.status from tickets as ticket where ticket.user_id=:uid";
                                                        $query = $dbh->prepare($sql);
                                                        $query->bindParam(':uid',$uid,PDO::PARAM_STR);
                                                        $query->execute();
                                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                                        $cnt=1;
                                                        if($query->rowCount() > 0)
                                                        {
                                                            foreach($results as $row)
                                                            {
                                                                ?>
                                                            <tr>
                                                                <td><?php echo htmlentities($cnt);?></td>
                                                                <td><?php echo htmlentities($row->tid);?></td>
                                                                <td><?php echo htmlentities($row->subject);?></a></td>
                                                                <td><?php echo htmlentities($row->message);?></td>
                                                                <td><?php echo htmlentities($row->priority);?></td>
                                                                <?php 
                                                                $status = $row->status;
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
                                                                ?>
                                                                <td><span class='label <?php echo $badgeClass ?>'><?php echo $statusText ?></span></td>
                                                                <td><a href="view_support.php?id=<?php echo $row->tid ?>">View</a></td>
                                                            </tr>
                                                        <?php $cnt++;}} ?>
                                                        </table>
                                                    </div>
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
         <?php include_once('includes/footer.php');?>
        <!--footer area are start-->
        <a href=""></a>
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
