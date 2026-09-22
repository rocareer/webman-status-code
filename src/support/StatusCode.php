<?php


namespace support;

/**
 * 系统状态码定义（rocareer/radmin src/support/StatusCode.php 的同步副本）
 *
 * 用途：webman-status-code 安装时经 pathRelation 将本文件落盘到宿主 support/，
 * 供未装 radmin 的宿主获得统一的 support\StatusCode（装了 radmin 的宿主由 radmin
 * PSR-4 映射优先生效，本文件不参与运行时解析）。
 *
 * 同步约定：内容随 radmin 真源同步，同步时必须保留本注解 @audit-ignore fqcn_dup
 * （声明为有意的真源同步副本，rocareer:audit fqcn_dup 门禁依赖该注解除重），
 * 历史同步曾丢失该注解导致门禁误报。
 *
 * @audit-ignore fqcn_dup
 * 【业务码 ≠ HTTP 状态码】（20260922 拍板：内部后台 API 一律 HTTP 200 + 本表业务码信封
 * {code,msg,time,data}，前端 createAxios 拦截器按 code 分诊——409 静默续期重放、303 回登录页、
 * 非 1 红错 toast）。注意本表码值**沿用了常见 HTTP 码的字面**（303/401/409/500）但语义是
 * 业务层的，与传输层无关：NEED_LOGIN=303「需登录」≠ HTTP 303 See Other；NO_PERMISSION=401
 * 「无权限」≠ HTTP 401 Unauthorized（HTTP 401 是「未认证」，「无权限」的 HTTP 语义是 403）；
 * TOKEN_EXPIRED=409 与 HTTP 409 Conflict 无关；SYSTEM_ERROR=500 恰好同义。传输层真值只有
 * 两种：JSON 业务响应恒 HTTP 200；框架/网关异常才产生真 5xx。勿以 HTTP 语义反推业务码，
 * 也勿据业务码猜测 HTTP 状态——判读响应一律以信封 code 为准（脚本/测试同口径，历史三次
 * 误读教训）。文件流下载/打印等非信封响应：错误体也是 HTTP 200+application/json，
 * 客户端按响应 Content-Type 判别（真源 /@/utils/download 的 fetchBlobByToken；
 * rocareer:audit frontend_raw_fetch 门禁禁页面裸 fetch/XHR 绕过信封）。
 * 若未来开放面向外部第三方的公开 API（跨信任域），该层应按公开惯例使用真 HTTP 语义
 * （401/403/429 + WWW-Authenticate），与本表分层共存、互不混用。
 */
class StatusCode
{
    const NEED_LOGIN                = 303; // 需要登录
    const TOKEN_EXPIRED             = 409; // 凭证已过期（与 TOKEN_SHOULD_REFRESH 共用 409：认证链路对两者同走续期，MESSAGES 只保留 409 一条文案）
    const NO_PERMISSION             = 401;
    const METHOD_NOT_ALLOWED        = 433;
    const TOKEN_SHOULD_REFRESH      = 409; // 凭证需刷新
    const SYSTEM_ERROR              = 500; // 系统错误
    const MEMBER_ERROR              = 666; // 会员错误
    const TOKEN_INVALID             = 10002; // 凭证无效
    const USER_NOT_FOUND            = 10001; // 用户不存在
    const SERVER_ERROR              = 10004; // 服务器错误
    const USER_DISABLED             = 10008; // 用户已禁用
    const LOGIN_ACCOUNT_LOCKED      = 10009; // 账号已锁定
    const PASSWORD_ERROR            = 10010; // 密码错误
    const PASSWORD_CHANGE_FAILED    = 10011; // 密码修改失败
    const TOKEN_NOT_FOUND           = 10012; // 凭证不存在
    const USER_SAVE_FAILED          = 10013; // 用户保存失败
    const LOGIN_TYPE_NOTFOUND       = 10014; // 登录类型不存在
    const LOGIN_FAILED              = 10015; // 登录失败
    const NOT_LOGIN                 = 10016; // 未登录
    const SESSION_MISMATCH          = 10017; // 会话不匹配
    const VIP_EXPIRED               = 10018; // VIP 已过期
    const USER_BANNED               = 10019; // 用户已封禁
    const USERNAME_REQUIRED         = 10020; // 用户名必填
    const PASSWORD_REQUIRED         = 10021; // 密码必填
    const CAPTCHA_REQUIRED          = 10022; // 验证码必填
    const TOKEN_CREATE_FAILED       = 10023; // 凭证创建失败
    const STATE_MANAGER_NOT_INIT    = 10024; // 状态管理器未初始化
    const STATE_MANAGER_CACHE_ERROR = 10025; // 状态缓存错误
    const UNAUTHORIZED              = 10027; // 未授权
    const AUTH_ERROR                = 10028; // 认证错误
    const BUSINESS_ERROR            = 10029; // 业务错误
    const VALIDATION_ERROR          = 10030; // 校验错误
    const SAVE_CACHE_FAILED         = 10031; // 缓存保存失败
    const STATE_ERROR               = 10032; // 状态错误
    const MEMBER_NOT_FOUND          = 10033; // 会员不存在
    const TOKEN_BLACK               = 10034; // 凭证已拉黑
    const TOKEN_DECODE_FAILED       = 10035; // 凭证解码失败
    const TOKEN_ERROR               = 10036; // 凭证错误
    const TOKEN_ENCODE_FAILED       = 10037; // 凭证编码失败
    const TOKEN_VERIFY_FAILED       = 10038; // 凭证校验失败
    const TOKEN_REFRESH_FAILED      = 10039; // 凭证刷新失败
    const TOKEN_DESTROY_FAILED      = 10040; // 凭证销毁失败
    const AUTHENTICATOR_ERROR       = 10043; // 认证器错误
    const AUTHENTICATION_FAILED     = 10044; // 认证失败
    const STATE_CACHE_FIND_FAILED   = 10045; // 状态缓存查找失败
    const ACCESS_DENIED             = 10046;            // 访问权限不足
    const TOKEN_GENERATE_FAILED     = 10047;            // Token生成失败
    const MEMBER_LOGGED_IN_ELSEWHERE     = 10048;            // 账号已在其他地方登录


