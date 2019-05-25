<?php
    require_once '../functions.php';

//todo:删除数据库中的相应数据

//todo: tip1:传参
    if(empty($_GET['id'])){
       exit('缺少必要参数！');
    }
    $id=$_GET['id'];

//todo: tip2:连接数据库进行删除操作
//     $rows=xiu_execute("delete from categories where id ='{$id}';");
//     $rows=xiu_execute("delete from categories where id  in '{$id}';");
     $rows = xiu_execute('delete from posts where id in (' . $id . ');');
     var_dump ($rows);

//todo: tip3:不跳转页面 就在当前分页的页面里进行删除
//不应该跳转到首页 而是跳到点击删除之前的页面
//    header('Location:/admin/posts.php');

header('Location:'.$_SERVER['HTTP_REFERER']);