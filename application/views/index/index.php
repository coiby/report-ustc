<?php echo $header;?>

<style>
select {
	margin-bottom: 0px;
}
</style>
<script>
$(function() {  
    $('.error').hide();  
    
  //报告编辑对话框
    $('#viewrep').dialog({
        autoOpen: false,
        modal: true,
        width: 1060,
        height: 870,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        position: {my: "center", at: "center"},
        close: function(event, ui) {
            $('#viewrep').html('');
        }
    });
  
    $('body').on("click",'.viewd',function(){//TODO the event binding is invalid... http://zhidao.baidu.com/link?url=AEli88NfFn4jPGpL0fwm0enJxZTZo5a_cNvRi-j_Dn_58P3LZY70HFQTt5C12NtG7Hbo-J3eBbaInJ1hIbgfU_
		//TODO https://stackoverflow.com/questions/10371677/how-to-attach-jquery-event-handlers-for-newly-injected-html
		var id=this.value;
		var dataString = 'action=view-rep&id='+id;
		$('#viewrep').empty();
		$.ajax({
			url:"ajax.php",
			data:dataString,
			dataType:'html',
			type:'post',
			beforeSend:function(){
				$('#viewrep').html('<div><img src="../img/loading.gif" style="margin-left:40%"></div>');
			},
			success:function(data){
				$('#viewrep').html(data);
			}
		});
		$('#viewrep').dialog('open');
	});
  
    $('#formreg').validate({
			rules : {
				email : {
					required : true,
					email : true
				},
				mobile:{
					required : true,
					digits:true,
					rangelength:[11,11]
				},
				password:{
					required : true
				},
				password2:{
					required : true,
					equalTo: "#password"
				}
			},
			messages : {
				email : {
					required : "<li>请输入邮箱</li>",
					minlength : "<li>非邮件格式</li>"
				},
				mobile:{
					required : "<li>请输入手机号</li>",
					digits : "请输入数字",
					rangelength:"手机为11位"
				},
				password2:{
					required : "请再输入密码",
					equalTo: "两遍密码不一致"
				}
			}
		});
    
    $('#formuns').validate({
		rules : {
			email : {
				required : true,
				email : true
			},
			 
			password:{
				required : true
			}
		},
		messages : {
			email : {
				required : "<li>请输入邮箱</li>",
				minlength : "<li>非邮件格式</li>"
			},
			password:{
				required : "请再输入密码"
			}
		}
	});
    
    
    $("#unsub").click(
			function() {
				// validate and process form here  

				$('.error').hide();

				var email = $("input#unemail").val();
				if (!(email.indexOf('@') > 0)) {
					$("label#email2_error").show();
					$("input#unemail").focus();
					return false;
				}

				var pw = $("input#unpassword").val();
				 
				if (pw == "") {
					$("input#unpassword").focus();
					return false;
				}
				 

				var dataString = 'action=unsubcribe&email='+email+'&password='+pw;
				//alert (dataString);return false;  
				$.ajax({
					type : "POST",
					url : "ajax.php",
					data : dataString,
					success : function(result) {
						alert(result);
						$("#formuns").trigger("reset");
						/* $('#message_form').html("<div id='response'></div>");  
						$('#response').html("<div id='content_success_block' class='shadow_box'>")  
						    .append("<div id='success_image'><img src='assets/misc/success.png'></div>")
						    .append("<div id='success_text'>Thanks for contacting us! We will be in touch soon.</div>")
						    .append("</div>")                   
						.hide()  
						.fadeIn(2500, function() {  
						    $('#response');  
						});  */
					}
				});
				return false;
			});
    
    
    $("#subscribe").click(
			function() {
				// validate and process form here  

				$('.error').hide();

				var email = $("input#email").val();
				if (!(email.indexOf('@') > 0)) {
					$("label#email2_error").show();
					$("input#email").focus();
					return false;
				}

				var pw = $("input#password").val();
				var pw2 = $("input#password2").val();
				if (pw == "") {
					$("input#password").focus();
					return false;
				}
				if (pw != pw2) {
					$("input#password2").focus();
					return false;
				}
				var mobile = $("input#mobile").val();
				var reg = /^1\d{10}$/;
				if (!reg.test(mobile)) {
					$("input#mobile").focus();
					return false;
				}

				var dataString = 'action=register&email=' + email
						+ '&mobile=' + mobile + '&password=' + pw
						+ '&password2=' + pw2;
				//alert (dataString);return false;  
				$.ajax({
					type : "POST",
					url : "ajax.php",
					data : dataString,
					dataType: "json",
					success : function(data) {
						//alert(result);
						if(data.status=="success"){
							alert(data.message);
							$("#formreg").trigger("reset");
						}else if(data.status=="error"){
			           	 	alert(data.message);
			           	 }
						/* $('#message_form').html("<div id='response'></div>");  
						$('#response').html("<div id='content_success_block' class='shadow_box'>")  
						    .append("<div id='success_image'><img src='assets/misc/success.png'></div>")
						    .append("<div id='success_text'>Thanks for contacting us! We will be in touch soon.</div>")
						    .append("</div>")                   
						.hide()  
						.fadeIn(2500, function() {  
						    $('#response');  
						});  */
					}
				});
				return false;
			});
		

	});
