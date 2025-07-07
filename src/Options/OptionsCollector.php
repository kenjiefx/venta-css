<?php 

namespace Kenjiefx\VentaCSS\Options;

use Kenjiefx\ScratchPHP\App\Directories\DirectoryService;
use Kenjiefx\VentaCSS\Files\FilePathFactory;
use Kenjiefx\VentaCSS\Files\FileService;

class OptionsCollector {

    public const DEFAULT_DIR = "/default";
    private static OptionIterator | null $iterator = null;

    public function __construct(
        public readonly DirectoryService $directoryService,
        public readonly FileService $fileService,
        public readonly FilePathFactory $filePathFactory,
        public readonly OptionFactory $optionFactory
    ) {}

    public function collect(
        string $customConfigDir
    ): OptionIterator {
        if (static::$iterator !== null) {
            // If the iterator is already set, we do not need to collect options again.
            return static::$iterator;
        }
        $aggregatedOptions = $this->scanDir("default", __DIR__ . self::DEFAULT_DIR);
        if ($this->directoryService->isDirectory($customConfigDir)) {
            $customOptions = $this->scanDir("default", $customConfigDir);
            array_push($aggregatedOptions, ...$customOptions);
        }
        static::$iterator = new OptionIterator($aggregatedOptions);
        return static::$iterator;
    }

    public function scanDir(string $theme, string $dir): array {
        // Collecting default options
        $files = $this->directoryService->listFiles($dir);
        $options = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $filePath = $dir . '/' . $file;
                $optionList = $this->getOptionFile($filePath);
                foreach ($optionList as $key => $option) {
                    $options[] = $this->optionFactory->create($theme, $key, $option);
                }
                continue;
            }
            $dirPath = "{$dir}/{$file}";
            $isDir = $this->directoryService->isDirectory($dirPath);
            if ($isDir) {
                $themeName = $file;
                $themedOptions = $this->scanDir($themeName, $dirPath);
                array_push($options, ...$themedOptions);
            }
        }
        return $options;
    }

    /**
     * Parses the option file and returns the options as an associative array.
     * @param string $filePath
     */
    private function getOptionFile(
        string $filePath
    ){
        $absFilePath = $this->filePathFactory->createAbsolutePath($filePath);
        $fileContent = $this->fileService->read($absFilePath);
        return json_decode($fileContent, true);
    }

}