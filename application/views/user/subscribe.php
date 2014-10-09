<?php echo $header;?>

<script>
$(function() {
	// 报告编辑对话框
	$('#subscribe-dia').dialog({
		autoOpen : false,
		modal : true,
		width : 260,
		height : 260,
		overlay : {
			backgroundColor : '#000',
			opacity : 0.5
		},
		 
		close : function(event, ui) {
			$('#subscribe-dia').html('');
		}
	});

	$('body').on("click",'.subscribe',function(event) {// TODO the event binding is invalid...
								// http://zhidao.baidu.com/link?url=AEli88NfFn4jPGpL0fwm0enJxZTZo5a_cNvRi-j_Dn_58P3LZY70HFQTt5C12NtG7Hbo-J3eBbaInJ1hIbgfU_
						// TODO
						// https://stackoverflow.com/questions/10371677/how-to-attach-jquery-event-handlers-for-newly-injected-html
						var id = this.value;
						var $this = $(this);
						var dataString = 'cid=' + id;
						$('#subscribe-dia').empty();
						$('#subscribe-dia').dialog({
							autoOpen : false,
							modal : true,
							width : 260,
							height : 260,
							overlay : {
								backgroundColor : '#000',
								opacity : 0.5
							},
							 position:{
								 my: 'left top',
							        at: 'right bottom',
								    of: $this 
							 },
							close : function(event, ui) {
								$('#subscribe-dia').html('');
							}
						});
						//$('#subscribe-dia').load('/api/user/sub_setting',{ sid: id });
						$.ajax({
									url : "/api/user/sub_setting",
									data : dataString,
									dataType : 'html',
									type : 'post',
									beforeSend : function() {
										$('#subscribe').html('<div><img src="/public/img/loading.gif" style="margin-left:40%"></div>');
									
									},
									success : function(data) {
										
										$('#subscribe-dia').html(data);
									}
								}); 
						
						$('#subscribe-dia').dialog('open');
					});

	$('body').on("click", 'input.modifysub[type=checkbox]', function() {
		// validate and process form here

		$('.error').hide();
		var $this = $(this);
		var url = "";
		var dataString = 'cid=' + $this.val();
	 
		if ($(this).is(':checked')) {
			url = "/api/user/subscribe";
		} else {
			url = "/api/user/unsubscribe";
		}
		// alert (dataString);return false;

		$.ajax({
			type : "POST",
			url : url,
			data : dataString,
			dataType : "json",
			success : function(data) {
				// alert(result);
				if (data.status == "success") {
					alert(data.message);
					$this.prop("checked", !$this.prop("checked"));
					$this.parent().siblings(":last").children().first().toggle();//

				} else if (data.status == "error") {
					alert(data.message);
				}
				/*
				 * $('#message_form').html("<div id='response'></div>");
				 * $('#response').html("<div id='content_success_block'
				 * class='shadow_box'>") .append("<div id='success_image'><img
				 * src='assets/misc/success.png'></div>") .append("<div
				 * id='success_text'>Thanks for contacting us! We will be in
				 * touch soon.</div>") .append("</div>") .hide() .fadeIn(2500,
				 * function() { $('#response'); });
				 */
			}
		});
		return false;
	});
});
</script>


<div id="subscribe-dia" style="display: none" title="订阅报告"></div>
<div class="navbar navbar-default navbar-fixed-top">

	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="#">学术报告订阅</a>
			<ul class="nav">
				<li class="active"><a href="#">报告订阅</a></li>

				<li><a href="#">报告列表</a></li>

			</ul>

			<ul class="nav pull-right">
				<li class="dropdown"><a class="dropdown-toggle"
					data-toggle="dropdown" href="user/index"><?php echo $user['email'];?><b
						class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="/user/index">个人信息</a></li>
						<li><a href="/user/logout">登出</a></li>
					</ul></li>
			</ul>

		</div>
	</div>
</div>
<div class="container" style="margin-top: 60px;">
	<table class="table table-hover">
		<thead>
			<tr>
				<th><!-- <input type='checkbox'> --></th>
				<th>ID</th>
				<th>名称</th>
				<th>学院</th>
				<th>介绍</th>
				<th>设置</th>
			</tr>
		</thead>
		<tbody>
		<?php
//print_r($subs);
foreach($subs as $sub){
	echo "<tr>";
	$subscribed=$sub['yes'];
	if($sub['yes']==1){
		$checked=" checked='checked'";
		$hidden="";
		 
	}else{
	  	$checked=" ";
		$hidden=" style='display:none'";
	}
	 
	echo  "<tr><td><input class='modifysub' type='checkbox'".$checked." value='".$sub['cid']."'></td>";
	
	unset($sub['yes']);
	foreach($sub as $value){
		echo "<td>".$value."</td>";
	}
	
	 
	echo "<td><button ".$hidden."class='subscribe' value='".$sub['cid']."'>修改</button></td>";
	echo "</tr>";
    
}
?>
			 
		</tbody>


	</table>
	<!-- <div class="controls">
		<button id="updatesub" class="btn btn-success">确定</button>
	</div> -->
</div>

<?php echo $footer;?>