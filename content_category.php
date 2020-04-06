<?php
    require_once('connection.php');
    if(!isset($_SESSION["admin_data"])){
        header("location:index.php");
        die;
    }
    $_SESSION['pages'] = 'Content_Category';

    $sql = "SELECT * FROM tbl_content_category ORDER BY update_at DESC";
    $result = mysqli_query($conn, $sql); 

    if (isset($_POST['category_submit']) && $_POST['category_submit'] == 'category_add') { 
        $category_name=mysqli_real_escape_string($conn,$_POST['category_name']);

        $sql = "INSERT INTO tbl_content_category (category_name) VALUES ('$category_name')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['insert_success'] = true;
           header("location: content_category.php");
           die;
        }
    }

    if (isset($_POST['category_submit']) && $_POST['category_submit'] == 'edit_category')  {
        $category_name=mysqli_real_escape_string($conn,$_POST['category_name']);
        $sql = "UPDATE tbl_content_category 
                SET category_name='".$_POST['category_name']."'
                WHERE id='".$_POST['category_id']."'";
      
        if (mysqli_query($conn, $sql)) {
            $_SESSION['update_success'] = true;
            header("location: content_category.php");
            die;
        }

        $sql = "SELECT * FROM tbl_content_category ORDER BY update_at DESC";
        $result = mysqli_query($conn, $sql);
    }

    if(isset($_POST['btn_delete'])){
        foreach ($_POST['checked_id'] as $key => $value) {
            $sql = "DELETE FROM tbl_content_category WHERE id='".$value."'"; 
            mysqli_query($conn, $sql); 
        }

        $sql = "SELECT * FROM tbl_content_category ORDER BY update_at DESC";
        $result = mysqli_query($conn, $sql);

        $_SESSION['delete_success'] = true;
        header("location: content_category.php");
        die;
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Category List</title>
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
                            <h1>Content Category List</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#" class="color-black"><i class="fa fa-home">Home</i></a></li>
                                <li class="breadcrumb-item active">Category List</li>
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
                                <h3 class="card-title pull-left float-l">Category List</h3>

                                <div class="pull-right float-r">

                                    <?php 
                                      add_button("Add Category",$user_privileges,"category");
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
                                                        </div>
                                                        <div class="f-right">
                                                            <?php 
                                                              delete_button("Yes, Delete",$user_privileges,"category_form");
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

                            <div class="modal fade" id="modal-category" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Add Category
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-category" name="form-category">
                                                <input type="hidden" name="category_id" id="category_id_add" value="">
                                                <div class="form-group">
                                                    <label for="name">Category Name:</label>
                                                    <input type="text" class="form-control" name="category_name"
                                                        id="cname_add">
                                                </div>
                                                <button type="submit" name="category_submit" id="category_submit_add"
                                                    value="category_add" class="btn btn-default">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- edit modal -->
                            <div class="modal fade" id="modal-category-edit" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Edit Category
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-category" name="form-category">
                                                <input type="hidden" name="category_id" id="category_id" value="">
                                                <div class="form-group">
                                                    <label for="name">Category Name:</label>
                                                    <input type="text" class="form-control" name="category_name"
                                                        id="cname">
                                                </div>

                                                <a class="btn btn-info color-white" data-toggle="modal"
                                                    data-target="#modal-edit-confirm" form="user_form">Update</a>

                                                <!-- edit confirm modal -->
                                                <div class="modal fade" id="modal-edit-confirm" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header text-center">
                                                                <h4 class="modal-title w-100 font-weight-bold">Update
                                                                    confirm</h4>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body mx-3">

                                                                <form method="post" id="form-user-edit"
                                                                    name="form-user-edit">
                                                                    <input type="hidden" name="type_id"
                                                                        id="confirm_type_id">
                                                                    <input type="hidden" class="form-control"
                                                                        name="type_name" id="confirm_type_name">
                                                                    <div class="form-group">
                                                                        <label> Are you sure you want to update?</label>

                                                                    </div>
                                                                    <div class="f-right">
                                                                        <button type="submit" name="category_submit"
                                                                            id="category_submit" value="category_add"
                                                                            class="btn btn-danger">Yes, Update</button>

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
                                                <!-- edit confirm modal end -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end edit modal -->

                            <!-- /.card-header -->
                            <div class="card-body">
                                <form method="post" name="category_form" id="category_form">
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
                                                    <th>Category Name</th>
                                                    <?php 
                                      if($_SESSION['admin_data']['role'] == 1 || 
                                            (!empty($user_privileges) && in_array(2, $user_privileges))){?><th>Action
                                                    </th><?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                  if (mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) { 
                                        if($_SESSION['admin_data']['role'] == 1 || 
                                            (!empty($user_privileges) && in_array(4, $user_privileges))){ ?>
                                                <tr>
                                                    <td align="center">
                                                        <div class="icheck-primary">
                                                            <input class="checkbox"  type="checkbox" name="checked_id[]" id="check<?php echo $row['id']; ?>"  value="<?php echo $row['id']; ?>" >
                                                            <label for="check<?php echo $row['id']; ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $row['category_name'];?></td>

                                                    <?php 
                                                      Edit_button($user_privileges,$row['id'],"edit_category");
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

    </div>
    <!-- ./wrapper -->

    <?php require_once('common/loadScript.php');?>


    <?php
  if(isset($_SESSION['insert_success']) && $_SESSION['insert_success']){?>
    <script type="text/javascript">
    toastr.success('Insert Category SuccessFully');
    </script>
    <?php
    unset($_SESSION['insert_success']);
   } ?>

    <?php
  if(isset($_SESSION['update_success']) && $_SESSION['update_success']){?>
    <script type="text/javascript">
    toastr.success('Category Updated SuccessFully');
    </script>
    <?php
    unset($_SESSION['update_success']);
   } ?>

    <?php
  if(isset($_SESSION['delete_success']) && $_SESSION['delete_success']){?>
    <script type="text/javascript">
    toastr.success('Category Deleted SuccessFully');
    </script>
    <?php
    unset($_SESSION['delete_success']);
   } ?>

    <script>
    $(document).on('click', '.edit_category', function() {
        $('#form-category')[0].reset();
        $this = $(this);
        $.ajax({
            type: "POST",
            url: "call_data.php",
            dataType: 'json',
            data: {
                category_id: $this.val()
            },
            success: function(data) {
                $("#cname").val(data.category_name);

                $('#category_id').val(data.id);
                $('#category_submit').val("edit_category");
                $('#sign-up').html("Edit Category");
                $('#modal-category-edit').modal('show');
                $("label.error").remove();
            }
        });
    });
    $(document).ready(function() {


        $('#category_submit').on('click', function() {
            $("form[name='form-category']").validate({
                rules: {
                    category_name: "required"
                },
                messages: {
                    category_name: "Please enter category name",
                }
            });

        });

        $("#add").on('click', function() {
            $("label.error").remove();
        })
        $(".close").on('click', function() {
            $('#form-category')[0].reset();
            $('#sign-up').html("Add Category");
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