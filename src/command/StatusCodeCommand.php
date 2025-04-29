<?php

namespace Rocareer\WebmanStatusCode\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use teamones\responseCodeMsg\Code;

class StatusCodeCommand extends Command
{
    protected static $defaultName = 'scode:run';
    protected static $defaultDescription = 'Generate and manage status codes';

    protected $statusCodeClass;
    protected array $statusScanPath = [];
    protected int $minNumber = 0;
    protected string $systemNumCode = "200";

    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Name description');
        $this->init();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting StatusCodeCommand...');
        try {
            $this->generateStatusCode($input, $io);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    protected function init()
    {
        $config = config("plugin.rocareer.webman-status-code.app");
        if (!isset($config["status_code_class"])) {
            throw new \RuntimeException("Please specify a valid status code class.");
        }

        if (!isset($config["status_scan_path"])) {
            throw new \RuntimeException("Please specify the scan paths.");
        }

        if (!isset($config["system_number"])) {
            throw new \RuntimeException("Please configure the system code.");
        }

        $this->statusCodeClass = $config["status_code_class"];
        $this->statusScanPath = $config["status_scan_path"];
        $this->systemNumCode = Code::getSystemCode((string)$config["system_number"]);

        if (isset($config["start_min_number"]) && $config["start_min_number"] > 0) {
            $this->minNumber = intval($config["start_min_number"]);
        }
    }

    protected function generateStatusCode(InputInterface $input, SymfonyStyle $io)
    {
        $reflection = new \ReflectionClass($this->statusCodeClass);
        $classNameSpaceName = $reflection->getNamespaceName();
        $className = $reflection->getShortName();
        $classPath = $reflection->getFileName();

        if (!is_writable($classPath)) {
            throw new \RuntimeException("The file is not writable: $classPath");
        }

        $io->text('Scanning files for status codes...');
        $codeList = $this->scanFilesForCodes($io);

        $max = $this->minNumber;
        $writeList = [];
        $existingConstants = $reflection->getConstants(); // 获取已存在的常量
        $newConstants = []; // 用于存储新增的常量

        foreach ($codeList as $name => $file) {
            if (!array_key_exists($name, $existingConstants)) {
                $currentNumber = ++$max;
                $currentErrorNumber = $this->systemNumCode . ((string)$currentNumber);
                $writeList[$name] = -(int)$currentErrorNumber; // 新增常量
                $newConstants[$name] = $currentErrorNumber; // 记录新增常量
            } else {
                $writeList[$name] = $existingConstants[$name]; // 使用现有常量的值
            }
        }

        // 读取现有的 MESSAGES 数组
        $messages = [];
        if ($reflection->hasConstant('MESSAGES')) {
            $messages = $reflection->getConstant('MESSAGES');
        }

        // 合并已有的消息和新生成的消息
        foreach ($newConstants as $name => $value) {
            if (!array_key_exists($name, $messages)) {
                $messages[$name] = $this->getConstantDescription($name);
            }
        }

        // 写入文件
        $this->writeToFile($classPath, $classNameSpaceName, $className, $writeList, $messages);

        $io->title('Status code generation completed.');
        $io->text('Total constants: ' . count($writeList));
        if (!empty($newConstants)) {
            $io->section('New constants:');
            $io->table(['Constant', 'Code'], array_map(function ($name, $code) {
                return [$name, $code];
            }, array_keys($newConstants), $newConstants));
        } else {
            $io->text('No new constants added.');
        }
    }

    protected function scanFilesForCodes(SymfonyStyle $output): array
    {
        try {
            $reflection = new \ReflectionClass($this->statusCodeClass);
        } catch (\Exception $e) {
            throw new \Exception("Class does not exist.");
        }

        $className = $reflection->getShortName();
        $codeList = [];
        foreach ($this->statusScanPath as $path) {
            $files = $this->getPhpFiles($path);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                preg_match_all('/' . preg_quote($className . '::') . '(\w+)/', $content, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $match) {
                        // 记录常量名称
                        $codeList[$match] = $file;
                    }
                }
            }
        }

        return $codeList;
    }

    protected function getPhpFiles(string $path): array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $files = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' || $file->getExtension() === 'PHP') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    protected function writeToFile(string $filePath, string $namespace, string $className, array $constants, array $messages)
    {
        $template = <<<EOT
<?php
namespace $namespace;

/**
 * 系统状态码定义
 */
class $className
{

EOT;

        // 写入常量
        foreach ($constants as $name => $value) {
            $padding = str_repeat(' ', 20 - strlen($name)); // 对齐 = 号
            $description = $this->getConstantDescription($name);
            $template .= "    const $name$padding= $value;           // $description\n";
        }

        // 生成 MESSAGES 数组
        $template .= "\n\n    // 状态码消息定义\n";
        $template .= "    const MESSAGES = [\n";

        // 保留已有的消息
        foreach ($messages as $name => $message) {
            if (array_key_exists($name, $constants)) { // 确保常量存在
                $padding = str_repeat(' ', 20 - strlen("self::$name")); // 对齐 = 号
                $template .= "        self::$name$padding => '$message',\n";
            }
        }

        $template .= "    ];\n\n";

        // 生成 getMessage() 方法
        $template .= <<<EOT

    /**
     * 获取状态码对应的消息
     * @param int \$code
     * @return string
     */
    public static function getMessage(int \$code): string
    {
        return self::MESSAGES[\$code] ?? '未知错误';
    }
}
EOT;

        // 确保文件内容写入前已经准备好
        if (file_put_contents($filePath, $template) === false) {
            throw new \RuntimeException("Failed to write to file: $filePath");
        }
    }

    // 为常量提供描述的方法
    protected function getConstantDescription(string $name): string
    {
        // 根据常量名称返回描述
        switch ($name) {
            case 'SUCCESS':
                return '操作成功';
            case 'ALREADY_LOGGED':
                return '用户已登录';
            case 'BAD_REQUEST':
                return '错误请求';
            case 'UNAUTHORIZED':
                return '未授权访问';
            case 'FORBIDDEN':
                return '禁止访问';
            case 'NOT_FOUND':
                return '资源不存在';
            case 'TOO_MANY_REQUESTS':
                return '请求过于频繁';
            case 'VALIDATION_ERROR':
                return '验证错误';
            case 'SERVER_ERROR':
                return '服务器错误';
            default:
                return '未知错误';
        }
    }
}
