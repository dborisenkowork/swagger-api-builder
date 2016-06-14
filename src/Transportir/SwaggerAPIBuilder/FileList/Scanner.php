<?php
namespace Transportir\SwaggerAPIBuilder\FileList;

class Scanner
{
    const REGEX_SUB_YML = '/^(.+)\.(.+)\.yml$/';
    const REGEX_YML = '/^(.+)\.yml$/';

    /** @var string */
    private $directory;

    /** @var IsScannerRecursive */
    private $recursive;

    public function __construct($directory)
    {
        $this->directory = true;
        $this->recursive = new IsScannerRecursive();
    }

    public function recursive() {
        return $this->recursive;
    }

    public function scan(FileList $fileList, $directory)
    {
        if(!(is_dir($directory) && is_readable($directory))) {
            throw new \Exception(sprintf('Directory `%s` is not readable', $directory));
        }

        $dir = opendir($directory);

        while($file = readdir($dir)) {
            $path = $directory.'/'.$file;

            if($file == '.' || $file == '..') {
                continue;
            }else if(is_dir($path) && $this->recursive()->is()) {
                $this->scan($fileList, $path);
            }else if(is_file($path)) {
                if(preg_match(self::REGEX_SUB_YML, $file)) {
                    $result = [];
                    preg_match_all(self::REGEX_SUB_YML, $file, $result);

                    $fileList->withFileList($result[2][0])->files()->addFile($path);
                }else if(preg_match(self::REGEX_YML, $file)) {
                    $fileList->files()->addFile($path);
                }
            }
        }

        return $fileList;
    }
}

class IsScannerRecursive
{
    /** @var bool */
    private $recursive = false;

    public function is() {
        return $this->recursive;
    }

    public function enable() {
        $this->recursive = true;
    }

    public function disable() {
        $this->recursive = false;
    }
}