<?php


namespace support;

/**
 * 系统状态码定义
 *
 * 真源：本文件为状态码唯一真源；rocareer/webman-status-code 安装时拷贝同名文件
 * （src/support/StatusCode.php）给未装 radmin 的宿主。修改此处后须同步 webman-status-code 副本。
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
    const MESSAGES = [
        self::NEED_LOGIN => '需要登录',
        self::TOKEN_EXPIRED => '凭证已过期',
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