<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Services;

class ClassParser {

    private array $classStatements = [];
    private string $HTMLSource = '';
    private array $HTMLChars = [];
    private string $classStatement = ' class="';


    public function __construct()
    {

    }

    public function setHTMLSource(
        string $HTMLSource
        )
    {
        $this->HTMLSource = $HTMLSource;
        $this->HTMLChars = str_split($HTMLSource);
    }

    public function parse()
    {
        $classes = [];
        $classStatement = str_split($this->classStatement);
        $classStatementPointer = 0;
        $isRecording = false;
        $classList = '';

        foreach ($this->HTMLChars as $HTMLChar) {
            if ($isRecording && $HTMLChar!=='"') {
                $classList = $classList.$HTMLChar;
                continue;
            }
            if ($isRecording && $HTMLChar==='"') {
                
                $classes['class="'.$classList.'"'] = [
                    'classList' => explode(' ',$classList),
                    'minifiedClassNames' => explode(' ',$classList)
                ];
                $classList = '';
                $isRecording = false;
                $classStatementPointer = 0;
                continue;
            }
            if ($HTMLChar == $classStatement[$classStatementPointer]) {
                $classStatementPointer++;
            } else {
                $classStatementPointer = 0;
            }
            if ($classStatementPointer===count($classStatement)) {
                $isRecording = true;
            }
        }

        return $classes;
    }

}
