<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  require_once('connection.php');
  if(!isset($_SESSION["admin_data"])){
    header("location:index.php");
    die;
  }
  $_SESSION['pages'] = 'Content';

  $sql = "SELECT * FROM tbl_content_category  ORDER BY update_at DESC";
  $result = mysqli_query($conn, $sql); 

  $query = "SELECT * FROM tbl_content_type  ORDER BY update_at DESC";
  $result_type = mysqli_query($conn, $query); 

    if (isset($_GET['filter_category'])) {
            $filter_category = $_GET['filter_category'];
        } else {
            $filter_category = '';
    }
    if (isset($_GET['filter_type'])) {
            $filter_type = $_GET['filter_type'];
        } else {
            $filter_type = '';
    }
    if (isset($_GET['filter_tag'])) {
            $filter_tag = $_GET['filter_tag'];
        } else {
            $filter_tag = '';
    }

    $url = '';

    if (isset($_GET['filter_category'])) {
        $url .= '?filter_category=' . urlencode(html_entity_decode($_GET['filter_category'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($_GET['filter_type'])) {
        $url .= '&filter_type=' . urlencode(html_entity_decode($_GET['filter_type'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($_GET['filter_tag'])) {
        $url .= '&filter_tag=' . $_GET['filter_tag'];
    }



  $content_query = "SELECT c.*,cc.category_name, ct.type_name 
    FROM tbl_content as c 
    LEFT JOIN tbl_content_category as cc ON (c.content_category = cc.id) 
    LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id)
  ";
  $content_query .= ' WHERE 1=1 ';
    if (!empty($_GET['filter_category'])) {
        $content_query .= " AND cc.id LIKE '" . $_GET['filter_category'] . "%'";
    }

    if (!empty($_GET['filter_type'])) {
        $content_query .= " AND ct.id LIKE '" . $_GET['filter_type'] . "%'";
    }

    if (!empty($_GET['filter_tag'])) {
        $content_query .= " AND c.tag LIKE '%" . $_GET['filter_tag'] . "%'";
    }

  $content_query .= ' order by c.update_at DESC';
  $result_content = mysqli_query($conn, $content_query); 
  
  if (isset($_POST['content_submit']) && $_POST['content_submit'] == 'content_add') 
  { 
    $content_category=mysqli_real_escape_string($conn,$_POST['content_category']);
    $content_type=mysqli_real_escape_string($conn,$_POST['content_type']);
    $tag=mysqli_real_escape_string($conn,$_POST['tag']);
    $content_description=mysqli_real_escape_string($conn,$_POST['content_description']);


     $sql = "INSERT INTO tbl_content (content,content_category,tag,content_type,date_added) VALUES ('$content_description','$content_category','$tag','$content_type', NOW())";
 
    if (mysqli_query($conn, $sql)) {
    $_SESSION['insert_success'] = true;
       header("location: content.php");
       die;
    }
  }


  if (isset($_POST['content_submit']) && $_POST['content_submit'] == 'edit_content') 
  {
    $content_category=mysqli_real_escape_string($conn,$_POST['content_category']);
    $content_type=mysqli_real_escape_string($conn,$_POST['content_type']);
    $tag=mysqli_real_escape_string($conn,$_POST['tag']);
    $content_description=mysqli_real_escape_string($conn,$_POST['content_description']);
    $sql = "UPDATE tbl_content 
            SET content='".$content_description."',
                content_category='".$content_category."',
                tag='".$tag."',
                content_type='".$content_type."'
            WHERE id='".$_POST['content_id']."'";
      
    if (mysqli_query($conn, $sql)) {
    $_SESSION['update_success'] = true;
       header("location: content.php");
       die;
    }
  }

  if(isset($_POST['btn_delete'])){

     foreach ($_POST['checked_id'] as $key => $value) {
       $sql = "DELETE FROM tbl_content WHERE id='".$value."'"; 
       mysqli_query($conn, $sql); 
     }
      $sql = "SELECT c.*,cc.category_name, ct.type_name 
        FROM tbl_content as c 
        LEFT JOIN tbl_content_category as cc ON (c.content_category = cc.id) 
        LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id)
          ORDER BY update_at DESC
      ";
        $result_content = mysqli_query($conn, $sql);

        $_SESSION['delete_success'] = true;
        header("location: content.php");
        die;
      }

  if(isset($_POST['btn_download']))
  { 
      require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';
      $objPHPExcel = new PHPExcel();
      // Set document properties
      $objPHPExcel->getProperties()
                      ->setTitle("Content");
      // Add some data
      $objPHPExcel->setActiveSheetIndex(0)
               ->setCellValue('A1', 'Content')
               ->setCellValue('B1', 'Tag')
               ->setCellValue('C1', 'Content Category')
               ->setCellValue('D1', 'Content Type');

      $col = 2;
      if(isset($_POST['checked_id']))
      {  
         foreach ($_POST['checked_id'] as $key => $value) 
         {
            $sql = "SELECT c.*,cc.category_name, ct.type_name 
                    FROM tbl_content as c 
                     LEFT JOIN tbl_content_category as cc ON (c.content_category = cc.id) 
                     LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id)
                     WHERE c.id='".$value."'   ORDER BY update_at DESC";
                  $result_content = mysqli_query($conn, $sql);
                  if (mysqli_num_rows($result_content) > 0) 
                  {
                     while($row = mysqli_fetch_assoc($result_content)) 
                     {
                        $objPHPExcel->setActiveSheetIndex(0)
                           ->setCellValue('A'.$col, strip_tags(html_entity_decode($row['content'])))
                           ->setCellValue('B'.$col, $row['tag'])
                           ->setCellValue('C'.$col, $row['category_name'])
                           ->setCellValue('D'.$col, $row['type_name']);
                           $col++;
                     }

                  }
         }
      }
      else
      {   
         $sql = "SELECT c.*,cc.category_name, ct.type_name 
              FROM tbl_content as c 
            LEFT JOIN tbl_content_category as cc ON (
            c.content_category = cc.id) 
            LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id)   ORDER BY update_at DESC";
         $result_content = mysqli_query($conn, $sql);
         if (mysqli_num_rows($result_content) > 0) 
         {
            while($row = mysqli_fetch_assoc($result_content)) 
            {
               $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$col, strip_tags(html_entity_decode($row['content'])))
                  ->setCellValue('B'.$col, $row['tag'])
                  ->setCellValue('C'.$col, $row['category_name'])
                  ->setCellValue('D'.$col, $row['type_name']);
                  $col++;
            }

         }
      }

     
      $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      // Redirect output to a client’s web browser (Excel2007)
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="Content.xls"');
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');

      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
      die();

  }

    require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';
    require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

    if (isset($_FILES['importexcel'])) {
        $objPHPExcel = new PHPExcel();
        $allcouponsarray = array();

        if (isset($_FILES['importexcel'])) {
              $uploadedfile = $_FILES['importexcel'];

              $allcouponsarray = array();
              $allcoupons = array();
              $done = 0;
              $totaladded = 0;
               if (isset($uploadedfile['tmp_name'])) {
                      $objPHPExcel = PHPExcel_IOFactory::load($uploadedfile['tmp_name']);
                      $sheet = $objPHPExcel->getSheet(0);
                      $highestRow = $sheet->getHighestRow();
                      $highestColumn = $sheet->getHighestColumn();
                      for ($row = 2; $row <= $highestRow; $row++){
                          //  Read a row of data into an array
                          $allcouponsarray[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE, FALSE)[0];
                      }
              }else{
              }
         }
         $i = 5;
         $json = array();
          $json_file_array = array();
          $array_size = 200;
          if(!is_dir(dirname(__FILE__) . "/content")){
              mkdir(dirname(__FILE__) . "/content",0777,true);
          }

          $index = 0;
          while($index < count($allcouponsarray))
          {
            $item = $allcouponsarray[$index];
            $enable = false;

            foreach ($item as $key => $value) {
              if($value)
              {
                $enable = true;
              }
            }

            if(!$enable)
            {
              array_splice($allcouponsarray, $index,1);
            }
            else
            {
              $index++;
            }
          }
          

          if (isset($allcouponsarray[0][3])) {
             foreach (array_chunk($allcouponsarray, $array_size, true) as $key => $value) {

                 $pathis = dirname(__FILE__)."/content/";
                 if(!is_dir($pathis)){
                     mkdir($content,0777,true);
                 }
                 file_put_contents( $pathis . "json_array".$key.".json" , json_encode($value));
                 $json['json_file_array'][] = $pathis . "json_array".$key.".json";
             }
            header('Content-Type: application/json');
            echo json_encode($json);
          }else{
             echo "<pre>"; print_r(4564); echo "</pre>";die;
          }
          die();
    }
    
    if (isset($_POST['request']) && $_POST['request'] == 'upload_data_in_db') {
       $json_file_array = $_POST['json_file_array'];
       $count = $_POST['count'];
       $allcoupons = array();
       if(isset($json_file_array[$count]) && $json_file_array[$count])$allcoupons = (array)json_decode(file_get_contents($json_file_array[$count]),true);
       else $allcoupons = '';

       $json['content'] = $allcoupons;
       $mycontent = $allcoupons;
       $coupon_code = array();
       if($allcoupons){

            if (!empty($allcoupons)) {
                   foreach ($allcoupons as $key => $coupon_data) {
                    
                        $get_category = "SELECT id FROM tbl_content_category WHERE category_name='".$coupon_data[2]."'   ORDER BY update_at DESC";
                        $get_record = mysqli_query($conn, $get_category);
                        $row = mysqli_fetch_assoc($get_record);

                        if(!isset($row['id']))
                        {
                          $insert_category = "INSERT into tbl_content_category(category_name) Values('" . $coupon_data[2] . "')";
                          mysqli_query($conn,$insert_category);
                          $row = array('id'=>mysqli_insert_id($conn));
                        } 
                        $get_type = "SELECT id FROM tbl_content_type WHERE type_name='".$coupon_data[3]."'   ORDER BY update_at DESC";
                        $result_content_type = mysqli_query($conn, $get_type);
                        $type = mysqli_fetch_assoc($result_content_type);


                        if(!isset($type['id']))
                        {
                          $insert_type = "INSERT into tbl_content_type(type_name) Values('" . $coupon_data[3] . "')";
                          mysqli_query($conn,$insert_type);
                          $type = array('id'=>mysqli_insert_id($conn));
                        }
                       $sql = "INSERT INTO tbl_content (`id`, `content`, `content_category`, `tag`, `content_type`, `date_added`)
                       VALUES (null,'".$coupon_data[0]."', '".$row['id']."', '".$coupon_data[1]."', '".$type['id']."',NOW())";
                       
                       $result_content = mysqli_query($conn, $sql);
                   }
               }
               unlink($json_file_array[$count]);
               $json['count'] = $count + 1;
           if (empty($mycontent)) {
               $json['success']='Update successfully';
           }
       }
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    if(isset($_POST['btn_download_pdf'])){
      require_once('TCPDF/examples/tcpdf_include.php');

      // create new PDF document
      $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

      // set document information
      $current_date=date('Y-m-d h:i:s');

      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor('Nicola Asuni');
      $pdf->SetTitle('Contant');
      $pdf->SetSubject('Contant'.$current_date);
      $pdf->SetKeywords('Contant');

      // set default header data
      
      $pdf->SetHeaderData('', 0, 'Content List', $current_date);

      // set header and footer fonts
      $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
      $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

      // set default monospaced font
      $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

      // set margins
      $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
      $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
      $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

      // set auto page breaks
      $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

      // set image scale factor
      $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

      // set some language-dependent strings (optional)
      if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
      }

      // ---------------------------------------------------------

      // set font
      $pdf->SetFont('dejavusans', '', 10);

      // add a page
      $pdf->AddPage();

      //get data what need display

      $data=array();

      if(isset($_POST['checked_id']))
        {  
           foreach ($_POST['checked_id'] as $key => $value) 
            {
              $sql = "SELECT c.*,cc.category_name, ct.type_name 
                      FROM tbl_content as c 
                       LEFT JOIN tbl_content_category as cc ON (c.content_category = cc.id) 
                       LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id)
                       WHERE c.id='".$value."' order by c.update_at DESC";
                    $result_content = mysqli_query($conn, $sql);
                    $num_row = mysqli_num_rows($result_content);
                    if ($num_row > 0) 
                    {
                       while($row = mysqli_fetch_assoc($result_content)) 
                       {
                          array_push($data, $row);
                       }

                    }
            }
        }
        else
        {   
           $sql = "SELECT c.*,cc.category_name, ct.type_name 
                FROM tbl_content as c 
              LEFT JOIN tbl_content_category as cc ON (c.content_category = cc.id) 
              LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id) order by c.update_at DESC";

           $result_content = mysqli_query($conn, $sql);
           $num_row = mysqli_num_rows($result_content);

           if ($num_row > 0){
              while($row = mysqli_fetch_assoc($result_content)) 
               {
                  array_push($data, $row);
               }

           }
        }

      // create some HTML content

      $html = '<h2>Content List:</h2>
      <table border="1" cellpadding="8">
      <thead>
        <tr bgcolor="#171b60">
          <th style="font-weight: bold; color: white; font-size:13px; height: 34px; width:40%;"  align="center" >Content</th>
          <th style="font-weight: bold; color: white; font-size:13px; height: 34px;width:20%;"  align="center" >Tag</th>
          <th style="font-weight: bold; color: white; font-size:11px; height: 34px;width:20%;"  align="center" >Content Category</th>
          <th style="font-weight: bold; color: white; font-size:13px; height: 34px; width:20%;"  align="center" >Content Type</th>
        </tr>
        </thead>
        <tbody>';
        foreach ($data as $key => $row) {
          $html=$html.'
          <tr><td style="width:40%;">'.$row['content'].'</td>
          <td style="width:20%;">'.$row['tag'].'</td>
          <td style="width:20%;">'.$row['category_name'].'</td>
          <td style="width:20%;">'.$row['type_name'].'</td></tr>';
        }
        $html=$html.'</tbody></table></div>';
        // echo $html; exit;
        

      // output the HTML content
      $pdf->writeHTML($html, true, false, true, false, '');


      // reset pointer to the last page
      $pdf->lastPage();

      // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
      // Print a table


      // ---------------------------------------------------------

      //Close and output PDF document
      $pdf->Output('Contant('.$current_date.').pdf', 'D');

      //============================================================+
      // END OF FILE
      //============================================================+
    }
   
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge; charset=UTF-8">
    <title>Content List</title>
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
                            <h1>Content List</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Content List</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>


            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title pull-left float-l">Filters</h3>
                                <div class="pull-right float-r">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form role="form" name="filter" id="filter">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Content Categoty</label>
                                                <select class="form-control select2bs4 hei-auto" name="filter_category"
                                                    id="filter_category">
                                                    <option> Please Select </option>

                                                    <?php
                            $cat_data = array();
                            if (mysqli_num_rows($result) > 0){
                              while($row = mysqli_fetch_assoc($result)) { 
                                 $cat_data[] = $row;  
                                 $selected = '';
                                 if($filter_category == $row['id'])
                                 {
                                    $selected = 'selected';
                                 }
                                 echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['category_name'] . "</option>";
                              } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Content Type</label>
                                                <select class="form-control select2bs4 width-100" name="filter_type"
                                                    id="filter_type">
                                                    <option> Please Select </option>
                                                    <?php
                            $type_data = array();
                            if (mysqli_num_rows($result_type) > 0){
                              while($row_data = mysqli_fetch_assoc($result_type)) { 
                                 $selected = '';
                                 if($filter_type == $row_data['id'])
                                 {
                                    $selected = 'selected';
                                 }
                                 $type_data[] = $row_data;
                                 echo "<option value='" . $row_data['id'] . "' " . $selected . ">" . $row_data['type_name'] . "</option>";
                              } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Tag </label>
                                                <input type="text" class="form-control" placeholder="Enter Tag..."
                                                    id="filter_tag" name="filter_tag" value="<?php echo $filter_tag ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 offset-md-8 text-right float-r">
                                            <button type="submit" name="button-filter" id="button-filter"
                                                class="btn btn-secondary" form="search_form">Filter</button>
                                            <button id="button-filter-clear" class="btn btn-info">clear</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>


                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title pull-left float-l">Content List</h3>

                                <div class="pull-right float-r">


                                    <?php  import_button($user_privileges);?>
                                    <!-- <button class="btn btn-info" name="btn_test" form="content_form">test download</button> -->
                                    <button class="btn btn-info" name="btn_download" form="content_form"><i
                                            class="fa fa-download"></i></button>
                                    <button class="btn btn-secondary" name="btn_download_pdf" form="content_form"><i
                                            class="fa fa-download"> PDF</i></button>

                                    <?php 
                    add_button("Add Content",$user_privileges,"content");
                   ?>
                                    <?php if(in_array(3, $user_privileges) || $_SESSION['admin_data']['role'] == 1){ ?>
                                    <a class="btn btn-danger color-white" data-toggle="modal"
                                        data-target="#modal-delete" form="user_form">Delete
                                    </a>
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
                              delete_button("Yes, Delete",$user_privileges,"content_form");
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

                            <div class="modal fade" id="modal-content" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Add Content</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-content" name="form-content">
                                                <input type="hidden" name="content_id" id="content_id_add" value="">
                                                <div class="form-group">
                                                    <label for="category">Content Category</label>
                                                    <select class="form-control select2bs4 hei-auto"
                                                        name="content_category" id="content_category_add">
                                                        <?php
                            if (mysqli_num_rows($result) > 0){
                                foreach ($cat_data as $key => $row) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['category_name'] . "</option>"; 
                                }
                          } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="type">Content Type</label>
                                                    <select class="form-control select2bs4 width-100"
                                                        name="content_type" id="content_type_add">
                                                        <?php
                           // echo "<pre>"; print_r($type_data); echo "</pre>";die; 
                            if (mysqli_num_rows($result_type) > 0){
                                foreach ($type_data as $key => $row_data) {
                                 echo "<option value='" . $row_data['id'] . "'>" . $row_data['type_name'] . "</option>";
                                }
                               } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tag">Tag</label>
                                                    <textarea class="form-control" rows="5" id="tag_add" name="tag"
                                                        word-limit="true" max-words="1000" min-words="1"></textarea>
                                                    <span id="TagCount_add"></span>
                                                    <div class="writing_error"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="name">Content Description</label>
                                                    <textarea class="textarea1 custom textarea-favo"
                                                        placeholder="Place some text here" name="content_description"
                                                        id="con_add"></textarea>
                                                    <span id="maxContentPost_add"></span>
                                                </div>
                                                <button type="submit" name="content_submit" id="content_submit_add"
                                                    value="content_add" class="btn btn-default">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- edit modal -->
                            <div class="modal fade" id="modal-content-edit" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Edit Content
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-3">

                                            <form method="post" id="form-content" name="form-content">
                                                <input type="hidden" name="content_id" id="content_id" value="">
                                                <div class="form-group">
                                                    <label for="category">Content Category</label>
                                                    <select class="form-control select2bs4 hei-auto"
                                                        name="content_category" id="content_category">
                                                        <?php
                            if (mysqli_num_rows($result) > 0){
                                foreach ($cat_data as $key => $row) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['category_name'] . "</option>"; 
                                }
                          } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="type">Content Type</label>
                                                    <select class="form-control select2bs4 width-100"
                                                        name="content_type" id="content_type">
                                                        <?php
                           // echo "<pre>"; print_r($type_data); echo "</pre>";die; 
                            if (mysqli_num_rows($result_type) > 0){
                                foreach ($type_data as $key => $row_data) {
                                 echo "<option value='" . $row_data['id'] . "'>" . $row_data['type_name'] . "</option>";
                                }
                               } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tag">Tag</label>
                                                    <textarea class="form-control" rows="5" id="tag" name="tag"
                                                        word-limit="true" max-words="1000" min-words="1"></textarea>
                                                    <span id="TagCount"></span>
                                                    <div class="writing_error"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="name">Content Description</label>
                                                    <textarea class="textarea1 custom textarea-favo"
                                                        placeholder="Place some text here" name="content_description"
                                                        id="con"></textarea>

                                                    <span id="maxContentPost"></span>
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
                                                                        <button type="submit" name="content_submit"
                                                                            id="content_submit" value="content_add"
                                                                            class="btn btn-danger">Submit</button>

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
                            <!-- edit modal end -->

                            <!-- /.card-header -->
                            <div class="card-body">
                                <form method="post" name="content_form" id="content_form">
                                    <div class="table-responsive">
                                        <table id="content" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <center><input type="checkbox" id="select_all" value="" />
                                                        </center>
                                                    </th>
                                                    <th>Content</th>
                                                    <th>Tag</th>
                                                    <th>Content Category</th>
                                                    <th>Content Type</th>
                                                    <th>Date Added</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                  if (mysqli_num_rows($result_content) > 0) 
                  {
                    while ($row= mysqli_fetch_assoc($result_content)) { 
                     $tags=explode(" ",$row['tag']);
                      if($_SESSION['admin_data']['role'] == 1 || 
                          (!empty($user_privileges) && in_array(4, $user_privileges))){
                      ?>
                                                <tr>
                                                    <td align="center"><input type="checkbox" name="checked_id[]"
                                                            class="checkbox" value="<?php echo $row['id']; ?>" /></td>
                                                    <td class="content_limit">
                                                        <span><?php echo strip_tags(html_entity_decode($row['content']));?></span>
                                                    </td>
                                                    <td class="tag_limit"><span>
                                                            <?php 
                                 foreach ($tags as $key => $value) { ?>
                                                            <span
                                                                class="badge badge-pill badge-info"><?php  echo $value;?></span>
                                                            <?php } ?>
                                                        </span></td>
                                                    <td><?php echo $row['category_name'];?></td>
                                                    <td><?php echo $row['type_name'];?></td>
                                                    <td><?php echo date("d-m-Y",strtotime($row['date_added']));?></td>

                                                    <?php view_button($user_privileges,$row['id'],"edit_content"); ?>

                                                </tr>
                                                <?php  } } } 
                  ?>
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
        <div class="modal fade" id="modal-view-content" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h4 class="modal-title w-100 font-weight-bold" id="sign-up">Content</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Content Category</th>
                                    <td><span class="content_category"></span></td>
                                </tr>
                                <tr>
                                    <th>Content Type</th>
                                    <td><span class="content_type"></span></td>
                                </tr>
                                <tr>
                                    <th>Tag</th>
                                    <td><span class="content_tag"></span></td>
                                </tr>
                                <tr>
                                    <th>Content Description</th>
                                    <td>
                                        <div class="content_description"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    toastr.success('Insert Content SuccessFully');
    </script>
    <?php
    unset($_SESSION['insert_success']);
   } ?>

    <?php
  if(isset($_SESSION['update_success']) && $_SESSION['update_success']){?>
    <script type="text/javascript">
    toastr.success('Content Updated SuccessFully');
    </script>
    <?php
    unset($_SESSION['update_success']);
   } ?>

    <?php
  if(isset($_SESSION['delete_success']) && $_SESSION['delete_success']){?>
    <script type="text/javascript">
    toastr.success('Content Deleted SuccessFully');
    </script>
    <?php
    unset($_SESSION['delete_success']);
   } ?>

    <script>
    $(document).on('click', '.edit_content', function() {
        $('#form-content')[0].reset();
        $this = $(this);
        $.ajax({
            type: "POST",
            url: "call_data.php",
            dataType: 'json',
            data: {
                content_id: $this.val()
            },
            success: function(data) {
                console.log(data)
                $("#tag").val(data.tag);
                $("#con").summernote("code", data.content);
                $('#content_category option[value="' + data.content_category + '"]').prop(
                    "selected", true);
                $('#content_type option[value="' + data.content_type + '"]').prop("selected", true);
                $('#content_id').val(data.id);
                $('#content_submit').val("edit_content");
                $('#sign-up').html("Edit content");
                $('#modal-content-edit').modal('show');
                $('#content_category').select2('');
                $('#content_type').select2('');
                $("label.error").remove();
            }
        });
    });

    $(document).on('click', '.view_content', function() {
        $this = $(this);
        $.ajax({
            type: 'post',
            url: "call_data.php",
            dataType: 'json',
            data: {
                content_id: $this.val()
            },
            success: function(json) {
                $model = $('#modal-view-content');
                $('.content_category', $model).text(json.category_name);
                $('.content_type', $model).text(json.type_name);
                $('.content_tag', $model).text(json.tag);
                $('.content_description', $model).html(json.content);
                $model.modal('show');
            }
        });
        return false;
    })


    $(document).ready(function() {
        $("#button-filter-clear").click(function() {
            $('#filter')[0].reset();
            $('#filter_category').select2('');
            $('#filter_type').select2('');
            window.location.href = window.location.origin + window.location.pathname;
            return false;
        });

        $('#content_submit').on('click', function() {
            $("form[name='form-content']").validate({
                rules: {
                    tag: "required",
                    content_description: "required"
                },
                messages: {
                    tag: "Please enter Tag Name",
                    content_description: "Please Enter Content Description"
                }
            });

        });

        $("#add").on('click', function() {
            $("label.error").remove();
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


    $("textarea[word-limit=true]").each(function() {
        $(this).attr("placeholder", "Writing entries: " + $(this).attr("min-words") + " Letter min, " + $(this)
            .attr("max-words") + " Letter max");
    });


    // Add event trigger for change to textareas with limit
    $(document).on("input", "textarea[word-limit=true]", function() {


        // Get individual limits
        thisMin = parseInt($(this).attr("min-words"));
        thisMax = parseInt($(this).attr("max-words"));

        // Create array of words, skipping the blanks
        var removedBlanks = [];
        removedBlanks = $(this).val().split(/\s+/).filter(Boolean);

        // Get word count
        var wordCount = removedBlanks.length;

        var value = $(this).val();

        if (value.length == 0 || wordCount == 0) {
            $('#TagCount').html("");
        } else {
            $('#TagCount').html("Letter Count:" + value.length);
        }
        // Remove extra words from string if over word limit
        if (wordCount > thisMax) {

            // Trim string, use slice to get the first 'n' values
            var trimmed = removedBlanks.slice(0, thisMax).join(" ");

            // Add space to ensure further typing attempts to add a new word (rather than adding to penultimate word)
            $(this).val(trimmed + " ");

        }


        // Compare word count to limits and print message as appropriate
        // google 
    });
    </script>

    <script type="text/javascript">
    function ajax_add_coupan_repeter(json_file_array, count) {
        jQuery('.loader').show();
        jQuery.ajax({
            url: 'content.php',
            data: {
                request: 'upload_data_in_db',
                json_file_array: json_file_array,
                count: count
            },
            type: 'post',
            dataType: 'json',
            success: function(json) {
                if (json.content === "") {
                    jQuery("#import-excel-submit").val('Import');
                    jQuery(".lds-dual-ring").fadeOut();
                    jQuery("#import-excel-submit i").removeClass('hide_this');
                    jQuery("#import-excel-submit").prop('disabled', false);
                    toastr.success('Content Import SuccessFully');
                    location.reload();

                } else {
                    setTimeout(function() {
                        ajax_add_coupan_repeter(json_file_array, json.count);
                    }, 1000);
                }
            }
        });
    }
    </script>
    <script type="text/javascript">
    $(document).ready(function() {
        jQuery(".lds-dual-ring").fadeOut();
        $("#import-excel-submit").on('click', function(e) {
            e.preventDefault();
            $("#importexcel").click();
        });
        $("#importexcel").on('change', function(e) {
            e.preventDefault();
            jQuery("#import-excel-submit").val('Wait..');
            jQuery(".lds-dual-ring").fadeIn();
            jQuery("#import-excel-submit i").addClass('hide_this');
            jQuery("#import-excel-submit").prop('disabled', true);

            var fromdata = new FormData();
            var file = jQuery(document).find('input[name="importexcel"]');
            var individual_file = file[0].files[0];
            fromdata.append("importexcel", individual_file);

            jQuery.ajax({
                url: 'content.php',
                type: 'POST',
                dataType: 'json',
                contentType: false,
                processData: false,
                data: fromdata,
                beforeSend: function() {},
                complete: function() {},
                success: function(json) {
                    if (json.json_file_array) {
                        ajax_add_coupan_repeter(json.json_file_array, 0);
                    } else {
                        // jQuery("#import-excel-submit").val('Import');
                        jQuery(".lds-dual-ring").fadeOut();
                        jQuery("#import-excel-submit i").removeClass('hide_this');
                        jQuery("#import-excel-submit").prop('disabled', false);
                        // $(_this).text('Crush Images');
                    }
                },
            });
        });
    });
    </script>

    <script type="text/javascript">
    <!--
    $('#button-filter').on('click', function() {

        var url = '?filter=true';

        var filter_category = $('select[name=\'filter_category\']').val();
        if (filter_category && filter_category != 'Please Select') {
            url += '&filter_category=' + encodeURIComponent(filter_category);
        }

        var filter_type = $('select[name=\'filter_type\']').val();

        if (filter_type && filter_type != 'Please Select') {
            url += '&filter_type=' + encodeURIComponent(filter_type);
        }

        var filter_tag = $('input[name=\'filter_tag\']').val();

        if (filter_tag) {
            url += '&filter_tag=' + encodeURIComponent(filter_tag);
        }

        location = 'content.php' + url;
    });
    //
    -->
    $(function
    ()
    {
    $('[data-toggle="tooltip"]').tooltip()
    })
    $(document).ready(function()
    {
    $("#tag").keydown(function(event)
    {
    var
    inputValue
    =
    event.which;
    });
    });

    </script>
</body>

</html>