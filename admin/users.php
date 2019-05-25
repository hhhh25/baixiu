<?php
require_once '../functions.php';
xiu_get_current_user();

//用户状态转化为中文
function convert_status($status){
  switch($status){
    case 'unactivated' :return '未激活';
    case 'activated' :return '已激活';
    case 'forbidden' :return '禁止';
    case 'trashed' :return '回收站';
    default:return '未知';
  }
}

//todo:添加
function add_post(){

  if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname'])) {
      $GLOBALS['message'] = '请完整填写表单！';
      $GLOBALS['success'] = false;
      return;
      }

   $sql=sprintf("insert into users values (null, '%s', '%s', '%s', '%s', null, null, 'unactivated')",
              $_POST['slug'],
              $_POST['email'],
              $_POST['password'],
              $_POST['nickname']
            );
    $rows=xiu_execute($sql);
    $GLOBALS['success']=$rows>0;
    $GLOBALS['message']=$rows<=0?'添加失败':'添加成功';
}

//todo:编辑
function edit_post(){
   global $current_user;

    $id=$current_user['id'];
    $email=empty($_POST['email'])?$current_user['email']:$_POST['email'];
    $slug=empty($_POST['slug'])?$current_user['slug']:$_POST['slug'];
    $nickname=empty($_POST['nickname'])?$current_user['nickname']:$_POST['nickname'];
    $password=empty($_POST['password'])?$current_user['password']:$_POST['password'];

    $sql = sprintf("update users set slug = '%s', email = '%s', nickname = '%s' where id = %d",
          $_POST['slug'],
          $_POST['email'],
          $_POST['nickname'],
          $_GET['id']
        );
     $rows1 = xiu_execute($sql);

     $GLOBALS['success'] = $rows1 > 0;
     $GLOBALS['message'] = $rows1 <= 0 ? '修改失败！' : '修改成功！';
}

//todo:判断是添加还是编辑
if(empty($_GET['id'])){
if($_SERVER['REQUEST_METHOD']==='POST'){
        add_post();
    }
}
else{
  $current_user=xiu_fetch_one('select * from users where id='.$_GET['id']);
  var_dump ($current_user);
  if($_SERVER['REQUEST_METHOD']==='POST'){
        edit_post();
    }
  }

//获取用户数据渲染到页面
$users=xiu_fetch_all('select * from users;');
//var_dump ($users);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <div class="row">
      <div class="col-md-8">
                <div class="page-action">
                  <!-- show when multiple checked -->
                  <a id='btn_delete' class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
                </div>
              </div>
         <div class="col-md-7">
                  <div class="page-action">
                    <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
                  </div>
                  <table class="table table-striped table-bordered table-hover">
                    <thead>
                       <tr>
                        <th class="text-center" width="40"><input type="checkbox"></th>
                        <th class="text-center" width="80">头像</th>
                        <th>邮箱</th>
                        <th>别名</th>
                        <th>昵称</th>
                        <th>状态</th>
                        <th class="text-center" width="100">操作</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach($users as $item):?>
                       <tr>
                           <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>"></td>
                           <td class="text-center"><img class="avatar" src="<?php echo empty($item['avatar']) ? '/static/assets/img/default.png' : $item['avatar']; ?>"></td>
                           <td><?php echo $item['email']; ?></td>
                           <td><?php echo $item['slug']; ?></td>
                           <td><?php echo $item['nickname']; ?></td>
                           <td><?php echo convert_status($item['status']);?></td>
                           <td class="text-center">
                             <a href="/admin/users.php?id=<?php echo $item['id'];?>" class="btn btn-default btn-xs">编辑</a>
                             <a href="/admin/user-delete.php?id=<?php echo $item['id'];?>" class="btn btn-danger btn-xs">删除</a>
                           </td>
                        </tr>
                    <?php endForeach?>
                    </tbody>
                  </table>
                </div>

        <div class="col-md-4">
          <?php if(isset($current_user)):?>
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $current_user['id'];?>" style="border:1px solid silver; padding:20px" autocomplete="off">
              <h2>编辑"<?php echo $current_user['nickname'];?>"</h2>
              <div class="form-group">
                  <label for="email">邮箱</label>
                  <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php echo $current_user['email'];?>">
              </div>
              <div class="form-group">
                  <label for="slug">别名</label>
                  <input id="slug" class="form-control" name="slug" type="text" placeholder="slug"  value="<?php echo $current_user['slug'];?>">
                  <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                  <label for="nickname">昵称</label>
                  <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称"  value="<?php echo $current_user['nickname'];?>">
              </div>
              <div class="form-group">
                  <label for="password">密码</label>
                  <input id="password" class="form-control" name="password" type="password" placeholder="密码"  value="<?php echo $current_user['password'];?>">
              </div>
              <div class="form-group">
                  <button class="btn btn-primary" type="submit">修改</button>
              </div>
          </form>
          <?php else:?>

          <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" style="border:1px solid silver; padding:20px" autocomplete="off">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="password" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="button">取消</button>
            </div>
          </form>
          <?php endif?>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
  $(function($){
  //TODO:批量删除
   var allChecked=[];
        $("td>input").on('change',function(){

            var id=$(this).data('id');
            if($(this).prop('checked')){
                allChecked.push(id);
            }else{
                allChecked.splice(allChecked.indexOf(id),1);
            }
//            console.log(allChecked);
            allChecked.length>0?$('#btn_delete').fadeIn():$('#btn_delete').fadeOut();
            $('#btn_delete').attr('href','/admin/user-delete.php?id='+allChecked);
        })
   //TODO:全选、全不选
   $('th>input').on('change',function(){
        var checked=$(this).prop('checked');
         $('td>input').prop('checked',checked).trigger('change');
   })
 })


  </script>
  <script>NProgress.done()</script>
</body>
</html>
