<?php
require_once 'pay.php';
require_once '../config/db.php';

if($_GET['action'] == 'pay') {
    $alipay = new AlipayService();
    
    // 验证金额
    $amount = $_POST['money'];
    
    // 检查是否是有效数字
    if(!is_numeric($amount)) {
        die('金额必须是数字');
    }
    
    // 转换为浮点数
    $amount = (float)$amount;
    
    // 如果金额小于0.01，自动调整为0.01
    if($amount < 0.01) {
        $amount = 0.01;
    }
    
    // 智能格式化：保留实际小数位数，但最多2位
    $amount = number_format($amount, 2, '.', '');
    $amount = rtrim(rtrim($amount, '0'), '.');  // 移除末尾多余的0和小数点
    
    $orderId = $alipay->generateOrderId();
    $subject = $_POST['des'];
    
    // 获取支付二维码
    $qrCode = $alipay->generateQrCode($orderId, $amount, $subject);
    
    // 显示支付页面
    include 'pay_template.php';
    
} else if($_GET['action'] == 'serve') {
    // 查询支付状态
    $gid = $_POST['gid'];
    $alipay = new AlipayService();
    
    // 查询支付状态
    $status = $alipay->queryOrder($gid);
    
    if($status == 'TRADE_SUCCESS') {
        // 获取订单信息
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $gid);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        // 将支付记录保存到数据库
        $nickname = substr(md5(time()), 0, 2); // 随机生成2位昵称
        $sql = "INSERT INTO donations (nickname, amount, item_name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sds", $nickname, $order['amount'], $order['item_name']);
        $stmt->execute();
        
        echo json_encode(['code' => 200]);
    } else {
        echo json_encode(['code' => 0]); 
    }
}
?>
