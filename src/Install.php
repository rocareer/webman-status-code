<?php

namespace Rocareer\WebmanStatusCode;

/**
 * webman-status-code 安装/更新/卸载钩子（webman 基础插件，WEBMAN_PLUGIN）
 *
 * 安装（composer require / update）：
 *   1. 接线配置 config/plugin/rocareer/webman-status-code/ 落盘（pathRelation）
 *   2. support/StatusCode.php 落盘到宿主 support/（供无 radmin 宿主直接使用统一状态码）
 *
 * 说明：
 *   - 本插件为纯「状态码常量与工具」库，无数据库表 / 后台菜单 / 进程 / 事件，无预置动作。
 *   - support/StatusCode.php 是 rocareer/radmin 真源的物理拷贝（文件头注明同步约定，
 *     类上标注 @audit-ignore fqcn_dup），与本包 pathRelation 一起落盘。
 *
 * 卸载（composer remove）：
 *   - 移除接线配置目录与落盘的 support/StatusCode.php
 *   - 注意：不删除宿主其它文件
 */
class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * 需要落盘到宿主项目的目录/文件（源 => 目标，相对项目根）
     */
    protected static $pathRelation = [
        'config/plugin/rocareer/webman-status-code' => 'config/plugin/rocareer/webman-status-code',
        'support' => 'support',
    ];

    /**
     * 安装（首次安装或更新）
     *
     * @param bool $isFirst 是否首次安装（composer require 时为 true，update 时为 false）
     */
    public static function install($isFirst = true): void
    {
        static::installByRelation($isFirst);
    }

    /**
     * 更新：刷新接线配置与缺失资源
     */
    public static function update(): void
    {
        static::installByRelation(false);
    }

    /**
     * 卸载：移除本包写入宿主项目的文件
     */
    public static function uninstall(): void
    {
        static::uninstallByRelation();
    }

    /**
     * 按 pathRelation 执行拷贝（目录：首次全量拷贝、更新仅补齐缺失项；文件：缺失才写）
     */
    protected static function installByRelation(bool $isFirst): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parentDir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parentDir)) {
                    mkdir($parentDir, 0777, true);
                }
            }
            // 源以包根为基准：Install.php 位于 src/ 下
            $sourcePath = dirname(__DIR__) . '/' . $source;
            $destPath = base_path() . '/' . $dest;

            if (is_dir($sourcePath)) {
                if ($isFirst || !is_dir($destPath)) {
                    static::copyDir($sourcePath, $destPath);
                    echo "Copy $dest\n";
                }
            } elseif (is_file($sourcePath)) {
                if ($isFirst || !is_file($destPath)) {
                    if (!is_dir(dirname($destPath))) {
                        mkdir(dirname($destPath), 0777, true);
                    }
                    copy($sourcePath, $destPath);
                    echo "Copy $dest\n";
                }
            }
        }
    }

    /**
     * 按 pathRelation 反向删除（先文件后目录，反向遍历保证子项先删）
     */
    protected static function uninstallByRelation(): void
    {
        foreach (array_reverse(static::$pathRelation) as $source => $dest) {
            $path = base_path() . '/' . $dest;
            if (is_dir($path) && !is_link($path)) {
                static::removeDir($path);
                echo "Remove $dest\n";
            } elseif (is_file($path)) {
                unlink($path);
                echo "Remove $dest\n";
            }
        }
    }

    /**
     * 递归复制目录
     */
    protected static function copyDir(string $source, string $dest): void
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        foreach (scandir($source) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $srcPath = $source . '/' . $item;
            $destPath = $dest . '/' . $item;
            if (is_dir($srcPath)) {
                static::copyDir($srcPath, $destPath);
            } else {
                copy($srcPath, $destPath);
            }
        }
    }

    /**
     * 递归删除目录
     */
    protected static function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? static::removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
