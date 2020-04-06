<?php
    require_once('connection.php');
    if(!isset($_SESSION["admin_data"])){
        header("location:index.php");
        die;
    }

    $_SESSION['pages'] = 'Content_Type';

    $sql = "SELECT * FROM tbl_content_type  ORDER BY update_at DESC";
    $result = mysqli_query($conn, $sql); 

    if (isset($_POST['type_submit']) && $_POST['type_submit'] == 'type_add') { 
        $type_name=mysqli_real_escape_string($conn,$_POST['type_name']);

        $sql = "INSERT INTO tbl_content_type (type_name) VALUES ('$type_name')";
        if (mysqli_query($conn, $sql)) {
        $_SESSION['insert_success'] = true;
        header("location: content_type.php");
        die;
        }
    }

    if (isset($_POST['type_submit']) && $_POST['type_submit'] == 'edit_type') {
        $type_name=mysqli_real_escape_string($conn,$_POST['type_name']);
        $sql = "UPDATE tbl_content_type 
                SET type_name='".$_POST['type_name']."'
                WHERE id='".$_POST['type_id']."'";
      
        if (mysqli_query($conn, $sql)) {
           $_SESSION['update_success'] = true;
           header("location: content_type.php");
           die;
        }

        $sql = "SELECT * FROM tbl_content_type  ORDER BY update_at DESC";
        $result = mysqli_query($conn, $sql);
    }

    if(isset($_POST['btn_delete'])){
        foreach ($_POST['checked_id'] as $key => $value) {
            $sql = "DELETE FROM tbl_content_type WHERE id='".$value."'"; 
            mysqli_query($conn, $sql); 
        }

        $sql = "SELECT * FROM tbl_content_type  ORDER BY update_at DESC";
        $result = mysqli_query($conn, $sql);

        $_SESSION['delete_success'] = true;
        header("location: content_type.php");
        die;
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Type List</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once('common/loadStyle.php');?>

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php require_once('common/header.php');?>
        <?php require_once('common/leftmenu.php');?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper back-white">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Content Type List</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#" class="color-black"><i class="fa fa-home">Home</i></a></li>
                                <li class="breadcrumb-item active">Type List</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card   card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title pull-left float-l">Type List</h3>

                                <div class="pull-right float-r">
                                    <?php 
                    add_button("Add Type",$user_privileges,"type");
                   ?>
                                    <?php if(in_array(3, $user_privileges) || $_SESSION['admin_data']['role'] == 1){ ?>
                                    <a class="btn btn-danger color-white" data-toggle="modal"
                                        data-target="#modal-delete" form="user_form">Delete</a>
                                    <?php } ?>
                                    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header text-center">
                                                    <h4 class="modal-title w-100 font-weight-bold">Delete confirm</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body mx-3">

                                                    <form method="post" id="form-user-edit" name="form-user-edit">
                                                        <div class="form-group">
                                                            <label> Are you sure you want to delete?</label>
                                                            <!-- <a class="showhide">Show</a> -->
                                                        </div>
                                                        <div class="f-right">
                                                            <?php 
                              delete_button("Yes, Delete",$user_privileges,"type_form");
                            ?>

                                                            <button type="button" class="btn btn-default"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">Cancel</span>
                                                            </button>
                                                        </div>

                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="clearfix"></div>
                            </div>

                            <div class="modal fade" id="modal-type" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Add Type</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-type" name="form-type">
                                                <input type="hidden" name="type_id_add" id="type_id_add" value="">
                                                <div class="form-group">
                                                    <label for="name">Content Type Name:</label>
                                                    <input type="text" class="form-control" name="type_name"
                                                        id="tname_add">
                                                </div>
                                                <button type="submit" name="type_submit" id="type_submit"
                                                    value="type_add" class="btn btn-default">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- edit modal -->
                            <div class="modal fade" id="modal-type-edit" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Edit Type</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-type-edit" name="form-type">
                                                <input type="hidden" name="type_id" id="type_id" value="">
                                                <div class="form-group">
                                                    <label for="name">Content Type Name:</label>
                                                    <input type="text" class="form-control" name="type_name" id="tname">
                                                </div>
                                                <a type="submit" id="edit-confirm"
                                                    class="btn btn-info color-white">Update</a>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-edit-confirm" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">

                                <!-- edit confirm modal -->
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold">Update confirm</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-user-confirm" name="form-user-edit">
                                                <input type="hidden" name="type_id" id="confirm_type_id">
                                                <input type="hidden" class="form-control" name="type_name"
                                                    id="confirm_type_name">
                                                <div class="form-group">
                                                    <label> Are you sure you want to update?</label>
                                                </div>
                                                <div class="f-right">
                                                    <button type="submit" name="type_submit" id="type_submit_edit"
                                                        value="edit_type" class="btn btn-danger">Yes, Update</button>

                                                    <button type="button" class="btn btn-default" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">Cancel</span>
                                                    </button>
                                                </div>

                                            </form>
                                        </div>

                                    </div>
                                </div>
                                <!-- edit confirm modal end -->


                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <form method="post" name="type_form" id="type_form">
                                    <div class="table-responsive">
                                        <table id="userData" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <center>
                                                            <div class="icheck-primary">
                                                                <input type="checkbox" id="select_all"  />
                                                                <label for="select_all"></label>
                                                            </div>
                                                        </center>
                                                    </th>
                                                    <th>Type Name</th>
                                                    <?php 
                                    if($_SESSION['admin_data']['role'] == 1 || 
                                          (!empty($user_privileges) && in_array(2, $user_privileges))){?><th>Action
                                                    </th><?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                            if (mysqli_num_rows($result) > 0) 
                            {
                                while($row = mysqli_fetch_assoc($result)) {
                                    if($_SESSION['admin_data']['role'] == 1 || 
                                        (!empty($user_privileges) && in_array(4, $user_privileges))){
                            ?>
                                                <tr>
                                                    <td align="center">
                                                        <div class="icheck-primary">
                                                            <input class="checkbox"  type="checkbox" name="checked_id[]" id="check<?php echo $row['id']; ?>"  value="<?php echo $row['id']; ?>" >
                                                            <label for="check<?php echo $row['id']; ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $row['type_name'];?></td>

                                                    <?php 
                                    if ($_SESSION['admin_data']['role'] == 1) {
                                        echo '<td><button type="button" class="btn btn-primary edit_type" value='.$row['id'].'><i class="fas fa-edit"></i></button></td>';
                                    }else if (isset($user_privileges) && !empty($user_privileges)) {
                                        if (in_array(2, $user_privileges)){
                                        echo '<td><button type="button" class="btn  btn-primary edit_type" value='.$row['id'].'><i class="fas fa-edit"></i></button></td>';
                                        }
                                    }
                                    ?>

                                                </tr>
                                                <?php } } } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php require_once('common/footer.php');?>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php require_once('common/loadScript.php');?>

    <?php
  if(isset($_SESSION['insert_success']) && $_SESSION['insert_success']){?>
    <script type="text/javascript">
    toastr.success('Insert Type SuccessFully');
    </script>
    <?php
    unset($_SESSION['insert_success']);
   } ?>

    <?php
  if(isset($_SESSION['update_success']) && $_SESSION['update_success']){?>
    <script type="text/javascript">
    toastr.success('Type Updated SuccessFully');
    </script>
    <?php
    unset($_SESSION['update_success']);
   } ?>

    <?php
  if(isset($_SESSION['delete_success']) && $_SESSION['delete_success']){?>
    <script type="text/javascript">
    toastr.success('Type Deleted SuccessFully');
    </script>
    <?php
    unset($_SESSION['delete_success']);
   } ?>

    <script>
    $(document.body).on('click', '.edit_type', function() {
        console.log("asdfasdfasdf")
        $('#form-type')[0].reset();
        $this = $(this);
        $.ajax({
            type: "POST",
            url: "call_data.php",
            dataType: 'json',
            data: {
                type_id: $this.val()
            },
            success: function(data) {
                $("#tname").val(data.type_name);

                $('#type_id').val(data.id);
                $('#modal-type-edit').modal('show');
                $("label.error").remove();
            }
        });
    });

    $(document).ready(function() {

        $("#edit-confirm").click(function() {

            $("#modal-type-edit").modal('hide');
            type_id = $("#type_id").val();
            tname = $("#tname").val();
            console.log("typeid", type_id, tname)
            $("#confirm_type_name").val(tname);
            $("#confirm_type_id").val(type_id);
            $("#modal-edit-confirm").modal('show');
        })

        $('#type_submit').on('click', function() {
            $("form[name='form-type']").validate({
                rules: {
                    type_name: "required"
                },
                messages: {
                    type_name: "Please enter category Type",
                }
            });

        });

        $("#add").on('click', function() {
            $("label.error").remove();
        })
        $(".close").on('click', function() {
            $('#form-type')[0].reset();
            $('#sign-up').html("Add Type");
        })

    })

    $(document).ready(function() {
        $('#select_all').on('click', function() {
            if (this.checked) {
                $('.checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function() {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function() {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
    </script>
</body>

</html>