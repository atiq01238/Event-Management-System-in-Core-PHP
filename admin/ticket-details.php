<?php
session_start();
error_reporting(0);

include('includes/config.php');
if(strlen($_SESSION['adminsession'])==0)
{   
    header('location:logout.php');
}
else
{ 
  // Code for reply creation
    if(isset($_POST['submit']))
    {
        $ticket_id = $_POST['ticket_id'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $created_by = 'admin'; // Assuming admin is creating the reply
        
        try {
            $sql = "INSERT INTO reply (ticket_id, subject, message, created_by) VALUES (:ticket_id, :subject, :message, :created_by)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR);
            $stmt->execute();
            echo "<script>alert('Reply created successfully.');</script>";
            echo "<script>window.location.href='/admin/ticket-details.php?ticket_id=".$ticket_id."'</script>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Code for updating ticket status
    if(isset($_POST['update_status']))
    {
        $ticket_id = $_POST['ticket_id'];
        $status = $_POST['status'];
        
        try {
            $sql = "UPDATE tickets SET status = :status WHERE id = :ticket_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            echo "<script>alert('Ticket status updated successfully.');</script>";
            echo "<script>window.location.href='/admin/ticket-details.php?ticket_id=".$ticket_id."'</script>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>EMS | Ticket Details</title>
    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
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
</head>

<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <!-- / Header -->
            <?php include_once('includes/header.php');?>
            <!-- / Leftbar -->
            <?php include_once('includes/leftbar.php');?>
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"> Ticket Details</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <?php
                            $bid=intval($_GET['ticket_id']);     
                            $sql = "SELECT tickets.*, tblusers.FullName AS user_name FROM tickets LEFT JOIN tblusers ON tickets.user_id = tblusers.Userid WHERE tickets.id=:bid";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':bid',$bid,PDO::PARAM_STR);
                            $query->execute();
                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                            $cnt=1;
                            if($query->rowCount() > 0)
                            {
                                foreach($results as $row)
                                { 
                        ?>              
                        <div class="panel-heading">
                          #<?php echo htmlentities($row->id);?> Details
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table width="100%" class="table table-striped table-bordered table-hover" >
                                        <tr>
                                            <th>Subject</th>    
                                            <td><?php echo htmlentities($row->subject);?></td>
                                        </tr>
                                        <tr>
                                            <th>Message</th>    
                                            <td><?php echo htmlentities($row->message);?></td>
                                        </tr>
                                        <tr>
                                            <th>Priority</th>    
                                            <td><?php echo htmlentities($row->priority);?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>    
                                            <td>
                                                <form method="post">
                                                    <input type="hidden" name="ticket_id" value="<?php echo $row->id; ?>">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <select name="status" onchange="this.form.submit()">
                                                        <option value="open" <?php if($row->status == 'open') echo 'selected'; ?>>Open</option>
                                                        <option value="in progress" <?php if($row->status == 'in progress') echo 'selected'; ?>>In Progress</option>
                                                        <option value="closed" <?php if($row->status == 'closed') echo 'selected'; ?>>Closed</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>    
                                            <td><?php echo htmlentities($row->user_name);?></td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>    
                                            <td><?php echo htmlentities(date('Y-m-d H:i:s', strtotime($row->created_at)));?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php }} ?>
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

            <!-- Reply Section -->
            <h3 class="aside-title uppercase">Replies</h3>
            <div class="message-container">
                <?php
                try {
                    $sql = "SELECT * FROM reply WHERE ticket_id = :ticket_id";

                    // Prepare the SQL statement
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(':ticket_id', $bid, PDO::PARAM_INT);

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
                                        <img src="/img/user-avatar.png" alt="User Avatar" class="avatar">
                                        <div class="content">
                                            <h3><small>(User)</small></h3>
                                            <p>'.$row->message.'</p>
                                        </div>
                                    </div>';
                            } else {
                                echo '<div class="message admin clearfix">
                                        <img src="/img/admin-avatar.png" alt="Admin Avatar" class="avatar">
                                        <div class="content">
                                            <h3><small>(Admin)</small></h3>
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
            <!-- /.message-container -->

            <div class="reply-form">
                 <h3 class="aside-title uppercase">Create Reply</h3>
                 <form name="ticket" method="post">
                     <div class="form-group">
                         <input type="hidden" name="ticket_id" value="<?php echo $_GET['ticket_id']?>">
                         <input type="hidden" class="form-control" placeholder="Subject" name="subject" value=" ">
                     </div>
                     <div class="form-group">
                         <textarea class="form-control" rows="5" placeholder="Message" name="message" required="true"></textarea>
                     </div>
                     <div class="form-group">
                         <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                     </div>
                 </form>
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
