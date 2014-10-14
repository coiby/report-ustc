<?php echo $header;?>

<script>
$(function() {  
    $('.error').hide();  

    $("#getmsgcode").click(
			function(event) {
				$("#control-valmobile").show();
				
				var datastring="mobile="+$("input#mobile").val();
				$.ajax({
					type : "POST",
					url : "/api/user/mobilecode",
					data : datastring,
					dataType: "json",
					success : function(data) {
						//alert(result);
						if(data.status=="success"){
							 
							// waiting time
						    var resendtime = 60;
						    $("#getmsgcode").prop('disabled',true);
						    
						    var timer = setInterval(function(){
						        // If the timer is up, enable button again
						        if(resendtime <= 0){
						            clearInterval(timer);
						            $("#getmsgcode").text("发送验证码");
						            $("#getmsgcode").prop('disabled',false);
						        }
						        // Otherwise the timer should tick and display the results
						        else{
						            // Decrement the waiting time
						        	resendtime--;
						            $("#getmsgcode").text(resendtime+"秒后重新发送");      
						        }
						    },1000);
							 return false;
							 
						}else if(data.status=="error"){
			           	 	alert(data.message);
			           	 	return false;
			           	 } 
						 
					}
				});
				
				return false;
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
    
    $("input#mobile").bind("input",
    		function() {
    			var mobile = $("input#mobile").val();
				var reg = /^1\d{10}$/;
				var getmsgcode=$("#getmsgcode");
				if (reg.test(mobile)) {
					getmsgcode.show();
					getmsgcode.prop('disabled',false);
					return false;
				}else{
					getmsgcode.prop('disabled',true);
					return false;
				}
        		}
    	    );

   
	
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
					url : "/api/user/register",
					data : dataString,
					dataType: "json",
					success : function(data) {
						//alert(result);
						if(data.status=="success"){
							alert(data.message);
							$("#formreg").trigger("reset");
							window.setTimeout(function () {
								location.href = "/user/login";
							}, 2000);
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
<div class="header">
		<div class="modal-header">

			<h3>用户注册</h3>
		</div>
		<!--<div class="hero-unit">-->
		<!-- <p>-->
		<!--本系统将发布在<a href="http://bbs.ustc.edu.cn/cgi/bbstdoc?board=ESS">bbs</a>上的固体物理报告自动发布到您的邮箱，每3小时检查一次新帖子。目前处于测试期，有建议或bug请反馈至<a href="https://github.com/Coiby/report-ustc">Github</a>或邮件coiby@mail.ustc。	-->
		<!--</p>-->
		<!--</div>-->

	</div>
	 
	<div class="container" id="content">
		<div class="tab-pane active in" id="login">
						<form id="formreg" class="form-horizontal">
							<fieldset>
								<!--<div id="legend">-->
									<!--<legend class="">订阅</legend>-->
								<!--</div>-->
								<div class="control-group">
									<!-- email -->
									<label class="control-label" for="email">邮箱</label>
									<div class="controls">
										<input type="text" id="email" name="email" placeholder=""
											class="input-xlarge">
									</div>
								</div>
								
								<div class="control-group">
									<!-- mobile -->
									<label class="control-label" for="mobile">手机号</label>
									<div class="controls">
										<input type="text" id="mobile" name="mobile" placeholder=""
											class="input-xlarge"><button style="display: none" id="getmsgcode">获取验证码</button>
									</div>
									<div class="controls" id="control-valmobile" style="display: none" >
										<input type="text" id="mobile_val" name="mobile_val" placeholder="验证码"
											class="input-xlarge"> 
									</div>
								</div>

								<div class="control-group">
									<!-- Password-->
									<label class="control-label" for="password">密码</label>
									<div class="controls">
										<input type="password" id="password" name="password"
											placeholder="" class="input-xlarge">
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
										<button id="subscribe" class="btn btn-success">确定</button>
									</div>
								</div>
							</fieldset>
						</form>
		</form>
		</div>
	</div>
	
		
<?php echo $footer;?>