 <!-- Main Sidebar Container -->
 <?php
    $sql_logo = "SELECT * FROM tbl_admin WHERE id= 1";
    $result_logo = mysqli_query($conn, $sql_logo);
    $row_logo = mysqli_fetch_array($result_logo,MYSQLI_ASSOC);
    $logo_img=$row_logo['logo_img'];
 ?>
  <aside class="main-sidebar sidebar-dark-primary elevation-4 favo-back">
    <!-- Brand Logo -->
    <a href="#" class="brand-link text-center back-white height-103">
      <img src="<?php echo $logo_img ?>" alt="AdminLTE Logo" class="brand-image logo-img">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image"><?php //echo $_SESSION['admin_data']['img']; ?>
        <?php 
        if(isset( $_SESSION['admin_data']['img'] )){ 
          ?>
          <img src="<?php echo $_SESSION['admin_data']['img']; ?>" class="logo-circle-small elevation-2 width-28r">
        <?php
         } 
         ?>
        <?php
         if(empty($_SESSION['admin_data']['img'])){ 
          ?>
          <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png"  class="logo-circle-small elevation-2 width-28r" >
        <?php
         } 
         ?>
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo " Welcome " . $_SESSION['admin_data']['username']; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo $_SESSION['pages'] == 'Dashboard'  ? 'active' : ''?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
        <?php if($_SESSION['admin_data']['role'] == 1) { ?>
          <li class="nav-item">
            <a href="user.php" class="nav-link <?php echo $_SESSION['pages'] == 'User' ? 'active' : ''?>">
              <i class="nav-icon fa fa-users"></i>
              <p>
                User
              </p>
            </a>
          </li>
        <?php } ?>
          <li class="nav-item">
            <a href="content_category.php" class="nav-link <?php echo $_SESSION['pages'] == 'Content_Category' ? 'active' : ''?>">
              <i class="nav-icon fa fa-list-alt"></i>
              <p>
                Content Category
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="content_type.php" class="nav-link <?php echo $_SESSION['pages'] == 'Content_Type' ? 'active' : ''?>">
              <i class="nav-icon fa fa-list-alt"></i>
              <p>
                Content Type
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="content.php" class="nav-link <?php echo $_SESSION['pages'] == 'Content' ? 'active' : ''?>">
              <i class="nav-icon fa fa-list-alt"></i>
              <p>
                Content
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profile.php" class="nav-link <?php echo $_SESSION['pages'] == 'Profile' ? 'active' : ''?>">
              <i class="nav-icon fa fa-user"></i>
              <p>
                Profile
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
