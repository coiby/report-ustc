<p>报告人：<?php echo $report['speaker']?></p>
			<p>单位：<?php echo $report['institution']?></p>
			<p>时间：<?php echo $report['starttime']?></p>
			<p>地点：<?php echo $report['place']?></p>
			<h4>报告人介绍</h4>
			<p><?php echo nl2br($report['profile'])?></p>
			<p></p>
			<h4>报告摘要</h4>
			<p><?php echo nl2br($report['content'])?></p>