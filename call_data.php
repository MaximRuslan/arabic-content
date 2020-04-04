<?php
require_once('connection.php'); 
if(!isset($_SESSION["admin_data"])){
    header("location:index.php");
    die;
  } 
if(!empty($_POST['id'])){
  $sql = "SELECT * FROM tbl_user where id='".$_POST['id']."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
 	echo json_encode($row);
 	die;
 }

 if(!empty($_POST['category_id'])){
 	$sql = "SELECT * FROM tbl_content_category where id='".$_POST['category_id']."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
 	echo json_encode($row);
 	die;
 }

 if(!empty($_POST['type_id'])){
 	$sql = "SELECT * FROM tbl_content_type where id='".$_POST['type_id']."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
 	echo json_encode($row);
 	die;
 }

 if(!empty($_POST['content_id'])){
 	$sql = "SELECT c.*,cc.category_name, ct.type_name
  FROM tbl_content as c 
  LEFT JOIN tbl_content_category as cc ON (c.content_category = cc.id) 
  LEFT JOIN tbl_content_type as ct ON (c.content_type = ct.id)
  where c.id='".$_POST['content_id']."'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
 	echo json_encode($row);
 	die;
 }