<link rel="stylesheet" type="text/css" media="screen"
	href="/public/css/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="/public/js/bootstrap-datetimepicker.js"></script>
<script>

	$(function() {
		
		$('#senttobbs').change(function() {//check http://www.tutorialrepublic.com/codelab.php?topic=faq&file=jquery-show-hide-div-using-checkboxes
		    if( this.checked ) {
		        $("#bbsaccount").show();
		        //$("#bbspass").show();
		    } else {
		    	$("#bbsaccount").hide();
		        //$("#bbspass").hide();
		    }
		}); 
		
		$(".btn").click(function() {  
		$('.error').hide();
		
		var title = $("input#title").val();  
        if (title == "") {   
        	$("input#title").focus();  
    		return false;  
    	} 
		
		var speaker = $("input#speaker").val();  
        if (speaker == "") {   
        	$("input#speaker").focus();  
    		return false;  
    	} 
        
        var insti = $("input#insti").val();  
        if (insti == "") {   
        	$("input#insti").focus();  
    		return false;  
    	} 
        
        var profile = $("textarea#profile").val();  
        if (profile == "") {   
        	$("textarea#profile").focus();  
    		return false;  
    	}
      
        var date = $("input#date").val();  
        if (date == "") {   
        	$("input#date").focus();  
    		return false;  
    	} 
        
        var begin = $("input#begin").val();  
        if (begin == "") {   
        	$("input#begin").focus();  
    		return false;  
    	} 
        
        var senttobbs=$("input#senttobbs").attr('checked');
        
        /* if(senttobbs){
        	var bbsuser=$("input#bbsuser").val();
        	var bbspass=$("input#bbspass").val();
        	if(bbsuser=""){
        		$("input#bbsuser").focus();
        		return false;  
        	}
        	if(bbspass=""){
        		$("input#bbspass").focus();
        		return false;  
        	}
        } */
        
        var end = $("input#end").val();  
        if (end == "") {   
        	$("input#end").focus();  
    		return false;  
    	} 
        
        var place = $("input#place").val();  
        if (place == "") {   
        	$("input#place").focus();  
    		return false;  
    	} 
        
        var content = $("textarea#content").val();  
        /* if (content == "") {   
        	$("textarea#content").focus();  
    		return false;  
    	}  */
        
        var dataString = 'action=sub-rep&' +'title='+title +'&speaker='+speaker+'&place='+place+'&institution='+insti +'&profile='+profile + '&date='+date +'&begin='+begin+'&end='+end+'&content='+content;
        if(senttobbs){
        	dataString =dataString+'&senttobbs=true';
        	//dataString =dataString+'&bbsuser='+bbsuser+'&bbspass='+bbspass;
        }
    	
		$.ajax({
			type : "POST",
			url : "/admin/onaddrep",
			data : dataString,
			dataType: "json",
			success : function(data) {
				//alert(result);
				if(data.status=="success"){
				 $('#add').dialog("close");
				 var trhtml="<tr id='"+data.id+"'><td><span>";
				 //<td class='cktd'><input class='ckfile' value='' type='checkbox'><span>状态</span></td>
				 trhtml=trhtml+"<a href=''>"+title+ "</a></span></td><td><span>";
				 trhtml=trhtml+date+" "+begin+"</span></td><td><span>";
				 trhtml=trhtml+place+"</span></td><td><span>";
				 trhtml=trhtml+speaker+"</span></td>";
				 trhtml=trhtml+"<td><span><a target='_blank' href='"+data.href+"'>BBS</a></span></td>";
				 trhtml=trhtml+"<td><button title='编辑' class='button edit' value='"+data.id+"'><span>编辑</span></button><!--<button title='分享' class='btn'><span>分享</span></button>--></td></tr>";
				 
				 $(trhtml).insertAfter($('#rep-list tr:last')).hide().fadeIn(500);
				 /* $('#rep-list tr:last').after(trhtml)                
	            	.hide()  
	            	.fadeIn(500, function() {  
	                	$('#rep-'+result);  
	           	 });  */
				}
			}
		});
		return false; 
		});
	});
