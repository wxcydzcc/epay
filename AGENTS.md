# AGENTS.md

## 项目目标

这是一个精简的 WooCommerce 易支付网关插件。保持代码小而清晰，不引入码支付、QQ 支付或空 `type` 的聚合收银台。

## 稳定接口约定

- 微信支付：`wxpay`
- 支付宝：`alipay`
- 银联支付：`bank`
- Revolut：`revolut`
- PayPal：`paypal`
- AlipayHK：`alipayhk`
- USDT：`usdt`
- LINE Pay：`linepay`
- PayNow：`paynow`
- 信用卡 / 借记卡：`card`

除非用户明确要求，不要改变这些 API `type` 值或结账名称。特别注意：银联的显示名称是“银联支付”，但 API 调用值是 `bank`。

## 安全不变量

- 回调必须先验证签名，再读取交易结果。
- 回调必须校验商户 ID、`TRADE_SUCCESS`、订单支付方式、通道 `type` 和完整订单金额。
- 已支付订单的重复回调必须返回 `success`，不得重复执行支付完成逻辑。
- 不得把商户密钥、订单隐私数据或其他凭据写入日志、测试固定值或 Git。
- 不要在生成支付链接前清空购物车。
- 订单读写使用 WooCommerce CRUD API，保持 HPOS 兼容。

## 兼容性

- 最低 PHP 7.4，避免使用更高 PHP 版本才支持的语法。
- 最低 WordPress 5.0 和 WooCommerce 5.0。
- 保留 `before_woocommerce_init` 中的 HPOS 兼容声明。
- 不要声称“已测试至”某个 WooCommerce 版本，除非实际运行过该版本的测试。

## 工作流程

1. 修改前检查 `git status -sb`，不覆盖用户的未提交更改。
2. 仅修改当前任务涉及的文件。
3. 所有 PHP 文件运行 `php -l`。
4. 如果修改支付方式，验证网关类、显示名称和 API `type` 的对应关系。
5. 如果修改签名或回调，至少验证正确签名、错误签名、金额不匹配和重复回调。
6. 使用 Git 提交信息记录公开变更；不要把个人工作日志或私人历史加入本仓库。

## 发布

- GitHub 仓库：`https://github.com/wxcydzcc/epay`
- 默认分支：`main`
- 推送前确保工作树只包含本次任务的变更。
- 不提交 ZIP 包、依赖目录、日志或本地 IDE 配置。
