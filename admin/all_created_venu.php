<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../includes/config.php');

if(strlen($_SESSION['adminsession'])==0) {   
    header('location:logout.php');
} else {
    if (isset($_POST['venue_id']) && isset($_POST['action'])) {
        $venue_id = $_POST['venue_id'];
        $action = $_POST['action'];

        if ($action == 'approve') {
            $sql = "UPDATE tblcreate_venu SET approved = 1 WHERE id = :venue_id";
        } elseif ($action == 'cancel') {
            $sql = "UPDATE tblcreate_venu SET approved = 0 WHERE id = :venue_id";
        } elseif ($action == 'delete') {
            $sql = "DELETE FROM tblcreate_venu WHERE id = :venue_id";
        }

        $query = $dbh->prepare($sql);
        $query->bindParam(':venue_id', $venue_id, PDO::PARAM_INT);

        if ($query->execute()) {
            $_SESSION['delmsg'] = "Venue action completed successfully.";
        } else {
            $_SESSION['delmsg'] = "Error performing venue action.";
        }
    }

    // Fetch all venues
    $sql = "SELECT * FROM tblcreate_venu";
    $query = $dbh->prepare($sql);
    $query->execute();
    $venues = $query->fetchAll(PDO::FETCH_OBJ);
}
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>EMS | Manage Venue</title>
    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- DataTables CSS -->
    <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
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
        <?php include_once ('includes/header.php'); ?>
        <!-- / Leftbar -->
        <?php include_once ('includes/leftbar.php'); ?>
    </nav>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">All Created Venue</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        All Created Venue
                    </div>

                    <?php if (isset($_SESSION['delmsg']) && $_SESSION['delmsg'] != "") { ?>
                        <div class="succWrap">
                            <strong>Success :</strong>
                            <?php echo htmlentities($_SESSION['delmsg']); ?>
                            <?php echo htmlentities($_SESSION['delmsg'] = ""); ?>
                        </div>
                    <?php } ?>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Venue Name</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Total Price</th>
                                            <!-- <th>Location</th> -->
                                            <th>Images</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        if ($venues) {
                                            foreach ($venues as $venue) { ?>
                                                <tr>
                                                    <td><?php echo htmlentities($cnt); ?></td>
                                                    <td><?php echo htmlentities($venue->venuname); ?></td>
                                                    <td><?php echo htmlentities($venue->category); ?></td>
                                                    <td><?php echo htmlentities($venue->description); ?></td>
                                                    <td><?php echo htmlentities($venue->venu_price); ?></td>
                                                    <td><?php echo htmlentities($venue->start_date); ?></td>
                                                    <td><?php echo htmlentities($venue->end_date); ?></td>
                                                    <td><?php echo htmlentities($venue->total_price); ?></td>
                                                    
                                                    <td>
                                                        <?php if ($venue->image1) { ?>
                                                            <img src="upload/<?php echo htmlentities($venue->image1); ?>" width="50">
                                                        <?php } ?>
                                                        <?php if ($venue->image2) { ?>
                                                            <img src="upload/<?php echo htmlentities($venue->image2); ?>" width="50">
                                                        <?php } ?>
                                                        <?php if ($venue->image3) { ?>
                                                            <img src="upload/<?php echo htmlentities($venue->image3); ?>" width="50">
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlentities($venue->approved ? 'Approved' : 'Not Approved'); ?>
                                                    </td>
                                                    <td>
                                                        <form action="all_created_venu.php" method="post" style="display:inline-block;">
                                                            <input type="hidden" name="venue_id" value="<?php echo $venue->id; ?>">
                                                            <input type="hidden" name="action" value="<?php echo $venue->approved ? 'cancel' : 'approve'; ?>">
                                                            <button type="submit" class="btn btn-<?php echo $venue->approved ? 'warning' : 'success'; ?> btn-sm" onclick="return confirm('Are you sure you want to <?php echo $venue->approved ? 'cancel' : 'approve'; ?> this venue?');">
                                                                <i class="fa fa-<?php echo $venue->approved ? 'times' : 'check'; ?>"></i> <?php echo $venue->approved ? 'Cancel' : 'Approve'; ?>
                                                            </button>
                                                        </form>
                                                        <form action="all_created_venu.php" method="post" style="display:inline-block;">
                                                            <input type="hidden" name="venue_id" value="<?php echo $venue->id; ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this venue?');">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php $cnt++;
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.row (nested) -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="../vendor/jquery/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<!-- Metis Menu Plugin JavaScript -->
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<!-- DataTables JavaScript -->
<script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
<!-- Custom Theme JavaScript -->
<script src="../dist/js/sb-admin-2.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
</script>
</body>
</html>
