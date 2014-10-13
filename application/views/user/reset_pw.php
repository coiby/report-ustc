<?php echo $header;?>
<style>
#login {
    padding-top: 40px;
    padding-bottom: 40px;
}
#login .form-signin .form-signin-heading, #login .form-signin .checkbox {
    margin-bottom: 10px;
}
#login .form-signin {
    max-width: 300px;
    padding: 19px 29px 29px;
    margin: 0px auto 20px;
    background-color: #FFF;
    border: 1px solid #E5E5E5;
    border-radius: 5px;
    box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
}
body{
	background-color: #f5f5f5;
}
</style>
<script>
$(function() {  
    $('.error').hide();
    $('#formresetpw').validate({
		rules : {
			password:{
				required : true
			},
			password2:{
				required : true,
				equalTo: "#password"
			}
		},
		messages : {
			password2:{
				required : "请再输入密码",
				equalTo: "两遍密码不一致"
			}
		}
	});
	  
    $(".btn").click(function() {  
    // validate and process form here  

        $('.error').hide();  
       
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
              
    	var dataString = 'password=' + pw
		+ '&password2=' + pw2;;  
        
       
    $.ajax({  
        type: "POST",  
        url: "/api/user/reset_pw",  
        data: dataString,  
        dataType: "json",
        success: function(data) {  
        	 
        	if(data.status=="success"){
				alert("重置密码成功，请重新登录");
				$("#formreg").trigger("reset");
				window.setTimeout(function () {
					location.href = "/user/login";
				}, 1000);
			}else if(data.status=="error"){
           	 	alert(data.message);
           	 }
        }  
    });  
    return false; 
    });

});
</script>
 
	 
	<div class="container" id="login">
	<?php if($valid){?>
		<form class="form-signin" id="formresetpw">
		<h2 class="form-signin-heading">输入要重置的密码</h2>
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
					<button class="btn btn-success">确定</button>
					 
				</div>
			</div>

		</form>
	<?php }else{?>
	<p>抱歉，用来重置密码的验证码不存在或已经失效！</p>
	<?php }?>
	</div>
	
</body>
</html>