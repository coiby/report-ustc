<?php echo $header;?>

<div class="navbar navbar-default navbar-fixed-top">

	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="#">学术报告订阅</a>
			<ul class="nav">
				<li><a href="/index">首页</a></li>

				<li><a href="/report">报告</a></li>

				<li class="active"><a href="#">关于</a></li>
			</ul>
		<?php if(empty($user)){?>
		<ul class="nav pull-right">

				<li class="divider-vertical"></li>
				<li><a href="user/login">登录</a></li>

				<li><a href="user/register">注册</a></li>
			</ul>
		<?php }else{ ?>
		<ul class="nav pull-right">
				<li class="dropdown"><a class="dropdown-toggle"
					data-toggle="dropdown" href="user/index"><?php echo $user['email'];?><b
						class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="user/index">用户面板</a></li>
						<li><a href="user/logout">登出</a></li>
					</ul></li>
			</ul>
		<?php } ?>
		</div>
	</div>
</div>

<div class="container" style="margin-top: 60px;">
	<h2>项目初衷</h2>
</div>
<div class="container">
	<h2>如何帮助项目</h2>
	<div class="span2">
		<h3>普通用户</h3>
		<ul>
			<li>使用系统，提交bug；</li>
			<li>提出功能和改进需求；</li>
			<li>联系学院报告发布老师；</li>
		</ul>
	</div>
	<div class="span2">
		<h3>开发者</h3>
		<ul>
			<li>参与开发；</li>
			<li>设计UI；</li>
		</ul>
	</div>
</div>

<div class="container">
	<h2>项目技术</h2>
	<p>前台：HTML+Javascript+CSS（框架Bootstrap）；</p>
	<p>后台：PHP（框架CodeIgniter）</p>
</div>

<div class="container">
繁琐的（正则匹配，提取时间、地点？）
</div>

<div class="container">
	<h2>许可协议</h2>
	<p>
		代码在GPLv3协议下发布<a href="https://github.com/Coiby/report-ustc"
			target='_blank'>Github</a>上。
	</p>
</div>

<div class="container">
	<h2>致谢</h2>
	<p>谢谢蔡键师兄和张超，尤其是蔡键实师兄，一直持续跟进本项目，申请域名、帮助测试以及提出修改建议。</p>
</div>


<?php echo $footer;?>