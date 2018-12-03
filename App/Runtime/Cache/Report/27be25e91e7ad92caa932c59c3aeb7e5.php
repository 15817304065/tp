<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo ($title); ?></title>
<link href="/frame/tp/Public/css/bootstrap.min.css" rel="stylesheet">
<link href="/frame/tp/Public/css/bootstrap-table.css" rel="stylesheet">
<script src="/frame/tp/Public/js/jquery-1.11.1.min.js"></script>
<script src="/frame/tp/Public/js/bootstrap.min.js"></script>
<script src="/frame/tp/Public/js/bootstrap-table.js"></script>
<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<h3 style="margin-top:10px ;color:#333;text-align: center"><?php echo ($title); ?></h3>

      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading"><span style="position: relative;left: 0"><a href="<?php echo U('expUser');?>&item=3" class="btn">导出数据</a></span><span style="position: relative;left: 0"><a href="<?php echo U('index');?>" class="btn">返回接口总访问数据</a></span></div>
          <div class="panel-body">
            <table data-toggle="table" data-url="<?php echo U('getUser');?>&item=3"  data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name" data-sort-order="desc">
                <thead>
                <tr>
                   <!-- <th data-field="state" data-checkbox="true" name="id">ID</th> -->
                     <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><th data-field="<?php echo ($val); ?>" data-sortable="true"><?php echo ($val); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
                </tr>
                </thead>
            </table>
          </div>
        </div>
      </div>

      <script type="text/javascript">
         $('table').bootstrapTable({
         
            striped: true,                      //是否显示行间隔色
            cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
            pagination: true,                   //是否显示分页（*）
            sortable: true,                     //是否启用排序
            sortOrder: "desc",                   //排序方式
            sidePagination: "client",           //分页方式：client客户端分页，server服务端分页（*）
            pageNumber: 1,                       //初始化加载第一页，默认第一页
            pageSize: 30,                       //每页的记录行数（*）
            pageList: [10, 20, 30, 50],        //可供选择的每页的行数（*）

        });
         setTimeout(function(){
               $('.sortable').eq(0).click();
         },'100')
          
      </script>
</body>
</html>