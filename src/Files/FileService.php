<?php 

namespace Kenjiefx\VentaCSS\Files;

class FileService {

    public function exists(AbsolutePath $file): bool {
        return file_exists($file->path);
    }

    public function read(AbsolutePath $file): string {
        if (!$this->exists($file)) {
            throw new \RuntimeException("File does not exist: " . $file->path);
        }
        return file_get_contents($file->path);
    }

    public function write(AbsolutePath $file, string $content): void {
        if (file_put_contents($file->path, $content) === false) {
            throw new \RuntimeException("Failed to write to file: " . $file->path);
        }
    }

    public function delete(AbsolutePath $file): void {
        if (!$this->exists($file)) {
            throw new \RuntimeException("File does not exist: " . $file->path);
        }
        if (!unlink($file->path)) {
            throw new \RuntimeException("Failed to delete file: " . $file->path);
        }
    }

    public function copy(AbsolutePath $source, AbsolutePath $destination): void {
        if (!$this->exists($source)) {
            throw new \RuntimeException("Source file does not exist: " . $source->path);
        }
        if (!copy($source->path, $destination->path)) {
            throw new \RuntimeException("Failed to copy file from " . $source->path . " to " . $destination->path);
        }
    }

    public function move(AbsolutePath $source, AbsolutePath $destination): void {
        if (!$this->exists($source)) {
            throw new \RuntimeException("Source file does not exist: " . $source->path);
        }
        if (!rename($source->path, $destination->path)) {
            throw new \RuntimeException("Failed to move file from " . $source->path . " to " . $destination->path);
        }
    }

}