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
     $rows = xiu_execute('delete from posts where id in (' . $id . ');');
     var_dump ($rows);

//todo: tip3:����תҳ�� ���ڵ�ǰ��ҳ��ҳ�������ɾ��
//��Ӧ����ת����ҳ �����������ɾ��֮ǰ��ҳ��
//    header('Location:/admin/posts.php');

header('Location:'.$_SERVER['HTTP_REFERER']);