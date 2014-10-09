 <?php echo $header;?>

<script>
$(function() {
	//报告新增对话框
    $('#add').dialog({
        autoOpen: false,
        modal: true,
        width: 1060,
        height: 870,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        show: {effect: 'scale',duration: 650},
        position: {my: "center", at: "center"},
        close: function(event, ui) {
            $('#add').html('');
        }
    });

    //报告编辑对话框
    $('#edit').dialog({
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
            $('#edit').html('');
        }
    });
    
    $('#newrep').button({
        icons: {primary: "ui-icon-circle-plus"}
    }).click(function() {
        $('#add').load("/admin/addrep").dialog('open');
    });
    //click a title, a dialog pop up
    $('.title').click(function() {
        $('#add').load("/admin/addrep").dialog('open');
    });

    $('body').on("click",'.edit',function(){//TODO the event binding is invalid... http://zhidao.baidu.com/link?url=AEli88NfFn4jPGpL0fwm0enJxZTZo5a_cNvRi-j_Dn_58P3LZY70HFQTt5C12NtG7Hbo-J3eBbaInJ1hIbgfU_
		//TODO https://stackoverflow.com/questions/10371677/how-to-attach-jquery-event-handlers-for-newly-injected-html
		var id=this.value;
		var dataString = 'id='+id;
		$('#edit').empty();
		$.ajax({
			url:"/admin/editrep",
			data:dataString,
			dataType:'html',
			type:'post',
			beforeSend:function(){
				$('#edit').html('<div><img src="/public/img/loading.gif" style="margin-left:40%"></div>');
			},
			success:function(data){
				$('#edit').html(data);
			}
		});
		$('#edit').dialog('open');
	});
	/*
    $('.edit').button({
		text:false,
		icons:{primary:'ui-icon-pencil'},
		create:function(event,ui){
			$('body').on("click",'.edit',function(){//TODO the event binding is invalid... http://zhidao.baidu.com/link?url=AEli88NfFn4jPGpL0fwm0enJxZTZo5a_cNvRi-j_Dn_58P3LZY70HFQTt5C12NtG7Hbo-J3eBbaInJ1hIbgfU_
				//TODO https://stackoverflow.com/questions/10371677/how-to-attach-jquery-event-handlers-for-newly-injected-html
				var id=this.value;
				var dataString = 'action=edit-rep&id='+id;
				$('#edit').empty();
				$.ajax({
					url:"ajax.php",
					data:dataString,
					dataType:'html',
					type:'post',
					beforeSend:function(){
						$('#edit').html('<div><img src="<?php echo ""?>/static/img/loading.gif" style="margin-left:40%"></div>');
					},
					success:function(data){
						$('#edit').html(data);
					}
				});
				$('#edit').dialog('open');
			});
		}
	})
    */
    //$(".ui-widget-overlay").attr('style','background-color: #000; opacity:1; z-index:1000;');
});
</script>
</head>
<body>
	<div id="add" style="display: none" title="新增报告"></div>
	<div id="edit" style="display: none" title="编辑报告"></div>
	<div class="btn-group">
		<button class="btn" id="newrep">新建报告</button>
		<!-- <button class="btn">Middle</button>
		<button class="btn">Right</button> -->
	</div>
	<table class="table table-hover" id="rep-list">
		<thead>
			<tr>
				<!-- <th id="headerCourse"><input value="" id="checkAll" type="checkbox">状态
				</th> -->
				<th id="headerComm"><span>题目</span></th>
				<th><span class="name">报告时间</span></th>
				<th id="headerPlace"><span>地点</span></th>
				<th id="headerSpeaker"><span>报告人</span></th>
				<th id="headerBBs"><span>BBS链接</span></th>
			</tr>
		</thead>
		<tbody>
<?php
foreach ($reports as $report) { 
?>
			<tr id="<?php echo $report['id']?>">
				<!-- <td class="cktd"><input type="checkbox" class="ckfile" value=""><span>状态</span></td> -->

				<td><span><?php echo $report['title']?></span></td>
				<td><span><?php echo $report['starttime']?></span></td>
				<td><span><?php echo $report['place']?></span></td>
				<td><span><?php echo $report['speaker']?></span></td>
				<td><span><a target="_blank" href="<?php echo $report['bbslink']; ?>">BBS</a></span></td>
				<td>
					<button title="编辑" class="button edit"
						value="<?php echo $report['id'];?>">
						<span>编辑</span>
					</button>
					<!-- <button title="分享" class="btn">
						<span>分享</span>
					</button> -->
				</td>

			</tr>
<?php }?>
		</tbody>
	</table>
<?php echo $footer;?>
