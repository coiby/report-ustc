<script>

	$(function() {

		 

		$("#button1id").click(function() {
		$('.error').hide(); 
		var dataString=$("input#byemail").is(':checked')?"byemail=1":"byemail=0";
		 
		dataString+=$("input#bymsg").is(':checked')?"&bymsg=1":"&bymsg=0";
      	dataString+="&cid="+$("#cid").val();
      	
		$.ajax({
			type : "POST",
			url : "/api/user/update_subsetting",
			data : dataString,
			dataType: "json",
			success : function(data) {
				 //alert(result);
				 if(data.status=="success"){
					 $('#subscribe-dia').dialog("close");
				 
	           	 }else if(data.status=="error"){
	           	 	alert(data.message);
	           	 }
			}
		});
		return false;
		});
	});
</script>

<div class="container">
		 
		    <input type="hidden" value="<?php echo $sub['cid'];?>" name="cid" id="cid">
			<!-- <div class="control-group">
				 <div class="span10">
				<label for="title" class="control-label">报告题目</label>
				<div class="controls" >
					<input type="text" class="input-xxlarge" name="title" id="title" value="{$title}">
				</div>
			</div>
			</div> -->
		
			 
 			<div class="checkbox">
				 
				<label for="content" class="control-label">邮件通知<input type="checkbox" id="byemail" value="byemail" <?php echo ($sub['byemail']==1) ? 'checked="checked"' : '';?>>  </label>
		 
		 
			</div>

			 
			 
			<div class="checkbox">
			 
				<label for="content" class="control-label">短信通知<input type="checkbox" id="bymsg" value="bymsg" <?php echo ($sub['bymsg']==1) ? 'checked="checked"' : '';?>>  </label>
				
			</div>

			<div class="control-group">
			 
				 <label class="control-label" for="button1id"></label>
  				 <div class="controls">
   					 <button id="button1id" name="button1id" class="btn btn-success">修改设置</button>
 				 </div>
			 
			</div>
		
	</div>