    // 状态码消息定义
    // 注：TOKEN_EXPIRED/TOKEN_SHOULD_REFRESH 共用 409（认证链路对两者同走续期），
    // MESSAGES 只保留 TOKEN_SHOULD_REFRESH 一条（数组后者覆盖前者，重复条目已收敛）
    const MESSAGES = [
        self::NEED_LOGIN => '需要登录',
        self::NO_PERMISSION => '无权限',
        self::METHOD_NOT_ALLOWED => '请求方法不允许',
        self::TOKEN_SHOULD_REFRESH => '凭证需刷新',
        self::SYSTEM_ERROR => '系统错误',
        self::MEMBER_ERROR => '会员错误',
        self::TOKEN_INVALID => '凭证无效',
        self::USER_NOT_FOUND => '用户不存在',
        self::SERVER_ERROR => '服务器错误',
        self::USER_DISABLED => '用户已禁用',
        self::LOGIN_ACCOUNT_LOCKED => '账号已锁定',
        self::PASSWORD_ERROR => '密码错误',
        self::PASSWORD_CHANGE_FAILED => '密码修改失败',
        self::TOKEN_NOT_FOUND => '凭证不存在',
        self::USER_SAVE_FAILED => '用户保存失败',
        self::LOGIN_TYPE_NOTFOUND => '登录类型不存在',
        self::LOGIN_FAILED => '登录失败',
        self::NOT_LOGIN => '未登录',
        self::SESSION_MISMATCH => '会话不匹配',
        self::VIP_EXPIRED => 'VIP 已过期',
        self::USER_BANNED => '用户已封禁',
        self::USERNAME_REQUIRED => '用户名必填',
        self::PASSWORD_REQUIRED => '密码必填',
        self::CAPTCHA_REQUIRED => '验证码必填',
        self::TOKEN_CREATE_FAILED => '凭证创建失败',
        self::STATE_MANAGER_NOT_INIT => '状态管理器未初始化',
        self::STATE_MANAGER_CACHE_ERROR => '状态缓存错误',
        self::UNAUTHORIZED => '未授权',
        self::AUTH_ERROR => '认证错误',
        self::BUSINESS_ERROR => '业务错误',
        self::VALIDATION_ERROR => '校验错误',
        self::SAVE_CACHE_FAILED => '缓存保存失败',
        self::STATE_ERROR => '状态错误',
        self::MEMBER_NOT_FOUND => '会员不存在',
        self::TOKEN_BLACK => '凭证已拉黑',
        self::TOKEN_DECODE_FAILED => '凭证解码失败',
        self::TOKEN_ERROR => '凭证错误',
        self::TOKEN_ENCODE_FAILED => '凭证编码失败',
        self::TOKEN_VERIFY_FAILED => '凭证校验失败',
        self::TOKEN_REFRESH_FAILED => '凭证刷新失败',
        self::TOKEN_DESTROY_FAILED => '凭证销毁失败',
        self::AUTHENTICATOR_ERROR => '认证器错误',
        self::AUTHENTICATION_FAILED => '认证失败',
        self::STATE_CACHE_FIND_FAILED => '状态缓存查找失败',
        self::ACCESS_DENIED             => '访问权限不足',
        self::TOKEN_GENERATE_FAILED     => 'Token生成失败',
        self::MEMBER_LOGGED_IN_ELSEWHERE     => '账号已在别处登录',
    ];


    /**
     * 获取状态码对应的消息
     * @param int $code
     * @return string
     */
    public static function getMessage(int $code): string
    {
        return self::MESSAGES[$code] ?? '未知错误';
    }
}