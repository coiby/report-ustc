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
    $(".btn").click(function() {  
    // validate and process form here  

        $('.error').hide();  
       
        var name = $("input#name").val(); 
        var pw = $("input#password").val();
        /* var username = $("input#username").val();  
            if (username == "") {  
            $("label#message_error").show();  
            $("input#username").focus();  
        return false;  
        } 
        var title = $("input#title").val();
            if(title !== "") {  
            $("input#title").focus();  
        return false;  
        }  */

    var dataString = 'name='+ name + '&pass=' + pw;  
        
    //alert (dataString);return false;  
    $.ajax({  
        type: "POST",  
        url: "/admin/onlogin",  
        data: dataString,  
        success: function(data, status) {  
        	var divcon=$('div#content');
        	divcon.html('');
        	divcon.html("<div id='response'></div>");
        	if(data=="OK"){
            	$('#response').html("<div id='content_success_block' class='shadow_box'>")
                .append("<div id='success_text'>登录成功，1秒后跳转到用户页面。</div>")
                .append("</div>")                   
            	.hide()  
            	.fadeIn(500, function() {  
                	$('#response');  
           	 }); 
            	  
                //.append("<div id='success_image'><img src='assets/misc/success.png'></div>")
            	window.setTimeout(function () {
                	location.href = "index";
            	}, 2000);
        }else{
        	$('#response').html("<div id='content_success_block' class='shadow_box'>")  
            .append("<div id='fail_text'>登录失败，请重新登录</div>")
            .append("</div>")                   
        	.hide()  
        	.fadeIn(1500, function() {  
            	$('#response');  
       	 }); 
//         	.append("<div id='success_image'><img src='assets/misc/success.png'></div>")
        	$('#response').fadeOut();
        	window.setTimeout(function () {
            	location.href = "login";
        	}, 2000);
        }
        }  
    });  
    return false; 
    });

});
</script>
 
	 
	<div class="container" id="login">
		<form class="form-signin" id="adminlogin">
		<h2 class="form-signin-heading">请登录</h2>
			<div class="control-group">
				<!-- username -->
				 
				<div class="controls">
					<input type="text" id="name" name="name" placeholder="用户名"
						class="input-xlarge">
				</div>
			</div>
			<div class="control-group">
				<!-- Password-->
				 
				<div class="controls">
					<input type="password" id="password" name="password" placeholder="密码"
						class="input-xlarge">
				</div>
			</div>
			<div class="control-group">
				<!-- Button -->
				<div class="controls">
					<button class="btn btn-success">登录</button>
					 
				</div>
			</div>

		</form>
	</div>
	
		
</body>
</html>