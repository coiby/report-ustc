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
			email : {
				required : true,
				email : true
			}
		},
		messages : {
			email : {
				required : "<li>请输入邮箱</li>",
				minlength : "<li>非邮件格式</li>"
			}
		}
	});
	//redirect user to mail login page
    var hash={
    		'qq.com': 'http://mail.qq.com',
    		'mail.ustc.edu.cn':'http://mail.ustc.edu.cn',
    		'ustc.edu.cn':'http://mail.ustc.edu.cn',
    		'gmail.com': 'http://mail.google.com',
    		'sina.com': 'http://mail.sina.com.cn',
    		'163.com': 'http://mail.163.com',
    		'126.com': 'http://mail.126.com',
    		'yeah.net': 'http://www.yeah.net/',
    		'sohu.com': 'http://mail.sohu.com/',
    		'tom.com': 'http://mail.tom.com/',
    		'sogou.com': 'http://mail.sogou.com/',
    		'139.com': 'http://mail.10086.cn/',
    		'hotmail.com': 'http://www.hotmail.com',
    		'live.com': 'http://login.live.com/',
    		'live.cn': 'http://login.live.cn/',
    		'live.com.cn': 'http://login.live.com.cn',
    		'189.com': 'http://webmail16.189.cn/webmail/',
    		'yahoo.com.cn': 'http://mail.cn.yahoo.com/',
    		'yahoo.cn': 'http://mail.cn.yahoo.com/',
    		'eyou.com': 'http://www.eyou.com/',
    		'21cn.com': 'http://mail.21cn.com/',
    		'188.com': 'http://www.188.com/',
    		'foxmail.coom': 'http://www.foxmail.com'
    		};
	  
    $(".btn").click(function() {  
    // validate and process form here  

        $('.error').hide();  
       
        var email = $("input#email").val(); 
              
    	var dataString = 'email='+ email;  
        
    //alert (dataString);return false;  
    $.ajax({  
        type: "POST",  
        url: "/api/user/reset_pw_code",  
        data: dataString,  
        dataType: "json",
        success: function(data) {  
        	 
        	if(data.status=="success"){
        		alert("重置链接已经发到邮箱，请登录查看");
        		var url = email.split('@')[1];
				  window.setTimeout(function () {
					location.href = hash[url];
				}, 500); 
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
		<form class="form-signin" id="formresetpw">
		<h2 class="form-signin-heading">输入要重置的账号</h2>
			<div class="control-group">
				<!-- username -->
				 
				<div class="controls">
					<input type="text" id="email" name="email" placeholder="邮箱"
						class="input-xlarge">
				</div>
			</div>
			 
			<div class="control-group">
				<!-- Button -->
				<div class="controls">
					<button class="btn btn-success">确定</button>
					 
				</div>
			</div>

		</form>
	</div>
	
</body>
</html>