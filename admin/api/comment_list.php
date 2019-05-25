<?php

require_once '../../functions.php';

//todo:查询评论数据


$page=empty($_GET['page'])?1:intval($_GET['page']);
$length=10;
$offset=($page-1)*$length;

$sql=sprintf("SELECT
comments.*,
posts.title as post_title
from comments
inner join posts on comments.post_id=posts.id
order by comments.created desc
limit %d,%d;",$offset,$length);

$comments=xiu_fetch_all($sql);

$total_count=xiu_fetch_one('select count(1) as count
 from comments
 inner join posts on comments.post_id=posts.id;')['count'];

$total_pages=ceil($total_count/$length);

//将数据转化为JSON格式的字符串
$json=json_encode(array(
    'total_pages'=>$total_pages,
    'comments'=>$comments
));

//设置响应类型
header('Content-Type:application/json');

//响应给客户端
echo $json;
