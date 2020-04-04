<?php 
    $user_privileges = array();
    if(isset($_SESSION['admin_data']) && $_SESSION['admin_data']){
        if($_SESSION['admin_data']['role'] != 1)
        {
            $privileges_data = $_SESSION['admin_data']['privileges'];
            $user_privileges = explode(",",$privileges_data);
        }   
    }
  
    function add_button($value = "Add",$user_privileges =array(),$target){
        if ($_SESSION['admin_data']['role'] == 1) {
            echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-'.$target.'" data-backdrop="static" data-keyboard="false" id="add">'.$value.'</button>';
        }else if (isset($user_privileges)) {
            if (in_array(1, $user_privileges)){
              echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-'.$target.'" data-backdrop="static" data-keyboard="false" id="add">'.$value.'</button>';
            }
        }
    }
    function edit_button($user_privileges =array(),$id,$class){
        if ($_SESSION['admin_data']['role'] == 1) {
            echo '<td><button type="button" class="btn btn-primary '.$class.'" value='.$id.'><i class="fas fa-edit"></i></button></td>';
        }else if (isset($user_privileges) && !empty($user_privileges)) {
            if (in_array(2, $user_privileges)){
            echo '<td><button type="button" class="btn  btn-primary '.$class.'" value='.$id.'><i class="fas fa-edit"></i></button></td>';
            }
        }
    }
    function view_button($user_privileges =array(),$id,$class){
        if ($_SESSION['admin_data']['role'] == 1) {
            echo '<td><button type="button" class="btn btn-primary '.$class.'" value='.$id.'><i class="fas fa-edit"></i> </button><button type="button" class="btn btn-success view_content" value='.$id.'><i class="fas fa-eye"></i></button></td>';
        }else if (isset($user_privileges) && !empty($user_privileges)) {
            $btns="<td>";
            if (in_array(2, $user_privileges)){
              $btns=$btns.'<button type="button" class="btn btn-primary '.$class.'" value='.$id.'><i class="fas fa-edit"></i></button>';
            }

            if (in_array(4, $user_privileges)){
                $btns=$btns.'<button type="button" class="btn btn-success view_content" value='.$id.'><i class="fas fa-eye"></i></button>';
            }
            $btns=$btns."</td>";
            echo $btns;
        }
    }
    function delete_button($value = "Delete",$user_privileges =array(),$name){
        if ($_SESSION['admin_data']['role'] == 1) {
            echo ' <button type="submit" name="btn_delete" class="btn btn-danger" form="'.$name.'" >'.$value.'</button>';
        }else if (isset($user_privileges) && !empty($user_privileges)) {
            if (in_array(3, $user_privileges)){
              echo ' <button type="submit" name="btn_delete" class="btn btn-danger" form="'.$name.'" >'.$value.'</button>';;
            }
        }
    }
    function import_button($user_privileges =array()){
        if ($_SESSION['admin_data']['role'] == 1) {
            echo '
             <a href="sample_import/import_sample.xlsx"><button type="button" class="btn btn-dark"  data-toggle="tooltip" title="Import sample file" download="import_sample">Import Sample</button></a>
            <input style="display:none;" type="file" id="importexcel" name="importexcel">
                    <button id="import-excel-submit" for="importexcel" data-toggle="tooltip" title="Import coupons" class="btn btn-primary"><i class="fa fa-upload"></i>
                        <svg class="lds-dual-ring" xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" stroke="#fff">
                        <g fill="none" fill-rule="evenodd" stroke-width="2">
                            <circle cx="22" cy="22" r="12.1001">
                                <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite"/>
                                <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="22" cy="22" r="19.5005">
                                <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite"/>
                                <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite"/>
                            </circle>
                        </g>
                    </svg>
                    </button>';
        }else if (isset($user_privileges) && !empty($user_privileges)) {
            if (in_array(1, $user_privileges)){
              echo '
               <a href="sample_import/import_sample.xlsx"><button type="button" class="btn btn-dark"  data-toggle="tooltip" title="Import sample file" download="import_sample">Import Sample</button></a>
              <input style="display:none;" type="file" id="importexcel" name="importexcel">
                    <button id="import-excel-submit" for="importexcel" data-toggle="tooltip" title="Import coupons" class="btn btn-primary"><i class="fa fa-upload"></i>
                        <svg class="lds-dual-ring" xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" stroke="#fff">
                        <g fill="none" fill-rule="evenodd" stroke-width="2">
                            <circle cx="22" cy="22" r="12.1001">
                                <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite"/>
                                <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="22" cy="22" r="19.5005">
                                <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite"/>
                                <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite"/>
                            </circle>
                        </g>
                    </svg>
                    </button>';;
            }
        }
    }
 ?>

 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>

    </ul>
    <ol class="breadcrumb route">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-home">Home</i></a></li>
        <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['pages'];?></a></li>
    </ol>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a href="logout.php">
          <i class="btn btn-info">Logout</i>
        </a>
      </li>
    </ul> 
  </nav>