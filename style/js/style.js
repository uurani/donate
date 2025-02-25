function handlePayment() {
    const formData = {
        money: $('#money').val(),
        des: $('input[name="des"]').val()
    };

    // 调用后端创建支付订单
    $.post('/pay/query.php?action=create', formData)
        .done(function(response) {
            if(response.qr_code){
                // 显示支付宝付款二维码
                layer.open({
                    type: 1,
                    title: '支付宝扫码支付',
                    content: '<div style="text-align:center"><img src="'+response.qr_code+'" width="250"></div>',
                    area: ['300px', '400px']
                });

                // 开启支付状态轮询
                checkPaymentStatus(response.out_trade_no);
            }
        })
        .fail(function() {
            layer.msg('订单创建失败');
        });
}

// 轮询支付状态
function checkPaymentStatus(outTradeNo) {
    const timer = setInterval(function() {
        $.get('/pay/query.php?action=check&out_trade_no=' + outTradeNo)
            .done(function(res) {
                if(res.trade_status === 'TRADE_SUCCESS') {
                    clearInterval(timer);
                    layer.msg('支付成功', {icon: 1});
                    setTimeout(() => location.reload(), 1500);
                }
            });
    }, 3000); // 每3秒查询一次
}

$(function () {

    function changetype() {
        $("#dachan").html('');
        $("#dachan").html('<div class="col-md-12 m-p-0 t-100"><input type="text" class="form-control" value="10" id="money" required=""  name="money" pattern="[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*" placeholder="输入您想请我吃的大餐价格（数字）"></div>');

    }

    $("#diydachan").click(function () {
        layer.prompt({
            title: '请输入自定义金额',
            formType: 0,
            value: '1'
        }, function(value, index, elem){
            // 验证输入的金额
            var amount = parseFloat(value);
            
            // 检查是否是有效数字
            if(isNaN(amount)) {
                layer.msg('请输入有效金额', {icon: 2});
                return;
            }
            
            // 如果金额小于0.01，自动调整为0.01
            if(amount < 0.01) {
                amount = 0.01;
                layer.msg('金额已自动调整为0.01元', {icon: 1});
            }
            
            // 智能格式化金额：保留实际小数位数，但最多2位
            amount = parseFloat(amount.toFixed(2));
            // 转为字符串，如果是整数则不显示小数点
            var displayAmount = amount.toString();
            
            // 更新金额
            $("#money").append('<option value="'+amount+'" selected>￥'+displayAmount+'</option>');
            layer.close(index);
        });
    });

    function wpname() {
        jg = $("#money").val();

        if (jg <= 0.5) {
            cpname = Array('一份爱');
        } else if (jg <= 1) {
            cpname = Array('一包辣条', '一瓶矿泉水', '一包干脆面', '一个包子', '一个鸡蛋', '一根火腿肠');
        } else if (jg <= 5) {
            cpname = Array('一份肠粉', '一份煎饼果子', '一份肉夹馍', '一瓶快乐水', '一个小鸡腿', '一桶泡面', '一瓶牛奶');
        } else if (jg <= 10) {
            cpname = Array('一份杂粮煎饼', '一杯奶茶', '一个汉堡', '一份快餐', '一碗云吞', '一碗饺子');
        } else if (jg <= 50) {
            cpname = Array('一份水煮鱼', '一份欧式牛排', '一顿自助餐', '一顿火锅', '一只烤鸡');
        } else if (jg <= 100) {
            cpname = Array('一份KFC全家桶', '一份3斤小龙虾', '一份纸包鱼');
        } else if (jg > 100 && jg < 888) {
            cpname = Array('奢华大餐', '奢侈的一堆干脆面', '一个月的快乐水');
        } else if (jg >= 888) {
            cpname = Array('直接包养了');
        }

        arrl = cpname.length;
        num = Math.floor(Math.random() * arrl);

        $("input[name='des']").val(cpname[num]);
    }

    wpname();

    $("#cptype").click(function () {
        wpname();
    });

    document.onkeydown = function () {

        if (window.event && window.event.keyCode == 123) {
            event.keyCode = 0;
            event.returnValue = false;
        }


    }

    document.oncopy = function (event) {
        if (window.event) {
            event = window.event;
        }
        try {
            var the = event.srcElement;
            if (!((the.tagName == "INPUT" && the.type.toLowerCase() == "text") || the.tagName == "TEXTAREA")) {
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
    }


});

$(function () {
    var wh = $(window).height();
    setInterval(function () {
        var f = $(document).width();
        var e = Math.random() * f - 100;//雪花的定位left值
        var o = 0.3 + Math.random();//雪花的透明度
        var fon = 10 + Math.random() * 30;//雪花大小
        var l = e - 100 + 200 * Math.random();//雪花的横向位移
        var k = 2000 + 5000 * Math.random();
        var html;
        switch (Math.floor(Math.random() * 13 + 1)) {
            case 1:
                html = "<div class='snow'>🍚<div>";
                break;
            case 2:
                html = "<div class='snow'>🍤<div>";
                break;
            case 3:
                html = "<div class='snow'>🍖<div>";
                break;
            case 4:
                html = "<div class='snow'>🍱<div>";
                break;
            case 5:
                html = "<div class='snow'>🍭<div>";
                break;
            case 6:
                html = "<div class='snow'>🍋<div>";
                break;
            case 7:
                html = "<div class='snow'>🍮<div>";
                break;
            case 8:
                html = "<div class='snow'>🍝<div>";
                break;
            case 9:
                html = "<div class='snow'>🍥<div>";
                break;
            case 10:
                html = "<div class='snow'>🍐<div>";
                break;
            case 11:
                html = "<div class='snow'>🍻<div>";
                break;
            case 12:
                html = "<div class='snow'>🌰<div>";
                break;
            case 13:
                html = "<div class='snow'>🍉<div>";
                break;

        }
        $(html).clone().appendTo("body").css({
            left: e + "px",
            opacity: o,
            "font-size": fon,
        }).animate({
            top: (wh * 2) + "px",
            left: l + "px",
            opacity: 0.1,
        }, k, "linear", function () {
        })
    }, 600)
})
