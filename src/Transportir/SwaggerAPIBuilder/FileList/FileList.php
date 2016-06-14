<?php
namespace Transportir\SwaggerAPIBuilder\FileList;

class FileList
{
    /** @var string */
    private $name;

    /** @var Files */
    private $files;

    /** @var FileList[] */
    private $subLists = [];

    public function __construct($name = 'root')
    {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('Invalid name for FileList');
        }

        $this->name = $name;
        $this->files = new Files();
    }

    public function getName() {
        return $this->name;
    }

    public function files() {
        return $this->files;
    }

    public function listSubLists() {
        return array_keys($this->subLists);
    }

    public function appendFileList(FileList $fileList) {
        $this->subLists[$fileList->getName()] = $fileList;
    }

    public function detachFileList(FileList $fileList) {
        if($this->hasFileList($fileList->getName())) {
            unset($this->subLists[$fileList->getName()]);
        }
    }

    public function hasFileList($name) {
        return isset($this->subLists[$name]);
    }

    public function withFileList($name) {
        if(!$this->hasFileList($name)) {
            $this->subLists[$name] = new FileList($name);
        }

        return $this->subLists[$name];
    }
}

class Files
{
    /** @var string[] */
    private $files = [];

    public function addFile($file) {
        if(!$this->hasFile($file)) {
            $this->files[$file] = true;
        }
    }

    public function hasFile($file) {
        return isset($this->files[$file]);
    }

    public function removeFile($file) {
        if($this->hasFile($file)) {
            unset($this->files[$file]);
        }
    }

    public function listFiles() {
        return array_keys($this->files);
    }
}