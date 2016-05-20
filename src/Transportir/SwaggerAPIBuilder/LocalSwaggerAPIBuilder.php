<?php
namespace Transportir\SwaggerAPIBuilder;

use Symfony\Component\Yaml\Yaml;
use Transportir\SwaggerAPIBuilder\FileList\FileList;
use Transportir\SwaggerAPIBuilder\FileList\Scanner;

class LocalSwaggerAPIBuilder implements SwaggerAPIBuilder
{
    /** @var string[] */
    private $directories = [];

    public function __construct(array $directories)
    {
        $this->validateDirectories($directories);

        $this->directories = $directories;
    }

    public function appendDirectory($directory)
    {
        if(!is_string($directory)) {
            throw new \InvalidArgumentException('Directory should be a string');
        }

        if(!(is_dir($directory) && is_readable($directory))) {
            throw new \InvalidArgumentException(sprintf('Path `%s` is not a directory or is not readable', $directory));
        }

        $this->directories[] = $directory;
    }

    public function detachDirectory($directory) {
        $this->directories = array_filter($this->directories, function($input) use($directory) {
            return $input !== $directory;
        });
    }

    public function listDirectories() {
        $this->validateDirectories($this->directories);

        return $this->directories;
    }

    private function validateDirectories($directories)
    {
        if(!is_array($directories)) {
            throw new \InvalidArgumentException('Directories should be an array of string');
        }

        foreach($directories as $directory) {
            if(!is_string($directory)) {
                throw new \InvalidArgumentException('Directories should be an array of string');
            }
        }
    }

    public function build()
    {
        $result = [];
        $fileList = $this->buildFileList();

        foreach($fileList->files()->listFiles() as $path) {
            $result = array_merge_recursive($result, Yaml::parse(file_get_contents($path)));
        }

        foreach($fileList->listSubLists() as $subList) {
            $result[$subList] = [];

            foreach($fileList->withFileList($subList)->files()->listFiles() as $path) {
                $result = array_merge_recursive($result[$subList], Yaml::parse(file_get_contents($path)));
            }
        }

        return $result;
    }

    private function buildFileList()
    {
        $this->validateDirectories($this->directories);

        $fileList = new FileList();

        foreach($this->directories as $directory) {
            $scanner = new Scanner($directory);
            $scanner->recursive()->enable();
            $scanner->scan($fileList, $directory);
        }

        return $fileList;
    }
}