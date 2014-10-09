<?php echo $header;?>

<style>
.item {
	border: 1px solid;  
	border-color:silver;
	padding: 10px;
	height: 430px;
	margin-top: 20px;
	cursor: pointer;
	-webkit-transition: width 2s; /* For Safari 3.1 to 6.0 */
	transition: background-color 2s;
}

.item:hover {
	box-shadow: 0px 1px 8px rgba(200, 200, 200, .6);
	background-color: #fff;
	border-color: #c8c8c8;
}

.ui-dialog {
	z-index: 9000 !important;
}
</style>
<script>
$(function() {
	 
	
  
    $('body').on("click",'.item',function(event){//TODO the event binding is invalid... http://zhidao.baidu.com/link?url=AEli88NfFn4jPGpL0fwm0enJxZTZo5a_cNvRi-j_Dn_58P3LZY70HFQTt5C12NtG7Hbo-J3eBbaInJ1hIbgfU_
		//TODO https://stackoverflow.com/questions/10371677/how-to-attach-jquery-event-handlers-for-newly-injected-html
		var id=this.id;
		var $this=$(this);
		var title= this.title;
		var dataString = 'id='+id;
		$('#viewrep').empty();
		//报告编辑对话框
	    $('#viewrep').dialog({
	        autoOpen: false,
	        modal: true,
	        width: 1000,
	        height: 870,
	        title:title,
	        overlay: {
	            backgroundColor: '#000',
	            opacity: 0.5
	        },
	        show: { effect: "explode", duration: 500 },
	        position:{
				 my: 'center top+100',
			     at: 'bottom center'
			 },
	        close: function(event, ui) {
	            $('#viewrep').html('');
	        },
	        open: function() {
	            $('.ui-widget-overlay').bind('click', function() {
	                $('#viewrep').dialog('close');
	            })
	        }
	    });
	     
		$.ajax({
			url:"/api/report/view",
			data:dataString,
			dataType:'html',
			type:'post',
			beforeSend:function(){
				$('#viewrep').html('<div><img src="/public/img/loading.gif" style="margin-left:40%"></div>');
			},
			success:function(data){
				$('#viewrep').html(data);
			}
		});
		//$(".ui-dialog-titlebar").hide();
		$('#viewrep').dialog('open');
	});
});
</script>

<div class="navbar navbar-default navbar-fixed-top">

	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="#">学术报告订阅</a>
			<ul class="nav">
				<li><a href="/">首页</a></li>

				<li class="active"><a href="#">报告列表</a></li>
				<li><a href="/about">关于</a></li>
			</ul>

			<?php if(empty($user)){?>
		<ul class="nav pull-right">

				<li class="divider-vertical"></li>
				<li><a href="/user/login">登录</a></li>

				<li><a href="/user/register">注册</a></li>
			</ul>
		<?php }else{ ?>
		<ul class="nav pull-right">
				<li class="dropdown"><a class="dropdown-toggle"
					data-toggle="dropdown" href="/user/index"><?php echo $user['email'];?><b
						class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="/user/index">用户面板</a></li>
						<li><a href="/user/logout">登出</a></li>
					</ul></li>
			</ul>
		<?php } ?>

		</div>
	</div>
</div>
<div id="viewrep" style="display: none" title="查看报告"></div>
<div class="container" style="margin-top: 60px;">
 <?php
foreach ($reports as $report) { 
?>
		<div class="span3 item" id="<?php echo $report['id']?>" title="<?php echo $report['title']?>">
		<h4 > <?php echo $report['title']?> </h4>
		<p>报告人：<?php echo $report['speaker']?></p>
		<p>单位：<?php echo $report['institution']?></p>
		<p>时间：<?php echo $report['starttime']?></p>
		<p>地点：<?php echo $report['place']?></p>
		 
		<h5>报告摘要</h5>
		<p><?php echo mb_substr($report['content'], 0,100)."..."?></p>

	</div>
		<?php }?>
		
</div>

<?php echo $footer;?>