<?php
//设置会话
require_once '../functions.php';
xiu_get_current_user();

function convert_status($status){
  $dict=array(
    'published'=>'已发布',
    'drafted'=>'草稿',
    'trashed'=>'回收站'
  );
  return isset($dict[$status])? $dict[$status]:'未知错误';
}

function convert_date($created) {
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}


//todo:总页码数=ceil（总条数/每页条数）
//每页条数
$size=10;
$page=isset($_GET['page'])?(int)$_GET['page']:1;
if ($page < 1) {
  header('Location: /admin/posts.php?page=1'.$search);
}
//总条数
$total_count = (int)xiu_fetch_one("select count(1) as count from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id")['count'];
//总页码数
$total_pages = (int)ceil($total_count / $size);
if($page>$total_pages){
header('Location:/admin/posts.php?page='.$total_pages.$search);
}
//var_dump ($total_pages);  19


//todo:控制每页显示的数据
//每页开始页码
$offset = ($page - 1) * $size;
//每页数据

//筛选
//获取所有的分类并添加到页面中
$allcate=xiu_fetch_all('select * from categories;');
//var_dump ($allcate);

//查询条件
$where='1=1';
//记录当前状态
$search='';

if(isset($_GET['category'])){
    //字符串和数字拼接(直接拼接)
     $where .= ' and posts.category_id = ' . $_GET['category'];
     $search.='&category='.$_GET['category'];
}
if(isset($_GET['status'])){
    //字符串和字符串拼接
      $where .= " and posts.status = '{$_GET['status']}'";
      $search.='&status='.$_GET['status'];
}



////筛选状态之后的总条数
//if(isset($_GET['category']) && isset($_GET['status'])){
//$choose_pages=xiu_fetch_one("select count(1) as count
//from posts
//inner join categories on posts.category_id = categories.id
//inner join users on posts.user_id = users.id
//where {$where}
//order by posts.created desc;
//");
//var_dump ($choose_pages);
//}else{
$posts = xiu_fetch_all("select
  posts.id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  posts.created,
  posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
order by posts.created desc
limit {$offset}, {$size};");
//var_dump ($posts);

//}


//todo:控制页面显示的页码
/*
  1. 当前页码显示高亮

  2. 左侧和右侧各有2个页码
  3. 开始页码不能小于1
  4. 结束页码不能大于最大页数

  5. 当前页码不为1时显示上一页
  6. 当前页码不为最大值是显示下一页

  7. 当开始页码不等于1时显示省略号
  8. 当结束页码不等于最大时显示省略号
*/
//5
//$begin=$page-2;
//$end=$begin+4;
//$begin=$begin<1?1:$begin;
//$end=$begin+4;
//$end=$end>$total_pages?$total_pages:$end;
//$begin=$end-4;
//$begin=$begin<1?1:$begin;

$visiable=5;
$region=(int)($visiable/2);//2
//var_dump ($region);
$begin=$page-$region;//2
$end=$begin+($visiable-1);
$begin=$begin<1?1:$begin;
$end=$begin+($visiable-1);
$end=$end>$total_pages?$total_pages:$end;
$begin=$end-($visiable-1);
//echo $begin;
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="/static/assets/css/admin.css">
    <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
   <?php include 'inc/navbar.php'; ?>


     <div class="container-fluid">
          <div class="page-title">
            <h1>所有文章</h1>
            <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
          </div>


      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->


     <div class="page-action">
             <!-- show when multiple checked -->
             <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
             <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
               <select name="category" class="form-control input-sm">

                 <option value="all">所有分类</option>
                 <?php foreach($allcate as $item):?>

                 <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>>
                               <?php echo $item['name']; ?>
                             </option>

                 <?php endForeach?>
               </select>
               <select name="status" class="form-control input-sm">
                 <option value="all">所有状态</option>
                 <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] =='published'? ' selected' : '' ?>>已发布</option>
                  <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
                  <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
               </select>
               <button class="btn btn-default btn-sm" type='submit'>筛选</button>
             </form>
             <ul class="pagination pagination-sm pull-right">
               <?php if($page-1>0):?>
               <li><a href="?page=<?php echo ($page-1).$search; ?>">上一页</a></li>
               <?php
               if ($begin > 1) {
                   print('<li class="disabled"><span>···</span></li>');
               }
               ?>
               <?php endif?>
               <?php for($i=$begin;$i<=$end;$i++):?>
               <li<?php echo $i===$page?' class="active"':''?>><a href='?page=<?php echo $i.$search; ?>'><?php echo $i; ?></a></li>
               <?php endfor?>
                <?php if($page+1<=$total_pages):?>
                <?php
                 if ($end < $total_pages) {
                 print('<li class="disabled"><span>···</span></li>');
                 }
                 ?>
               <li><a href="?page=<?php echo ($page+1).$search; ?>">下一页</a></li>
               <?php endif?>

             </ul>
     </div>


     <table class="table table-striped table-bordered table-hover">
               <thead>
                 <tr>
                   <th class="text-center" width="40"><input type="checkbox"></th>
                   <th>标题</th>
                   <th>作者</th>
                   <th>分类</th>
                   <th class="text-center">发表时间</th>
                   <th class="text-center">状态</th>
                   <th class="text-center" width="100">操作</th>
                 </tr>
               </thead>
               <tbody>
               <?php foreach($posts as $item):?>
                   <tr>
                     <td class="text-center"><input type="checkbox"></td>
                     <td><?php echo $item['title'];?></td>

                     <td><?php echo $item['user_name'];?></td>
                     <td><?php echo $item['category_name'];?></td>

                     <td class="text-center"><?php echo convert_date($item['created']);?></td>

                     <td class="text-center"><?php echo convert_status($item['status']);?></td>

                     <td class="text-center">
                       <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                       <a href="post-delete.php?id=<?php echo $item['id'];?>" class="btn btn-danger btn-xs">删除</a>
                     </td>
                   </tr>
                   <?php endForeach?>
               </tbody>
       </table>
    </div>
  </div>

   <?php $current_page = 'posts'; ?>
   <?php include 'inc/sidebar.php'; ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
