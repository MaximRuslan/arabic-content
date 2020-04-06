<?php
    require_once('connection.php');
    if(!isset($_SESSION["admin_data"])){
        header("location:index.php");
        die;
    }

    $_SESSION['pages'] = 'User';
    
    $sql = "SELECT * FROM tbl_user WHERE role != 1  ORDER BY update_at DESC";
    $result = mysqli_query($conn, $sql);
    
    if (isset($_POST['user_submit']) && $_POST['user_submit'] == 'new_user') { 
        $username=mysqli_real_escape_string($conn,$_POST['username']);
        $email=mysqli_real_escape_string($conn,$_POST['email']);
        $password=mysqli_real_escape_string($conn,$_POST['password']);

         $privileges=implode(',',$_POST['privileges']);

        $sql = "INSERT INTO tbl_user (username, email, password,privileges) VALUES ('$username','$email','$password','$privileges')";

        if (mysqli_query($conn, $sql)) {
        $_SESSION['insert_success'] = true;
           header("location: user.php");
           die;
        }
    }


    if (isset($_POST['user_submit']) && $_POST['user_submit'] == 'edit_user') {
        $username=mysqli_real_escape_string($conn,$_POST['username']);
        $email=mysqli_real_escape_string($conn,$_POST['email']);
    
        $privileges=implode(',',$_POST['privileges']);

        if(!empty($_POST['password']))
        {
          $password=mysqli_real_escape_string($conn,$_POST['password']);
          $sql = "UPDATE tbl_user 
                SET username='".$_POST['username']."',email='".$_POST['email']."',
                privileges='".$privileges."',password='".$password."'
                WHERE id='".$_POST['user_id']."'";
        } else {
             $sql = "UPDATE tbl_user 
                    SET username='".$_POST['username']."',email='".$_POST['email']."',privileges='".$privileges."'
                    WHERE id='".$_POST['user_id']."'";
        }

        if (mysqli_query($conn, $sql)) {
            $_SESSION['update_success'] = true;
            header("location: user.php");
            die;
        }

        $sql = "SELECT * FROM tbl_user  ORDER BY update_at DESC";
        $result = mysqli_query($conn, $sql);
    }

    if(isset($_POST['btn_delete'])){  
        foreach ($_POST['checked_id'] as $key => $value) {
            $sql = "DELETE FROM tbl_user WHERE id='".$value."'"; 
            mysqli_query($conn, $sql); 
        }

        $sql = "SELECT * FROM tbl_user  ORDER BY update_at DESC";
        $result = mysqli_query($conn, $sql);

        $_SESSION['delete_success'] = true;
        header("location: user.php");
        die;
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>User List</title>
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
                            <h1>User List</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#" class="color-black"><i class="fa fa-home">Home</i></a></li>
                                <li class="breadcrumb-item active">User List</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card  card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title pull-left float-l">User List</h3>

                                <div class="pull-right float-r">

                                    <?php 
                                      add_button("Add user",$user_privileges,"user");
                                    ?>
                                    <a class="btn btn-danger color-white" data-toggle="modal"
                                        data-target="#modal-delete" form="user_form">Delete</a>
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

                                                    <form method="post" id="form-user-delete" name="form-user-delete">
                                                        <div class="form-group">
                                                            <label> Are you sure you want to delete?</label>
                                                            <!-- <a class="showhide">Show</a> -->
                                                        </div>
                                                        <div class="f-right">
                                                            <?php 
                                                              delete_button("Yes, Delete",$user_privileges,"user_form");
                                                            ?>
                                                            <!-- <button type="submit" name="user_submit" id="user_submit" value="new_user" class="btn btn-danger">Yes, Delete</button> -->
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

                            <div class="modal fade" id="modal-user" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up-add">Add User
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-user-edit" name="form-user-edit">
                                                <input type="hidden" name="user_id" id="user_id-add" value="">
                                                <div class="form-group">
                                                    <label for="name">User Name:</label>
                                                    <input type="text" class="form-control" name="username"
                                                        id="uname-add">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email address:</label>
                                                    <input type="email" class="form-control" name="email"
                                                        id="email-add">
                                                </div>
                                                <div class="form-group">
                                                    <label for="pwd">Password:</label>
                                                    <input type="password" class="form-control pwd" name="password"
                                                        id="pwd-add">
                                                    <!-- <a class="showhide">Show</a> -->
                                                    <a class="showhide"><i class="fas fa-eye"></i></a>
                                                </div>
                                                <div class="form-group">
                                                    <label for="privileges">Privileges:</label>
                                                    <div class="checkbox">
                                                        <label><input type="checkbox" class="chk" id="chk-1-add"
                                                                name="privileges[]" value="1">Add</label>
                                                        <label><input type="checkbox" class="chk" id="chk-2-add"
                                                                name="privileges[]" value="2">Edit</label>
                                                        <label><input type="checkbox" class="chk" id="chk-3-add"
                                                                name="privileges[]" value="3">Delete</label>
                                                        <label><input type="checkbox" class="chk" id="chk-4-add"
                                                                name="privileges[]" value="4">View</label>
                                                    </div>
                                                </div>
                                                <button type="submit" name="user_submit" id="user_submit-add"
                                                    value="new_user" class="btn btn-default">Submit</button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- edit user modal -->
                            <div class="modal fade" id="modal-user-edit" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Edit User</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-user-edit" name="form-user-edit">
                                                <input type="hidden" name="user_id" id="user_id" value="">
                                                <div class="form-group">
                                                    <label for="name">User Name:</label>
                                                    <input type="text" class="form-control" name="username" id="uname">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email address:</label>
                                                    <input type="email" class="form-control" name="email" id="email">
                                                </div>
                                                <div class="form-group">
                                                    <label for="pwd">Password:</label>
                                                    <input type="password" class="form-control pwd" name="password"
                                                        id="pwd">
                                                    <!-- <a class="showhide">Show</a> -->
                                                    <a class="showhide"><i class="fas fa-eye"></i></a>
                                                </div>
                                                <div class="form-group">
                                                    <label for="privileges">Privileges:</label>
                                                    <div class="checkbox">
                                                        <label><input type="checkbox" class="chk" id="chk-1"
                                                                name="privileges[]" value="1">Add</label>
                                                        <label><input type="checkbox" class="chk" id="chk-2"
                                                                name="privileges[]" value="2">Edit</label>
                                                        <label><input type="checkbox" class="chk" id="chk-3"
                                                                name="privileges[]" value="3">Delete</label>
                                                        <label><input type="checkbox" class="chk" id="chk-4"
                                                                name="privileges[]" value="4">View</label>
                                                    </div>
                                                </div>

                                                <a class="btn btn-info color-white" data-toggle="modal"
                                                    data-target="#modal-edit-confirm" form="user_form">Update</a>
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

                                                                <form method="post" id="form-user-delete"
                                                                    name="form-user-delete">
                                                                    <div class="form-group">
                                                                        <label> Are you sure you want to update?</label>

                                                                    </div>
                                                                    <div class="f-right">
                                                                        <button type="submit" name="user_submit"
                                                                            id="user_submit" value="new_user"
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

                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- modal end -->

                            <!-- /.card-header -->
                            <div class="card-body">
                                <form method="post" name="user_form" id="user_form">
                                    <div class="table-responsive">
                                        <table id="userData" class="table table-bordered ">
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
                                                    <th>User Name</th>
                                                    <th>Email</th>
                                                    <th>Privileges</th>
                                                    <?php 
                              if($_SESSION['admin_data']['role'] == 1 || 
                                (!empty($user_privileges) && in_array(2, $user_privileges))){?>
                                                    <th>Action</th>
                                                    <?php } ?>
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
                                                            <input class="checkbox check-table"  type="checkbox" name="checked_id[]" id="check<?php echo $row['id']; ?>"  value="<?php echo $row['id']; ?>" >
                                                            <label for="check<?php echo $row['id']; ?>"></label>
                                                        </div>
                                                 <!--    <input type="checkbox" name="checked_id[]"
                                                            class="checkbox" value="<?php echo $row['id']; ?>" /> -->
                                                        </td>
                                                    <td><?php echo $row['username'];?></td>
                                                    <td><?php echo $row['email'];?></td>
                                                    <td>
                                                        <?php //echo $row['privileges'];?>
                                                        <?php
                                                          if (strpos($row['privileges'], '1') !== false){?>
                                                        <span class="badage add">ADD</span>
                                                        <?php } ?>
                                                        <?php 
                                                          if (strpos($row['privileges'], '2') !== false){ ?>
                                                        <span class="badage update">UPDATE</span>
                                                        <?php } ?>
                                                        <?php
                                                          if (strpos($row['privileges'], '3') !== false){ ?>
                                                        <span class="badage delete">DELETE</span>
                                                        <?php } ?>
                                                        <?php
                                                          if (strpos($row['privileges'], '4') !== false){ ?>
                                                        <span class="badage view">VIEW</span>
                                                        <?php } ?>
                                                    </td>

                                                    <?php 
                                                      view_button($user_privileges,$row['id'],"edit_user");
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
    toastr.success('Insert User SuccessFully');
    </script>
    <?php
    unset($_SESSION['insert_success']);
   } ?>

    <?php
  if(isset($_SESSION['update_success']) && $_SESSION['update_success']){?>
    <script type="text/javascript">
    toastr.success('User Updated SuccessFully');
    </script>
    <?php
    unset($_SESSION['update_success']);
   } ?>

    <?php
  if(isset($_SESSION['delete_success']) && $_SESSION['delete_success']){?>
    <script type="text/javascript">
    toastr.success('User Deleted SuccessFully');
    </script>
    <?php
    unset($_SESSION['delete_success']);
   } ?>

    <script>
    var hv = "";
    $(document).on('click', '.edit_user', function() {
        $('#form-user-edit')[0].reset();
        $this = $(this);
        $.ajax({
            type: "POST",
            url: "call_data.php",
            dataType: 'json',
            data: {
                id: $this.val()
            },
            success: function(data) {
                var str = data.privileges;
                var priv = str.split(",");
                $("#uname").val(data.username);
                $('#uname').attr('disabled', false);
                $("#email").val(data.email);
                $("#email").attr('disabled', false);
                $("#pwd").val(data.password);
                $("#pwd").attr('disabled', false);
                $('input[name="privileges[]"]').attr('disabled', false);
                for (var i = 1; i < 5; i++) {
                    $('#chk-' + i).prop('checked', false);
                }
                $.each(priv, function(key, value) {
                    $('#chk-' + value).prop('checked', true);
                })
                $('#user_id').val(data.id);
                $('#user_submit').val("edit_user");
                $('#user_submit').attr('disabled', false);
                $('#user_submit').show();
                $('#sign-up').html("Edit User");
                $('#modal-user-edit').modal('show');
                $("label.error").remove();
                hv = $('#user_id').val();
            }
        });
    })
    $(document).on('click', '.view_content', function() {
        $.ajax({
            type: "POST",
            url: "call_data.php",
            dataType: 'json',
            data: {
                id: $(this).val()
            },
            success: function(data) {
                var str = data.privileges;
                var priv = str.split(",");
                $("#uname").val(data.username);
                $('#uname').attr('disabled', true);
                $("#email").val(data.email);
                $("#email").attr('disabled', true);
                $("#pwd").val(data.password);
                $("#pwd").attr('disabled', true);

                $('input[name="privileges[]"]').attr('disabled', true);
                for (var i = 1; i < 5; i++) {
                    $('#chk-' + i).prop('checked', false);
                }
                $.each(priv, function(key, value) {
                    $('#chk-' + value).prop('checked', true);
                })
                $('#user_id').val(data.id);
                $('#user_submit').val("edit_user");
                $('#user_submit').attr('disabled', true);
                $('#user_submit').hide();
                $('#sign-up').html("View Content");
                $('#modal-user-edit').modal('show');
                $("label.error").remove();
                hv = $('#user_id').val();
            }
        });
    })

    $(document).ready(function() {


        $(".edit_user_modal").click(function() {
            $("#modal-delete").modal();
        })

        $('#user_submit').on('click', function() {
            if (hv != '') {
                $("form[name='form-user-edit']").validate({
                    rules: {
                        username: "required",
                        email: {
                            required: true,
                            email: true
                        }
                    },
                    messages: {
                        firstname: "Please enter your name",

                        email: "Please enter a valid email address"
                    }
                });
            } else {
                $("form[name='form-user-edit']").validate({
                    rules: {
                        username: "required",

                        password: {
                            required: true,
                            minlength: 5
                        },
                        email: {
                            required: true,
                            password: {
                                required: "Please provide a password",
                                minlength: "Your password must be at least 5 characters long"
                            },
                            email: true
                        }
                    },
                    messages: {
                        firstname: "Please enter your name",
                        email: "Please enter a valid email address"
                    }
                });
            }
        });

        $("#add").on('click', function() {
            $('#uname').attr('disabled', false);
            $('#email').attr("disabled", false);
            $('#pwd').attr("disabled", false);
            $('input[name="privileges[]"]').attr('disabled', false);
            $('#user_submit').show();
            $('#user_submit').attr('disabled', false);
            $("label.error").remove();
        })
        $(".close").on('click', function() {

            $('#form-user-edit')[0].reset();
            $('#sign-up').html("Add User");
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

        $('.check-table').on('click', function() {
            console.log($('.check-table:checked').length, ":::", $('.check-table').length )
            if ($('.check-table:checked').length == $('.check-table').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });

    $(".showhide").click(function(e) {
        console.log("clicked");
        e.preventDefault();
        var curr_val = $(this).hasClass("show");
        if (!curr_val) {
            $(this).addClass("show")
            $(".pwd").attr("type", "text");
            $(".showhide").find("i").addClass('fa-eye-slash');
            $(".showhide").find("i").removeClass('fa-eye');
        } else {
            $(this).removeClass("show")
            $(".pwd").attr("type", "password");
            $(".showhide").find("i").removeClass('fa-eye-slash');
            $(".showhide").find("i").addClass('fa-eye');
        }

    });
    </script>
</body>

</html>