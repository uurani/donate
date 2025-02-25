<!DOCTYPE html>
<html>
	<head>
		<?php
		// 在文件开头就引入数据库连接
		require_once 'config/db.php';
		?>
		<meta charset="utf-8">
		<title>赞赏我</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
		<link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="./style/css/style.css" type="text/css"/>
		<script src="./style/js/jquery-3.2.1.min.js"></script>
		<script src="./style/js/layer/layer.js"></script>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row main_top">
			</div>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="main">
						<div class="row user">
							<div class="col-md-3 user_logo">
								<img src="https://q1.qlogo.cn/g?b=qq&nk=75070460&s=640" alt="头像" />
							</div>
							<div class="col-md-9 profile">
								<h3>赞助我</h3>
								<p>小时候很穷但是我很快乐，可是长大后我虽然总笑，可是我心里却空了</p>
							</div>
							
						</div>
						
						<div class="row contribution">
							<div class="contribution_form t-100">
								<div class="contribution_title t-100">
									<h4>记得按时吃🍚</h4>
								</div>
								<div class="contribution_post t-100">
									<form action="/pay/query.php?action=pay" method="post">
										<p class="contribution_f_title">邀请朋友一起吃🍚</p>
										<div class="row" id="dachan">
											<div class="col-md-8 m-p-0">
												<select class="custom-select t-100" name="money" id="money">
													<option value="1" selected="selected">￥1</option>
													<option value="5">￥5</option>
													<option value="10">￥10</option>
													<option value="50">￥50</option>
													<option value="100">￥100</option>
												</select>
											</div>
											<div class="col-md-4 m-p-0 p-left">
												<input type="button" class="btn btn-primary btn-block" value="自定义大餐" id="diydachan">
											</div>
											
										</div>
										<p class="contribution_f_title">📦&nbsp;选一个您请客的菜（物品）名称</p>
										<div class="row">
											<div class="col-md-8 m-p-0">
												<input type="text" class="form-control" name="des" value="咖啡" readonly="">
											</div>
											<div class="col-md-4 m-p-0 p-left">
												<input type="button" class="btn btn-primary btn-block" value="换一个" id="cptype">
											</div>
										</div>
										<br />
										<div class="row">
											<div class="col-md-12 m-p-0">
												<input type="submit" class="btn btn-primary btn-block g-recaptcha" value="🔊老板~结账!" />
											</div>
										</div>
									</form>
									
								</div>
							</div>
						</div>
						
						<div class="row contribution_log">
							<div class="contribution_form t-100">
								<div class="contribution_title t-100">
									<?php
									// 现在可以安全使用 $conn，因为已经在文件开头引入了数据库连接
									$count_sql = "SELECT COUNT(*) as total FROM donations";
									$count_result = $conn->query($count_sql);
									$count = $count_result->fetch_assoc()['total'];
									?>
									<h5>干饭记录(<?php echo $count; ?>条)🍽🍻</h5>
								</div>
								<hr>
								<div class="wy_log">
									<ul>
										<?php
										$sql = "SELECT * FROM donations ORDER BY created_at DESC LIMIT 66";
										$result = $conn->query($sql);
										
										while($row = $result->fetch_assoc()) {
											$displayAmount = rtrim(rtrim(number_format($row['amount'], 2, '.', ''), '0'), '.');
											echo '<li>';
											echo '<p>' . date('Y-m-d H:i:s', strtotime($row['created_at'])) . '</p>';
											echo '<p>网友' . htmlspecialchars($row['nickname']) . '💰💰💰送了我：' . 
												 htmlspecialchars($row['item_name']) . ' <span>￥' . 
												 $displayAmount . '</span></p>';
											echo '</li>';
										}
										?>
									</ul>
								</div>
							</div>
						</div>
						
					</div>
					<div class="clear c-bottom"></div>
				</div>
				<div class="col-md-3"></div>
			</div>
			
			<div class="footer">
				<?php
				// 获取统计数据
				$sql_stats = "SELECT COUNT(*) as total_count, SUM(amount) as total_amount FROM donations";
				$result_stats = $conn->query($sql_stats);
				$stats = $result_stats->fetch_assoc();
				
				echo '<p>一共有[' . $stats['total_count'] . ']位暖心的陌生人请了我恰饭，恰零食，' .
					 '人民币金额共计【' . number_format($stats['total_amount'], 2) . '】元。' .
					 '感谢您的每一次请客，我都有在记录！</p>';
				?>
			</div>
		</div>
		<script src="./style/js/style.js"></script>
		<script src="./style/js/layer/layer.js"></script>
		<?php if(isset($_GET['success']) && $_GET['success'] == '200'): ?>
		<script>
		// 等待页面和layer.js完全加载后再显示提示
		$(document).ready(function() {
			setTimeout(function() {
				layer.msg("感谢您的支持!愿您天天开心,事事顺心!", {
					icon: 1,
					time: 5000
				});
			}, 500);  // 延迟500ms确保layer.js已加载
		});
		</script>
		<?php endif; ?>
	</body>
</html>
