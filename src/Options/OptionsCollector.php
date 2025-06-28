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

    public function collect(): OptionIterator {
        if (static::$iterator !== null) {
            // If the iterator is already set, we do not need to collect options again.
            return static::$iterator;
        }
        $defaultOptions = $this->scanDir("default", __DIR__ . self::DEFAULT_DIR);
        static::$iterator = new OptionIterator($defaultOptions);
        return static::$iterator;
    }

    public function scanDir(string $theme, string $dir): array {
        // Collecting default options
        $files = $this->directoryService->listFiles($dir);
        $options = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $filePath = __DIR__ . self::DEFAULT_DIR . '/' . $file;
                $optionList = $this->getOptionFile($filePath);
                foreach ($optionList as $key => $option) {
                    $options[] = $this->optionFactory->create($theme, $key, $option);
                }
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