</script>

</head>
<body>
	 
	<div class="container">
		<form class="form-horizontal">
		
			<div class="control-group">
				 <div class="span10">
				<label for="title" class="control-label">报告题目</label>
				<div class="controls" >
					<input type="text" class="input-xxlarge" name="title" id="title">
				</div>
			</div>
			</div>
			
			<div class="control-group">
				 <div class="span4">
				<label for="speaker" class="control-label">报告人</label>
				<div class="controls" >
					<input type="text" name="speaker" id="speaker" class="input-large">
				</div>
				</div>
				<div class="span4">
				<label for="insti" class="control-label">单位</label>
				<div class="controls" >
					<input type="text" name="insti" id="insti" class="input-xlarge">
				</div>
				</div>
			
			</div>

			<div class="control-group">
				<div class="span10">
				<label for="profile" class="control-label ">报告人介绍</label>
				<div class="controls" >
					<textarea name="profile" id="profile" class="span4"></textarea>
				</div>
				</div>
			</div>

			<div class="control-group">
				<div class="span10">
				<label for="date" class="control-label">报告日期</label>
				<div class="controls date form_date" data-date-format="yyyy-mm-dd" >
					<input type="text" name="date" id="date">
					<span class="add-on"><i class="icon-remove"></i></span>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>
			</div>
			 
			<div class="control-group">
				<div class="span5">
				<label for="begin" class="control-label">开始时间</label>
				
				<div class="controls date form_time" data-date-format="hh:ii">
					<input type="text" name="begin" id="begin">
					<span class="add-on"><i class="icon-remove"></i></span>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
				</div>
				
				<div class="span5">
					<label for="end" class="control-label ">结束时间</label>
				<div class="controls date form_time" data-date-format="hh:ii">
					<input type="text" name="end" id="end">
					<span class="add-on"><i class="icon-remove"></i></span>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>

				</div>
			    
			</div>
			
		
			<div class="control-group">
				 <div class="span10">
				<label for="place" class="control-label">地点</label>
				<div class="controls" >
					<input type="text" class="input-xxlarge" name="place" id="place">
				</div>
			</div>
			</div>
			
			<div class="control-group">
				<div class="span10">
				<label for="content" class="control-label">报告内容</label>
				<div class="controls">
					<textarea name="content" id="content" cols="70" rows="12" class="span8" placeholder="请填写报告内容" ></textarea>
				</div>
			</div>
			</div>

			<!-- <div class="control-group">
				<div class="span2">
					<label for="senttobbs" class="control-label">发送到BBS</label>
					<div class="controls">
						<input type="checkbox" name="senttobbs" id="senttobbs" checked="checked">
					</div>
				</div>
				
				 <div id="bbsaccount">
				<div class="span3">
					<label for="bbsuser" class="control-label">BBS账号</label>
					<div class="controls">
						<input class="input-small" type="text" name="bbsuser" id="bbsuser">
					</div>
				</div>

				<div class="span3">
					<label for="bbspass" class="control-label">BBS密码</label>
					<div class="controls">
						<input type="text" class="input-medium" name="bbspass" id="bbspass">
					</div>
				</div>
				</div> 
			</div>-->

			<div class="control-group">
				<div class="span10">
				 <label class="control-label" for="button1id"></label>
  				 <div class="controls">
   					 <button id="button1id" name="button1id" class="btn btn-success">提交报告</button>
 				 </div>
			 </div>
			</div> 
		</form>
	</div>
	<script>
$('.form_date').datetimepicker({
		 startView: 2,
		 minView: 2
		});
$('.form_time').datetimepicker({
	 startView: 1,
	 minView: 0,
	 maxView:1,
	 formatViewType:"time",
	 minuteStep: 10
	});

</script>
</body>
 