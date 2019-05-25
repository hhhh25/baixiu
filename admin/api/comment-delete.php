<?php
    require_once '../../functions.php';

//todo:删除数据库中的相应数据

//todo: tip1:传参
    if(empty($_GET['id'])){
       exit('缺少必要参数！');
    }
    $id=$_GET['id'];

//todo: tip2:连接数据库进行删除操作
     $rows = xiu_execute('delete from comments where id in (' . $id . ');');

//todo: tip3:给客户端返回信号 -->删除成功、失败

    header('Content-Type:application/json');
    //序列化布尔值
    //json数据类型：null,string,number,boolean,object,array
    echo json_encode($rows>0);
    //---->'true'|'false'