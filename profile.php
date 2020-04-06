<?php
    require_once('connection.php');
    if(!isset($_SESSION["admin_data"])){
        header("location:index.php");
        die;
    }

    $_SESSION['pages'] = 'Profile';
    
    $sql = "SELECT * FROM tbl_user WHERE role != 1  ORDER BY update_at DESC";
    $result = mysqli_query($conn, $sql);
    $current_time=date('Ymdhis');

    


    if (isset($_POST['user_submit']) && $_POST['user_submit'] == 'edit_user') {

        $username=mysqli_real_escape_string($conn,$_POST['username']);
        $email=mysqli_real_escape_string($conn,$_POST['email']);

        // echo $_FILES["user_img"]["name"]; exit;
        if($_FILES["user_img"]["name"] != ""){
            $target_dir = "upload/";
            $target_file = $target_dir.$current_time.basename($_FILES["user_img"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            // Check if image file is a actual image or fake image

            $check = getimagesize($_FILES["user_img"]["tmp_name"]);
            $error_msg="";
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                $error_msg="File is not an image.";
                $uploadOk = 0;
            }
           
            if (file_exists($target_file)) {
                $error_msg="Sorry, image already exists.";
                $uploadOk = 0;
            }
           

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                $error_msg="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }



            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $error_msg="Sorry, your image was not uploaded.";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["user_img"]["tmp_name"], $target_file)) {
                    echo "The file ". basename( $_FILES["user_img"]["name"]). " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your image.";
                }
            }

            if(!empty($_POST['password']))
            {
              $password=mysqli_real_escape_string($conn,$_POST['password']);
              $sql = "UPDATE tbl_user 
                    SET username='".$_POST['username']."',email='".$_POST['email']."',img='".$target_file."',password='".$password."'
                    WHERE id='".$_POST['user_id']."'";
            } else {
                 $sql = "UPDATE tbl_user 
                        SET username='".$_POST['username']."',email='".$_POST['email']."',img='".$target_file."'
                        WHERE id='".$_POST['user_id']."'";
            }
        }else{
            if(!empty($_POST['password']))
            {
              $password=mysqli_real_escape_string($conn,$_POST['password']);
              $sql = "UPDATE tbl_user 
                    SET username='".$_POST['username']."',email='".$_POST['email']."',password='".$password."'
                    WHERE id='".$_POST['user_id']."'";
            } else {
                 $sql = "UPDATE tbl_user 
                        SET username='".$_POST['username']."',email='".$_POST['email']."'
                        WHERE id='".$_POST['user_id']."'";
            }
        }

        $user_id=$_POST['user_id'];
        if (mysqli_query($conn, $sql)) {
            $sql = "SELECT *  FROM tbl_user WHERE id = '$user_id'";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
            $_SESSION["admin_data"] = $row;
            $_SESSION['update_success'] = true;
            header("location: profile.php");
            die;
        }


        
    }

    if (isset($_POST['logo_submit'])) {
        //system logo image upload
        $target_dir = "upload/";
        $logo_file = $target_dir.$current_time.basename($_FILES["logo_img"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($logo_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["logo_img"]["tmp_name"]);
        $error_msg="";
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $error_msg="File is not an image.";
            $uploadOk = 0;
        }
        // Check if file already exists
        if (file_exists($logo_file)) {
            $error_msg="Sorry, file already exists.";
            $uploadOk = 0;
        }
      
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $error_msg="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }


        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $error_msg="Sorry, your image was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["logo_img"]["tmp_name"], $logo_file)) {
                echo "The file ". basename( $_FILES["logo_img"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your image.";
            }
        }
        $sql="UPDATE tbl_admin SET logo_img='$logo_file' WHERE id=1";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['upload_success'] = true;
           header("location: profile.php");
           die;
        }
        //system logo image upload end
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
                            <h1>User Profile</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#" class="color-black"><i class="fa fa-home">Home</i></a></li>
                                <li class="breadcrumb-item active">User Profile</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content back-white">


                <hr>
                <div class="container bootstrap snippet">

                    <form class="form" method="post" id="registrationForm" enctype="multipart/form-data">
                        <div class="row">

                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['admin_data']['id']; ?>">
                            <div class="col-sm-4 d-center">
                                <div class="text-center">
                                    <?php 
                                    if(isset( $_SESSION['admin_data']['img'] )){ 
                                      ?>
                                    <img src="<?php echo $_SESSION['admin_data']['img']; ?>"
                                        class="width-70 avatar logo-circle img-thumbnail" alt="avatar">
                                    <?php
                                     } 
                                     ?>
                                    <?php
                                     if(empty($_SESSION['admin_data']['img'])){ 
                                      ?>
                                    <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png"
                                        class="width-70 avatar logo-circle img-thumbnail" alt="avatar">
                                    <?php
                                     } 
                                     ?>
                                    <h6>Choose Your photo...</h6>
                                    <input type="file" name="user_img" class="text-center center-block file-upload">
                                </div>
                                </hr><br>
                                <hr>
                            </div>
                            <!--/col-3-->

                            <div class="col-sm-8">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="home">
                                        <!-- <hr> -->
                                        <div class="form-group">

                                            <div class="col-xs-6">
                                                <label for="first_name">
                                                    <h4>User name</h4>
                                                </label>
                                                <input type="text" class="form-control" name="username" id="uname"
                                                    placeholder="user name" title="enter your first name if any."
                                                    value="<?php echo $_SESSION['admin_data']['username']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <div class="col-xs-6">
                                                <label for="email">
                                                    <h4>Email</h4>
                                                </label>
                                                <input type="email" class="form-control" name="email" id="email"
                                                    placeholder="you@email.com" title="enter your email."
                                                    value="<?php echo $_SESSION['admin_data']['email']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-6">
                                                <label for="password">
                                                    <h4>Password</h4>
                                                </label>
                                                <input type="password" class="form-control" name="password" id="pwd"
                                                    placeholder="password" title="enter your password."
                                                    value="<?php echo $_SESSION['admin_data']['password']; ?>">
                                                <a class="showhide"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <br>

                                                <a class="btn btn-lg btn-success color-white" data-toggle="modal"
                                                    data-target="#modal-confirm" form="user_form">Save</a>
                                                <div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog"
                                                    aria-labelledby="myModalLabel" aria-hidden="true">
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
                                                                            id="user_submit" value="edit_user"
                                                                            class="btn btn-lg btn-success"> Save
                                                                        </button>

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
                                                <button class="btn btn-lg" type="reset">Reset</button>
                                            </div>
                                        </div>

                                        <hr>

                                    </div>


                                </div>
                                <!--/tab-pane-->
                            </div>
                            <!--/tab-content-->

                        </div>
                        <!--/col-9-->
                    </form>
                    <?php 
                    if($_SESSION['admin_data']['role'] ==1 ){  ?>
                    <div class="row" class="title"><label class="logo-set">Logo Set</label></div>
                    <div class="row">

                        <div class="col-sm-3">
                            <h6>Choose System logo...</h6>
                            <img src="<?php echo $logo_img ?>" class="width-70 avatar  img-thumbnail" alt="avatar">

                        </div>
                        <div class="col-sm-9 d-center">
                            <form method="post" enctype="multipart/form-data" class="d-center">
                                <input type="file" name="logo_img" class="text-center center-block file-upload">
                                <button type="submit" name="logo_submit" id="logo_submit" value="edit_user"
                                    class="btn btn-info"> Logo Upload </button>
                            </form>

                        </div>
                    </div>
                    <?php } ?>

                </div>
                <!--/row-->
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
  if(isset($_SESSION['update_success']) && $_SESSION['update_success']){?>
    <script type="text/javascript">
    toastr.success('User Updated SuccessFully');
    </script>
    <?php
    unset($_SESSION['update_success']);
   } ?>

    <?php
  if(isset($_SESSION['update_faild']) && $_SESSION['update_faild']){?>
    <script type="text/javascript">
    toastr.error("<?php echo $_SESSION['error_msg']; ?>", 'User Updated Faild!');
    </script>
    <?php
    unset($_SESSION['update_faild']);
   } ?>

    <?php
  if(isset($_SESSION['upload_success']) && $_SESSION['upload_success']){?>
    <script type="text/javascript">
    toastr.success('Upload Logo Image SuccessFully');
    </script>
    <?php
    unset($_SESSION['upload_success']);
   } ?>

    <script>
    var hv = "";


    $(document).ready(function() {

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
                        username: "Please enter your name",

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
    })

    $(".showhide").click(function(e) {
        console.log("clicked");
        e.preventDefault();
        var curr_val = $(this).hasClass("show");
        if (!curr_val) {
            $(this).addClass("show")
            $("#pwd").attr("type", "text");
            $(".showhide").find("i").addClass('fa-eye-slash');
            $(".showhide").find("i").removeClass('fa-eye');
        } else {
            $(this).removeClass("show")
            $("#pwd").attr("type", "password");
            $(".showhide").find("i").removeClass('fa-eye-slash');
            $(".showhide").find("i").addClass('fa-eye');
        }

    });
    </script>
</body>

</html>