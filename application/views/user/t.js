$(function() {
	// 报告编辑对话框
	$('#subscribe').dialog({
		autoOpen : false,
		modal : true,
		width : 560,
		height : 560,
		overlay : {
			backgroundColor : '#000',
			opacity : 0.5
		},
		position : {
			my : "center",
			at : "center"
		},
		close : function(event, ui) {
			$('#subscribe').html('');
		}
	});

	$('body').on("click",'.subscribe',function() {// TODO the event binding is invalid...
								// http://zhidao.baidu.com/link?url=AEli88NfFn4jPGpL0fwm0enJxZTZo5a_cNvRi-j_Dn_58P3LZY70HFQTt5C12NtG7Hbo-J3eBbaInJ1hIbgfU_
						// TODO
						// https://stackoverflow.com/questions/10371677/how-to-attach-jquery-event-handlers-for-newly-injected-html
						var id = this.value;
						var dataString = 'sid=' + id;
						$('#subscribe').empty();
						$.ajax({
									url : "/api/user/sub_setting",
									data : dataString,
									dataType : 'html',
									type : 'post',
									beforeSend : function() {
										$('#subscribe')
												.html(
														'<div><img src="/public/img/loading.gif" style="margin-left:40%"></div>');
									},
									success : function(data) {
										$('#subscribe').html(data);
									}
								});
						$('#subscribe').dialog('open');
					});

	$('body').on("click", 'input[type=checkbox]', function() {
		// validate and process form here

		$('.error').hide();
		var $this = $(this);
		var url = "";
		var dataString = 'sid=' + $(this).val();
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
					$this.parent().siblings(":last").toggle();

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