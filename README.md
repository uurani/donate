### 📌 项目背景​

在个人博客/自媒体场景中，接收读者小额赞赏是常见的互动方式。基于支付宝的「面对面支付」接口，我开发了一个轻量级赞赏页，支持金额选择、动态二维码生成、捐赠记录展示等功能。核心代码基于PHP+MySQL实现，无需依赖复杂框架，适合个人快速部署

#### ​🚀 核心功能拆解​

1. ​动态支付二维码生成​
```PHP
// 调用第三方API生成二维码（示例）
<img src="https://api.qrtool.cn/?text=<?php echo urlencode($qrCode); ?>">
```
- 通过alipays://协议深度链接唤起支付宝APP

- 移动端自动识别设备类型，显示「点击跳转支付宝」按钮

2. ​捐赠记录实时展示​
```PHP
// 数据库查询最新66条记录
$sql = "SELECT * FROM donations ORDER BY created_at DESC LIMIT 66";
while($row = $result->fetch_assoc()) {
echo '<li>网友'.$row['nickname'].'赠送：'.$row['item_name'].'</li>';
}
```
- 采用瀑布流式滚动设计

- 统计总金额和捐赠人次（SQL聚合查询）

3. ​支付状态轮询机制​
```PHP
// 每5秒轮询支付状态
setInterval(function() {
$.ajax({ url: "/pay/query.php?action=serve", data: { gid: "<?php echo $orderId; ?>" } });
}, 5000);

// 60秒自动刷新防重复支付
setTimeout(() => window.location.reload(), 60000);
```
- 参考支付宝官方推荐的轮询策略

- 前端使用Layer.js实现支付成功弹窗提示

#### ​🔧 技术亮点​

1. ​设备自适应设计​
通过isMobile()函数检测设备类型，动态切换H5/PC布局：
``` javascript
function isMobile() {
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
return (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'MicroMessenger') !== false);
}
```
2. ​防重复支付设计​

- 生成带时间戳的订单号`createNewOrderNo()`

- 数据库记录支付请求参数和状态

3. ​安全增强措施​

- 使用`htmlspecialchars()`过滤用户输入

- 限制二维码有效期为60秒

- 关键参数加密传输（示例未展示具体实现）

#### ​📊 数据统计展示​

通过底部统计模块直观展示捐赠影响力：
```PHP
$sql_stats = "SELECT COUNT(*) as total_count, SUM(amount) as total_amount FROM donations";
echo "共有[{$stats['total_count']}]位暖心人捐赠，总计[{$stats['total_amount']}]元";
```
- 数字动态更新，增强用户参与感

- 采用「恰饭」「零食」等趣味化文案

#### ​💡 开发心得​

1. ​支付宝接口选择​
个人开发者推荐使用「当面付」或「电脑网站支付」接口，前者无需营业执照即可申请

2. ​性能优化点​

- 使用CDN加载Bootstrap/jQuery（减少服务器压力）

- 捐赠记录分页加载（当前示例限制66条）

3. ​扩展可能性​

- 接入微信支付实现双渠道收款

- 添加打赏留言功能（需注意内容审核）

- 数据可视化图表展示

#### ​源码结构参考：
``` text
donate
├── config/
│   └── db.php                # 数据库配置文件
│
├── pay/
│   ├── pay.php              # 支付宝支付核心处理类
│   ├── pay_template.php     # 支付页面模板
│   ├── query.php            # 支付状态查询处理
│   └── public/              # 支付页面静态资源
│       ├── style.css
│       └── jquery.min.js
│
├── style/
│   ├── css/
│   │   └── style.css       # 主页样式文件
│   └── js/
│       ├── jquery-3.2.1.min.js
│       ├── layer/          # layer弹窗组件
│       │   └── layer.js
│       └── style.js        # 主页JS逻辑
│
├── sql/
│   ├── donation.sql        # 捐赠记录表结构
│   └── orders.sql          # 订单表结构
│
├── favicon.ico             # 网站图标
└── index.php
```
#### ​🌐 效果预览​