</script>

<div class="navbar navbar-default navbar-fixed-top">

	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="#">学术报告订阅</a>
			<ul class="nav">
				<li class="active"><a href="#">首页</a></li>

				<li><a href="/report">报告</a></li>

				<li><a href="/about">关于</a></li>
			</ul>
		<?php if(empty($user)){?>
		<ul class="nav pull-right">

				<li class="divider-vertical"></li>
				<li><a href="user/login">登录</a></li>

				<li><a href="user/register">注册</a></li>
			</ul>
		<?php }else{ ?>
		<ul class="nav pull-right">
			<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="user/index"><?php echo $user['email'];?><b class="caret"></b></a>
		    <ul class="dropdown-menu" role="menu">
				<li><a  href="user/index">用户面板</a></li>
				<li><a  href="user/logout">登出</a></li>
			</ul>
			</li>
		</ul>
		<?php } ?>
		</div>
	</div>
</div>

<div class="container">

	<!-- .carousel -->

	<div class="header">
		<div class="modal-header">

			<h3>固物讲座报告通知订阅</h3>
		</div>
		<div class="hero-unit">

			<p>
				本系统将学校的学术报告以邮件或短信形式自动通知您，评论报告请点击相应的BBS链接。目前处于测试期，有建议或bug请反馈至<a
					href="https://github.com/Coiby/report-ustc">Github</a>或邮件coiby@mail.ustc。
			</p>
		</div>


	</div>
</div>

<div class="container">
	 

	<div id="viewrep" style="display: none" title="查看报告"></div>
	<h3>本周报告</h3>
	<table class="table table-hover" id="rep-list">
		<thead>
			<tr>
				<!-- <th id="headerCourse"><input value="" id="checkAll" type="checkbox">状态
				</th> -->
				<th id="headerComm"><span>题目</span></th>
				<th><span class="name">报告时间</span></th>
				<th id="headerPlace"><span>地点</span></th>
				<th id="headerSpeaker"><span>报告人</span></th>
				<th id="headerBBs"><span>链接</span></th>
				<th id="headerBBs"><!--<select name="car" id="Cars"
					style="padding-bottom: 4px;" onchange="myFunction(this)">
						<option value="0">Toyota</option>
						<option value="1">Audi</option>
						<option value="2">Suzuki</option>
				</select>--></th>
			</tr>
		</thead>
		<tbody>
		<?php
foreach ($reports as $report) { 
?>
			<tr id="<?php echo $report['id']?>">
				<!-- <td class="cktd"><input type="checkbox" class="ckfile" value=""><span>状态</span></td> -->

				<td><span><a href=""><?php echo $report['title']?></a></span></td>
				<td><span><?php echo $report['starttime']?></span></td>
				<td><span><?php echo $report['place']?></span></td>
				<td><span><?php echo $report['speaker']?></span></td>
				<td><span><a target="_blank"
						href="<?php echo $report['bbslink']; ?>">来源</a></span></td>
				<td>
					<button title="查看详情" class="button viewd"
						value="<?php echo $report['id'];?>">
						<span>详情</span>
					</button> <!-- <button title="分享" class="btn">
						<span>分享</span>
					</button> -->
				</td>

			</tr>
<?php }?>
		</tbody>
	</table>
</div>


<?php echo $footer;?>