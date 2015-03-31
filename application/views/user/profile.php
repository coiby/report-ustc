<?php echo $header;?>

<script>
$(function() {
	 $("#update-info").click(
				function() {
					// validate and process form here  

					$('.error').hide();

					var email = $("input#email").val();
					if (!(email.indexOf('@') > 0)) {
						$("label#email2_error").show();
						$("input#email").focus();
						return false;
					}

					var dataString ="action=update-info&";
					var pw = $("input#password").val();
					var pw2 = $("input#password2").val();
					
					if (pw != pw2) {
						$("input#password2").focus();
						return false;
					}
					if (pw != "") {
						   dataString=dataString+'&password=' + pw
							+ '&password2=' + pw2;
						}
					
					var mobile = $("input#mobile").val();
					var reg = /^1\d{10}$/;
					if (!reg.test(mobile)) {
						$("input#mobile").focus();
						return false;
					}

					var dataString =dataString+ '&email=' + email
							+ '&mobile=' + mobile;
					//alert (dataString);return false;  
					$.ajax({
						type : "POST",
						url : "/api/user/update",
						data : dataString,
						dataType: "json",
						success : function(data) {
							//alert(result);
							if(data.status=="success"){
								alert(data.message);
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
				<li><a href="/user/subscribe">报告订阅</a></li>

				<li><a href="#">报告列表</a></li>

			</ul>

			<ul class="nav pull-right">
				<li class="dropdown"><a class="dropdown-toggle"
					data-toggle="dropdown" href="user/index"><?php echo $user['email'];?><b
						class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="/user/profile">个人信息</a></li>
						<li><a href="/user/logout">登出</a></li>
					</ul></li>
			</ul>

		</div>
	</div>
</div>

<div class="container" style="margin-top:60px;">
	 
	<form id="forminfo" class="form-horizontal">
		<fieldset>
			<div id="legend">
			<legend class="">账号设置</legend>
			</div>
			<div class="control-group">
				<!-- email -->
				<label class="control-label" for="email">邮箱</label>
				<div class="controls">
					<input type="text" id="email" name="email" placeholder=""
						class="input-xlarge" value="<?php echo $user['email'];?>">
				</div>
			</div>

			<div class="control-group">
				<!-- mobile -->
				<label class="control-label" for="mobile">手机号</label>
				<div class="controls">
					<input type="text" id="mobile" name="mobile" placeholder=""
						class="input-xlarge" value="<?php echo $user['mobile'];?>">
				</div>
			</div>

			<div class="control-group">
				<!-- Password-->
				<label class="control-label" for="password">密码</label>
				<div class="controls">
					<input type="password" id="password" name="password"
						placeholder="不修改请留空" class="input-xlarge">
				</div>
			</div>

			<div class="control-group">
				<!-- Password-->
				<label class="control-label" for="password2">密码确认</label>
				<div class="controls">
					<input type="password" id="password2" name="password2"
						placeholder="" class="input-xlarge">
				</div>
			</div>




			<div class="control-group">
				<!-- Button -->
				<div class="controls">
					<button id="update-info" class="btn btn-success">确定</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>

<?php echo $footer;?>