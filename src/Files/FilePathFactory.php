<?php

namespace Kenjiefx\VentaCSS\Files;

class FilePathFactory
{
    /**
     * Create an absolute file path.
     *
     * @param string $path
     * @return AbsolutePath
     * @throws \InvalidArgumentException
     */
    public function createAbsolutePath(string $path): AbsolutePath
    {
        if (!$this->isAbsolutePath($path)) {
            throw new \InvalidArgumentException("Provided path is not an absolute path: $path");
        }

        return new AbsolutePath($path);
    }

    /**
     * Create a relative file path.
     *
     * @param string $path
     * @return RelativePath
     * @throws \InvalidArgumentException
     */
    public function createRelativePath(string $path): RelativePath
    {
        if ($this->isAbsolutePath($path)) {
            throw new \InvalidArgumentException("Provided path is not a relative path: $path");
        }

        return new RelativePath($path);
    }

    /**
     * Determine if a path is absolute.
     *
     * @param string $path
     * @return bool
     */
    private function isAbsolutePath(string $path): bool
    {
        // Unix-style absolute path
        if (strpos($path, '/') === 0) {
            return true;
        }

        // Windows-style absolute path, e.g., C:\ or D:/
        if (preg_match('/^[a-zA-Z]:[\/\\\\]/', $path)) {
            return true;
        }

        return false;
    }
}
