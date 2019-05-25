<?php
//var_dump($_FILES['avatar']);
//todo: 接收文件
//todo:保存文件
//todo:返回文件url

if(empty($_FILES['avatar'])){
    exit('请上传文件');
}

$avatar=$_FILES['avatar'];

if($avatar['error']!==UPLOAD_ERR_OK){
    exit('上传失败');
}

//补充：检验类型、大小
 //类型
//判断'image/'首次出现的位置是否为0（即文件类型是否以'image/'开头）
if(strpos($avatar['type'],'image/')!==0){
     exit('上传图片类型错误');
}
//大小
if($avatar['size']>1*1024*1024){
    exit('上传图片过大');
}

//移动到网站范围之内
//文件扩展名：
$ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target='../../static/uploads/img-'.uniqid().'.'.$ext;
if(!move_uploaded_file($avatar['tmp_name'],$target)){
    exit('上传失败');
}


echo substr($target,5);
