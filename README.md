# rocareer/webman-status-code

Webman 统一错误码/状态码插件：一套与 **rocareer/radmin 3.2+ 完全对齐**的状态码定义（含中文消息与 `getMessage()`），随插件复制安装到宿主项目 `support/`，并提供 `scode:run` 命令扫描代码库自动重生成状态码类。

- **统一状态码**：`support\StatusCode` 常量 + `MESSAGES` 中文消息表 + `getStatusCode/getMessage` 用法；与 radmin 内置 `support\StatusCode` diff 一致（v1.0.3 起同步 radmin 3.2 安全加固新增码）
- **复制安装**：webman 标准插件（`Install::WEBMAN_PLUGIN`），安装时把 `support/StatusCode` 与插件配置复制到宿主项目，卸载可还原
- **代码规范**：`Code::getSystemCode()` 提供 8 位错误码编号规范（3 位系统标识 + 中间 2 位服务标识 + 后 3 位错误码，负数）
- **自动重生成**：`php webman scode:run` 扫描 `status_scan_path` 下所有 PHP 文件，收集 `StatusCode::XXX` 使用点 / `const` 定义 / `throw XxxException('消息', StatusCode::XXX)` 三处信息，重新生成整个状态码类文件（常量按值排序 + MESSAGES + getMessage）

## 安装

```bash
composer require rocareer/webman-status-code
php webman plugin:install rocareer/webman-status-code
# 复制产物：
#   config/plugin/rocareer/webman-status-code/（app.php + command.php）
#   support/StatusCode.php
```

dev 全家桶（`dev/full`、`dev/luoling`）已通过 path 仓库钉版 `v1.0.3` 接入。

## 使用

```php
use support\StatusCode;

// 控制器/服务内返回统一错误
return $this->error(StatusCode::NO_TOKEN);              // code + 中文消息自动附带
return $this->error('用户不存在', StatusCode::USER_NOT_FOUND);

// 取任意码的中文消息（未登记返回 '未知错误'）
$msg = StatusCode::getMessage(StatusCode::TOKEN_EXPIRED);
```

常用码段（完整列表见 `src/support/StatusCode.php`）：

| 码 | 常量 | 含义 |
|---|---|---|
| 10001 | USER_NOT_FOUND | 用户不存在 |
| 10010 | PASSWORD_ERROR | 密码错误 |
| 10015 | LOGIN_FAILED | 登录失败 |
| 10016 | NOT_LOGIN | 未登录 |
| 10030 | VALIDATION_ERROR | 校验失败 |
| 10046 | ACCESS_DENIED | 访问权限不足 |
| 10047 | TOKEN_GENERATE_FAILED | Token 生成失败 |
| 10048 | MEMBER_LOGGED_IN_ELSEWHERE | 账号已在其他地方登录 |

## 代码生成命令

```bash
php webman scode:run
```

- 扫描 `status_scan_path`（默认 `app_path()`）下全部 PHP 文件，识别：
  - `StatusCode::XXX` 引用（描述默认 '未知错误'）
  - `const NAME = N; // 中文注释` 形式的定义（注释作为描述）
  - `throw XxxException('消息', StatusCode::XXX)`（消息作为描述，覆盖默认）
- 重生成状态码类文件：保留已有常量原值、为新增引用自动分配下一个码值（起始 `start_min_number`），按常量值排序输出，附 MESSAGES 表与 `getMessage()`
- 输出新增常量清单，便于 code review 后提交

> 注意：命令直接覆写状态码类源文件，生成结果需人工 review 后再提交；已发布的码值**只增不改**，避免前端/客户端依赖旧值。

## 配置（config/plugin/rocareer/webman-status-code/app.php）

```php
return [
    'enable' => true,
    'status_code_class' => new StatusCode(),   // 目标状态码类（support\StatusCode）
    'system_number' => 201,                    // 系统标识（8 位码前 3 位）
    'start_min_number' => 10000,               // 新增码起始范围，例如 10000-99999
    'status_scan_path' => [app_path()],        // 扫描路径列表，可自行扩充 config_path()/process/ 等
];
```

## 类参考

| 类 | 说明 |
|---|---|
| `support\StatusCode` | 状态码常量 + MESSAGES + `getMessage()`（复制到宿主项目 `support/`，使用侧唯一入口） |
| `Rocareer\WebmanStatusCode\Code` | 错误码编号规范工具：`getSystemCode('201')` → 3 位系统标识（截断/补零） |
| `Rocareer\WebmanStatusCode\command\StatusCodeCommand` | `scode:run` 命令实现（Symfony Console） |
| `Rocareer\WebmanStatusCode\Install` | webman 插件安装/卸载（复制/移除 support 与配置） |

## 历史遗留（只读，勿引用）

- `src/StatusRun.php`（`teamones\responseCodeMsg\Generate`，类名 Generate）与根目录 `error_code.php`：早期"负 8 位错误码 shell 扫描"实现，命名空间/类名均为迁移遗留；现行路径为 `scode:run`（`StatusCodeCommand`）
- `src/StatusCode.php`：早期重复定义文件（namespace 误写为 support），现行定义以 `src/support/StatusCode.php` 为准

## 与 radmin 的同步约定

`src/support/StatusCode.php` 必须与 `rocareer/radmin` 内置 `support\StatusCode` 保持 diff 一致（radmin 升级新增状态码时同步本包，参考 v1.0.3 变更记录）。

## 卸载

```bash
php webman plugin:uninstall rocareer/webman-status-code
composer remove rocareer/webman-status-code
```

仅移除插件复制的 `support/StatusCode.php` 与 `config/plugin/rocareer/webman-status-code/`；宿主项目已改写的业务代码需自行处理。
