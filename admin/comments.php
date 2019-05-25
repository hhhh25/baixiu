<?php

require_once '../functions.php';

xiu_get_current_user();

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>

        <ul class="pagination pagination-sm pull-right">

        </ul>

      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

 <?php $current_page = 'comments'; ?>
   <?php include 'inc/sidebar.php'; ?>

   <script src="/static/assets/vendors/jquery/jquery.js"></script>
   <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
   <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
   <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
   <script id="comment_tmpl" type="text/x-jsrender">
     {{for comments}}
     <tr class="{{: status === 'held' ? 'warning' : status === 'rejected' ? 'danger' : '' }}" data-id="{{: id }}">
       <td class="text-center"><input type="checkbox"></td>
       <td>{{: author }}</td>
       <td>{{: content }}</td>
       <td>《{{: post_title }}》</td>
       <td>{{: created}}</td>
       <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许' }}</td>
       <td class="text-center">
         {{if status === 'held'}}
         <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
         <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
         {{/if}}
         <a class="btn btn-danger btn-xs btn-delete" href="javascript:;">删除</a>
       </td>
     </tr>
     {{/for}}

   </script>
   <script>
     $(function () {
       // 页面加载完成过后，发送异步请求获取评论数据
       var currentPage=1;

       function pageData(page){
        $('tbody').fadeOut();
        $.get('/admin/api/comment_list.php', {page:page}, function (data) {
        if(page>data.total_pages){
            pageData(data.total_pages);
            return;
        }
        //第一次回调没有初始化化分页组件
        //第二次调用不会重新渲染分页组件
        //先destroy 再重新渲染
        $('.pagination').twbsPagination('destroy')
        $('.pagination').twbsPagination({
                   first:'第一页',
                   last:'最后一页',
                   prev:'上一页',
                   next:'下一页',
                   startPage:page,
                   totalPages:data.total_pages,
                   visiablePages:5,
                   initiateStartPageClick:false,
                   onPageClick: function (e, page) {
                   //点击分页页码会执行这里
                    pageData(page);
                   }
               })
//          console.log(data);
          //渲染数据
          var html = $('#comment_tmpl').render({comments:data.comments})
          $('tbody').html(html).fadeIn();
            currentPage=page;
         })
       }
       pageData(currentPage);

       //删除：
       //======================
       //由于删除按钮是动态添加的 --->委托事件
        $('tbody').on('click','.btn-delete',function(){
        //todo:确定删除单条数据的时机
//        console.log(111);

           //todo：1.拿到需要删除id
           var $tr=$(this).parent().parent();
           var id=$tr.data('id');
           //todo:2.发送AJAX请求 告诉服务器具体删除那一条数据
           $.get('/admin/api/comment-delete.php',{id:id},function(res){
           //todo:3.根据服务器返回的删除数据是否成功决定是否在界面上移除这个元素
//           console.log(typeof res);//boolean
           if(!res) return;
           //todo:4.重新载入当前页数据
           pageData(currentPage);
//            $tr.remove();
           })
        })
     })
   </script>
   <script>NProgress.done()</script>
 </body>
 </html>
