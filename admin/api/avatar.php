<?php
//TODO:该文件只会被AJAX使用
require_once '../../config.php';
//TODO:接收客户端通过AJAX发送过来的数据
    if(empty($_GET['email'])){
        exit('缺少必要参数');
    }
    $email=$_GET['email'];
    echo $email;
//TODO:在数据库中查询邮箱对应的头像的地址
    $conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
    if($conn){
        exit('连接数据库失败');
    }
    $res = mysqli_query($conn, "select avatar from users where email = '{$email}' limit 1;");
    if (!$res) {
      exit('查询失败');
    }
    $row = mysqli_fetch_assoc($res);
//TODO:输出头像地址
echo $row['avatar'];