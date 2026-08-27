# 更新日志 (Changelog)

## 未发布（Unreleased）

### 状态码体系收敛（v1.0.4）

- 删除 `src/StatusCode.php`（namespace support 的业务码版：全仓库零引用 + 与 `src/support/StatusCode.php`
  同名类冲突 + PSR-4 无法加载的死代码）。
- `src/support/StatusCode.php` 明确为 rocareer/radmin 真源的物理拷贝（文件头注明同步约定），
  消除"两套同名 support\StatusCode"的类重复定义风险。
- 修复 `Code::getSystemCode()` 位数补齐 bug：`str_pad` 目标长度由 `$len-$numberLength` 改为 `$len`
  （如系统标识 '1' 此前生成 '10'，现正确生成 '100'）。
- `StatusCodeCommand` 清理冗余空行。

### 文档

- 新增 README：插件职责/安装/使用示例（异常抛码 + getMessage）、scode:run 代码生成说明、配置项、类参考、历史遗留（StatusRun/error_code.php）说明、与 radmin 同步约定、卸载

### 许可与版权

- 许可证由开源协议改为 proprietary（商业/内部专有），不适用任何开源许可证；LICENSE 文件同步替换为 Rocareer 专有许可文本。
- 版权声明统一为：Copyright (c) Rocareer Team. All rights reserved.；作者：albert@rocareer.com。

本项目版本号遵循 [Semantic Versioning](https://semver.org/lang/zh-CN/)。

## [v1.0.3] - 2026-08-23

### 同步：support/StatusCode 与 rocareer/radmin 3.2+ 对齐

- 补齐 ACCESS_DENIED(10046)/TOKEN_GENERATE_FAILED(10047)/MEMBER_LOGGED_IN_ELSEWHERE(10048)/SAVE_CACHE_FAILED(10031)/STATE_ERROR(10032) 常量与消息
- 与 radmin 内置 `support\StatusCode` 完全一致（diff 校验），radmin 3.2 安全加固依赖这些状态码

## [v1.0.2] - 2026-08-23

### 维护

- composer 最低稳定性设为 dev（历史版本）

## [v1.0.1] - 2025-05

### 修复

- 状态码常量及消息定义重构后的首版修复

## [v1.0.0] - 2025-05

- 首个版本：webman 状态码插件（错误码定义 + 生成命令 + support/StatusCode 复制安装）
