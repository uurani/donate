<?php
// 配置信息
define('ALIPAY_APPID', '你的支付宝APPID');
define('ALIPAY_PRIVATE_KEY', "-----BEGIN RSA PRIVATE KEY-----\n" .
    "你的支付宝应用私钥" .
    "\n-----END RSA PRIVATE KEY-----");

define('ALIPAY_PUBLIC_KEY', "-----BEGIN PUBLIC KEY-----\n" .
    "你的支付宝公钥" .
    "\n-----END PUBLIC KEY-----");

class AlipayService {
    // 生成订单号
    public function generateOrderId() {
        global $conn;
        
        $orderId = substr(md5(uniqid(mt_rand(), true)), 0, 12);
        
        // 保存订单信息
        $sql = "INSERT INTO orders (order_id, amount, item_name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sds", $orderId, $_POST['money'], $_POST['des']);
        $stmt->execute();
        
        return $orderId;
    }
    
    // 生成支付二维码URL
    public function generateQrCode($orderId, $amount, $subject) {
        // 1. 准备请求参数
        $bizContent = array(
            'out_trade_no' => $orderId,
            'total_amount' => $amount,
            'subject' => $subject,
            'product_code' => 'FACE_TO_FACE_PAYMENT'
        );
        
        // 2. 构建请求数据
        $params = array(
            'app_id' => ALIPAY_APPID,
            'method' => 'alipay.trade.precreate',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent)
        );
        
        // 3. 生成签名
        $params['sign'] = $this->generateSign($params, ALIPAY_PRIVATE_KEY);
        
        // 4. 发送请求到支付宝
        $url = 'https://openapi.alipay.com/gateway.do';
        $result = $this->sendRequest($url, $params);
        
        // 5. 返回二维码链接
        if(isset($result['alipay_trade_precreate_response']) 
           && $result['alipay_trade_precreate_response']['code'] == '10000') {
            return $result['alipay_trade_precreate_response']['qr_code'];
        }
        
        // 如果失败返回默认二维码
        return 'https://qr.alipay.com/你的默认收款标号';
    }
    
    // 生成签名
    private function generateSign($params, $privateKey) {
        // 1. 对参数进行排序
        ksort($params);
        
        // 2. 构建签名字符串
        $stringToBeSigned = "";
        foreach ($params as $k => $v) {
            if($k != 'sign' && !empty($v)) {
                $stringToBeSigned .= "&{$k}={$v}";
            }
        }
        $stringToBeSigned = substr($stringToBeSigned, 1);
        
        // 3. 签名
        $sign = "";
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        openssl_sign($stringToBeSigned, $sign, $privateKeyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKeyResource);
        return base64_encode($sign);
    }
    
    // 发送HTTP请求
    private function sendRequest($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
    
    // 查询订单状态
    public function queryOrder($orderId) {
        // 构建查询请求
        $bizContent = array(
            'out_trade_no' => $orderId
        );
        
        $params = array(
            'app_id' => ALIPAY_APPID,
            'method' => 'alipay.trade.query',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent)
        );
        
        // 生成签名
        $params['sign'] = $this->generateSign($params, ALIPAY_PRIVATE_KEY);
        
        // 发送请求
        $url = 'https://openapi.alipay.com/gateway.do';
        $result = $this->sendRequest($url, $params);
        
        if(isset($result['alipay_trade_query_response'])) {
            return $result['alipay_trade_query_response']['trade_status'];
        }
        
        return 'WAIT_BUYER_PAY';
    }
}

// 支付成功后，将记录保存到数据库
if($支付成功) {
    // 获取用户昵称的前两个字符
    $nickname = mb_substr($user_nickname, 0, 2, 'UTF-8');
    
    $sql = "INSERT INTO donations (nickname, amount, item_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sds", $nickname, $amount, $item_name);
    $stmt->execute();
} 