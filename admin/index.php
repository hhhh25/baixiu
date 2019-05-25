<?php

////todo:启动新会话
//session_start();
//
////todo:判断登录的用户信息是否存在，若不存在则返回登录界面重新登录
//if(empty($_SESSION['current_login_user'])){
//  header('Location:/admin/login.php');
//}
require_once '../functions.php';
xiu_get_current_user();

//$posts_count=xiu_fetch('select count(1) from posts;');
//var_dump($posts_count[0]['count(1)']);

$posts_count=xiu_fetch_one('select count(1) as num from posts;');
$posts_count_drafted=xiu_fetch_one("select count(1) as num from posts where status='drafted';");
$posts_categories=xiu_fetch_one('select count(1) as num from categories;');
$posts_comments=xiu_fetch_one('select count(1) as num from comments;');
$posts_comments_held=xiu_fetch_one("select count(1) as num from comments where status='held';");

//var_dump($posts_count['num']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
      <div class="jumbotron text-center">
        <h2>Let inspiration flow at your fingertips!</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo ($posts_count['num'])?></strong>篇文章（<strong><?php echo ($posts_count_drafted['num'])?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo ($posts_categories['num'])?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo ($posts_comments['num'])?></strong>条评论（<strong><?php echo ($posts_comments_held['num'])?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
              <canvas id="chart"></canvas>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $current_page = 'index'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/Chart.js"></script>

  //TODO:chart
  <script>
     var ctx = document.getElementById('chart').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'pie',
          data: {
            datasets: [
              {

                data: [<?php echo ($posts_count['num'])?>, <?php echo ($posts_categories['num'])?>, <?php echo ($posts_comments['num'])?>],
                backgroundColor: [
                  'hotpink',
                  'silver',
                  'blue',
                ]
              },
            ],

            labels: [
              '文章',
              '分类',
              '评论'
            ]
          }
        });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
