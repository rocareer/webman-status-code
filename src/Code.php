<?php

namespace Rocareer\WebmanStatusCode\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
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
        $this->statusScanPath = [
            radmin_app(),
            app_path(),
        ];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting StatusCodeCommand...');
        try {
            $this->generateStatusCode($input, $output);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
        $output->writeln('<info>Process completed successfully.</info>');
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

    protected function generateStatusCode(InputInterface $input, OutputInterface $output)
    {
        $reflection = new \ReflectionClass($this->statusCodeClass);
        $classNameSpaceName = $reflection->getNamespaceName();
        $className = $reflection->getShortName();
        $classPath = $reflection->getFileName();

        if (!is_writable($classPath)) {
            throw new \RuntimeException("The file is not writable: $classPath");
        }

        $output->writeln('Scanning files for status codes...');
        $codeList = $this->scanFilesForCodes($output);

        $max = $this->minNumber;
        $writeList = [];
        foreach ($reflection->getConstants() as $constName => $value) {
            $currentRealNumber = (int)substr((string)$value, 4);
            $writeList[$constName] = $value;
            unset($codeList[$constName]);
            if ($currentRealNumber > $max) {
                $max = $currentRealNumber;
            }
        }

        foreach ($codeList as $name => $value) {
            $currentNumber = ++$max;
            $currentErrorNumber = $this->systemNumCode . ((string)$currentNumber);
            $writeList[$name] = -(int)$currentErrorNumber;
        }

        $this->writeToFile($classPath, $classNameSpaceName, $className, $writeList);
        $output->writeln('Status code generation completed.');
    }

    protected function scanFilesForCodes($output): array
    {
        try {
            $reflection = new \ReflectionClass($this->statusCodeClass);
        } catch (\Exception $e) {
            throw new \Exception("Class does not exist.");
        }
        $classNameSpaceName = $reflection->getNamespaceName();
        $tmp = explode("\\", $reflection->getName());
        $className = end($tmp);
        $codeList = [];
        foreach ($this->statusScanPath as $path) {
            $files = $this->getPhpFiles($path);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                preg_match_all('/' . preg_quote($className . '::') . '(\w+)/', $content, $matches);

                if (!empty($matches[0])) {
                    $output->writeln("Found matches in file $file: " . implode(', ', $matches[0]));
                }

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $match) {
                        $codeList[$match] = true;
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

    protected function writeToFile(string $filePath, string $namespace, string $className, array $constants)
    {
        $template = <<<EOT
<?php
/**
 * Auto-generated file. Do not modify manually.
 * 
 * Error code format:
 * - -2xx00000
 * - Error codes are negative numbers, 8 digits in total.
 * - The first three digits represent the system, the middle two represent the service, and the last three represent the error code.
 */
namespace $namespace;

class $className
{
EOT;

        // 自动分组常量
        $groupedConstants = [];
        foreach ($constants as $name => $value) {
            $group = substr($name, 0, 3); // 以前三个字符分组
            $groupedConstants[$group][$name] = $value;
        }

        foreach ($groupedConstants as $group => $consts) {
            $template .= "\n    // Group: $group\n";
            foreach ($consts as $name => $value) {
                $template .= "    const $name = $value; // Error code for $name\n";
            }
        }

        $template .= "}\n";
        file_put_contents($filePath, $template);
    }
}
