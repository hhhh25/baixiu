<?php
//引入配置文件
require_once '../config.php';
//启动会话
session_start();

function login(){
//todo:接收并校验
//todo:持久化
//todo:响应

if(empty($_POST['email'])){
  $GLOBALS['message']='请输入邮箱！';
  return;
}
if(empty($_POST['password'])){
  $GLOBALS['message']='请输入密码！';
  return;
}

  $email=$_POST['email'];
  $password=$_POST['password'];

//todo:连接数据库
$conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
if(!$conn){
  exit('<h3>数据库连接失败！</h3>');
}

//todo:发送一条 MySQL 查询
$query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");
if(!$query){
  $GLOBALS['massage']='登录失败，请重试！';
  return;
}

//todo:查看结果集(这一行的数据)
$user=mysqli_fetch_assoc($query);
//var_dump ($user);
if(!$user){
  $GLOBALS['message']='该用户不存在！';
  return;
}
if($user['password']!==$password){
  $GLOBALS['message']='邮箱与密码不匹配！';
  return;
}


//todo:设置会话
//TODO:为了后续可直接获取当前登录用户的信息，这里直接将用户信息放在session中（这里 session相当于一个箱子）
 $_SESSION['current_login_user'] = $user;

//todo:跳转页面
 header('Location:/admin/index.php');

}
if($_SERVER['REQUEST_METHOD']==='POST'){
  login();
}
if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']==='logout'){
//todo:清除登录状态并跳转至登录页面
  unset($_SESSION['current_login_user']);
  header('Location:/admin/login.php');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($message)?' shake animated':'';?>" method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>' autocomplete='off' novalidate>
      <img class="avatar" src="/static/assets/img/default.png">

      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <div class="alert alert-danger">
          <strong>ERROR！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>

      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" value="<?php echo isset($_POST['email'])?$_POST['email']:'';?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
    <script>
      $(function ($) {

        var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;

        $('#email').on('blur', function () {
          var value = $(this).val()
          if (!value || !emailFormat.test(value)) return
          $.get('/admin/api/avatar.php', {email:value}, function (res) {
            // 希望 res => 这个邮箱对应的头像地址
            if (!res) return;

            // 用户输入了一个合理的邮箱地址
            // 获取这个邮箱对应的头像地址
            // 因为客户端的 JS 无法直接操作数据库，应该通过 JS 发送 AJAX 请求 告诉服务端的某个接口，
            // 让这个接口帮助客户获取头像地址
              // 展示到上面的 img 元素上
              // $('.avatar').fadeOut().attr('src', res).fadeIn()
              $('.avatar').fadeOut(function () {
                // 等到 淡出完成
                $(this).on('load', function () {
                  // 图片完全加载成功过后
                  $(this).fadeIn()
                }).attr('src', res)
              })
          })
        })
      })
    </script>

</body>
</html>
