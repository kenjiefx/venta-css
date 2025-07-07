<?php 

namespace Kenjiefx\VentaCSS\Tokens;

class MinifiedTokenPool {

    private const CHARS = 'abcdefghijklmnOPQRSTUVWXYZ';
    private static array $usedTokens = [];
    public function __construct(
        // Dependencies can be injected here if needed
    ) {}

    /**
     * Generates a unique minified token name. 
     * The name consists of three random characters from the defined set,
     * optionally followed by a numeric extension.
     * @param int $nameExt
     * @param string|null $baseName
     * @return string
     */
    public function generate(
        int $nameExt = 0,
        string|null $baseName = null
    ): string {
        $chars = str_split(self::CHARS);
        if ($baseName === null) {
            $baseName = $chars[rand(0,25)].
                        $chars[rand(0,25)].
                        $chars[rand(0,25)];
        }
        // Generate the minified name
        $minifiedName = ($nameExt > 0) ? $baseName.$nameExt : $baseName;

        // Check if the generated name is unique
        if (!in_array($minifiedName, static::$usedTokens)) {

            // If unique, add it to the used tokens and return
            array_push(static::$usedTokens, $minifiedName);

            // Finally, return the unique minified name
            return $minifiedName;
        }

        // If the name is not unique, increment the extension and try again
        $nameExt++;
        return $this->generate($nameExt, $baseName);
    }

}