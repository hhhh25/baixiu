<?php
    require_once '../functions.php';

//todo:ɾ�����ݿ��е���Ӧ����

//todo: tip1:����
    if(empty($_GET['id'])){
       exit('ȱ�ٱ�Ҫ������');
    }
    $id=$_GET['id'];

//todo: tip2:�������ݿ����ɾ������
//     $rows=xiu_execute("delete from categories where id ='{$id}';");
//     $rows=xiu_execute("delete from categories where id  in '{$id}';");
     $rows = xiu_execute('delete from users where id in (' . $id . ');');

//todo: tip3:��תҳ��
    header('Location:/admin/users.php');