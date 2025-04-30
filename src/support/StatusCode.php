<?php
namespace support;

/**
 * 系统状态码定义
 */
class StatusCode
{
    const SYSTEM                        = 10;           // 系统码
    const TEST                          = 96;           // 测试码
    const NO_ROLE                       = 20110001;           // 无权访问 请获得相应权限
    const TOKEN_REFRESH_FAILED          = 20110002;           // 未知错误
    const ALREADY_LOGGED                = 20110003;           // 未知错误
    const LOGIN_HANDLER_NOT_FOUND       = 20110004;           // 登录处理器未实现
    const USER_NOT_FOUND                = 20110005;           // 用户不存在
    const LOGIN_TYPE_NOTFOUND           = 20110006;           // 不支持的登录类型
    const INVALID_TOKEN                 = 20110007;           // Invalid token:
    const TOKEN_EXPIRED                 = 20110008;           // 凭证已过期 请重新登录
    const NO_TOKEN                      = 20110009;           // 未登录或凭证失效,请重新登录
    const LOGIN_PASSWORD_ERROR          = 20110010;           // 密码错误1
    const LOGIN_NOT_FOUND_USER          = 20110011;           // 用户不存在
    const LOGIN_VALIDATA_FAILED         = 20110012;           // 未知错误
    const CAPTCHA_ERROR                 = 20110013;           // 验证码错误
    const ERROR_EMAIL_OR_PASSWORD       = 20110015;           // 邮箱或密码错误
    const TOKEN_NREFRESH_FAILED         = 20110016;           // 未知错误
    const UPLOAD_ERROR                  = 20110017;           // 未知错误


    // 状态码消息定义
    const MESSAGES = [
        self::SYSTEM                   => '系统码',
        self::TEST                     => '测试码',
        self::NO_ROLE                  => '无权访问 请获得相应权限',
        self::TOKEN_REFRESH_FAILED     => '未知错误',
        self::ALREADY_LOGGED           => '未知错误',
        self::LOGIN_HANDLER_NOT_FOUND  => '登录处理器未实现',
        self::USER_NOT_FOUND           => '用户不存在',
        self::LOGIN_TYPE_NOTFOUND      => '不支持的登录类型',
        self::INVALID_TOKEN            => 'Invalid token: ',
        self::TOKEN_EXPIRED            => '凭证已过期 请重新登录',
        self::NO_TOKEN                 => '未登录或凭证失效,请重新登录',
        self::LOGIN_PASSWORD_ERROR     => '密码错误1',
        self::LOGIN_NOT_FOUND_USER     => '用户不存在',
        self::LOGIN_VALIDATA_FAILED    => '未知错误',
        self::CAPTCHA_ERROR            => '验证码错误',
        self::ERROR_EMAIL_OR_PASSWORD  => '邮箱或密码错误',
        self::TOKEN_NREFRESH_FAILED    => '未知错误',
        self::UPLOAD_ERROR             => '未知错误',
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