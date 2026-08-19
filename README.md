# EPay for WooCommerce

WooCommerce 易支付网关插件，为不同支付通道提供独立的结账选项。

项目仓库：https://github.com/wxcydzcc/epay

## 支付方式

| 结账名称 | API `type` |
| --- | --- |
| 微信支付 | `wxpay` |
| 支付宝 | `alipay` |
| 银联支付 | `bank` |
| Revolut | `revolut` |
| PayPal | `paypal` |
| AlipayHK | `alipayhk` |
| USDT | `usdt` |
| LINE Pay | `linepay` |
| PayNow | `paynow` |
| 信用卡 / 借记卡 | `card` |

易支付服务端需要支持对应的 `type` 值。

## 设计说明

- 每种支付方式都是独立的 WooCommerce 网关，不使用空 `type` 的聚合收银台。
- 付款请求发送到易支付的 `submit.php`，签名方式为 MD5。
- 异步回调支持 GET 和 POST，并且具有幂等处理。
- 订单操作使用 WooCommerce API，并声明兼容 HPOS 订单存储。
- 同时支持经典 shortcode 结账和 WooCommerce Cart/Checkout Blocks。
- 项目不包含码支付、QQ 支付或聚合收银台。

## 环境要求

- PHP 7.4+
- WordPress 5.0+
- WooCommerce 5.0+

## 安装

1. 下载项目并将目录命名为 `epay`。
2. 将 `epay` 上传到 WordPress 的 `wp-content/plugins/` 目录。
3. 在 WordPress 后台启用 **EPay for WooCommerce**。

## 配置

1. 前往 **WooCommerce → 易支付设置**。
2. 填写易支付网关地址、商户 ID 和商户密钥。
3. 前往 **WooCommerce → 设置 → 付款**。
4. 启用需要的支付方式并设置结账页标题。

启用后的支付方式会同时显示在经典结账页和 Checkout Block 中。每个区块支付方式沿用相同的标题、说明和图标设置。

## 回调验证

插件会校验回调签名、商户 ID、交易状态、支付通道和订单金额。异步通知地址会在支付请求中自动提交。

## 开发检查

提交代码前至少运行：

```bash
find . -name '*.php' -type f -print0 | xargs -0 -n1 php -l
```

支付通道、回调签名和金额校验属于高风险区域，修改时请同步补充验证记录。开发约定见 [AGENTS.md](AGENTS.md)。

## 贡献

欢迎提交 Issue 和 Pull Request。提交代码前请确保 PHP 语法检查通过，并说明变更对应的易支付接口行为。

## 许可证

本项目使用 [GNU General Public License v3.0](LICENSE)。
