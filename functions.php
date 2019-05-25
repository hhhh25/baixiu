<?php
require_once 'config.php';

session_start();

//TODO:判断session
function xiu_get_current_user(){
if(empty($_SESSION['current_login_user'])){
  header('Location:/admin/login.php');
  exit();
}
return $_SESSION['current_login_user'];

}

//TODO:数据库查询获取多条语句（索引数组套关联数组）
function xiu_fetch_all($sql){
  $conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
  if(!$conn){
    exit('连接数据库失败');
  }

  $query=mysqli_query($conn,$sql);
  if(!$query){
  //查询失败
    return false;
  }
  while($res=mysqli_fetch_assoc($query)){
      $data[]=$res;
  }
  mysqli_free_result($query);
  mysqli_close($conn);

  return $data;
}

//TODO:数据库查询获取单条语句（关联数组）
function xiu_fetch_one($sql){
  $res=xiu_fetch_all($sql);
  return isset($res[0])?$res[0]:null;
}

//TODO:执行增删改操作
function xiu_execute($sql){
  $conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
    if(!$conn){
      exit('连接数据库失败');
    }

    $query=mysqli_query($conn,$sql);
    if(!$query){
    //查询失败
      return false;
    }
    //受影响行数
    $affected_rows=mysqli_affected_rows($conn);

    mysqli_close($conn);

    return $affected_rows;
}
