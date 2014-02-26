<?php

namespace ride\web\model\filebrowser;

use ride\web\model\filebrowser\filter\Filter;

use ride\library\system\exception\FileSystemException;
use ride\library\system\file\File;

use \Exception;

/**
 * Read the filesystem with extra options
 */
class FileBrowser {

    /**
     * Default root path
     * @var string
     */
    const DEFAULT_PATH = '.';

    /**
     * The root path
     * @var ride\library\system\file\File
     */
    private $root;

    /**
     * The absolute path of the root
     * @var string
     */
    private $rootAbsolutePath;

    /**
     * Constructs a new file browser
     * @param ride\library\system\\fileFile $root The root of your browser
     * @return null
     */
    public function __construct(File $root) {
        $this->setRoot($root);
    }

    /**
     * Sets the root path of the browser
     * @param ride\library\system\file\File $root
     * @return null
     */
    public function setRoot(File $root) {
        $this->root = $root;
        $this->rootAbsolutePath = $root->getAbsolutePath();
    }

    /**
     * Gets the root path of the browser
     * @return ride\library\system\file\File
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * Reads from the filesystem with extra options
     * @param ride\library\system\file\File $path Path of the directory to read
     * @param array $filters Array with filters
     * @param boolean $recursive Set to true to read recursivly
     * @return array Array with the directories and files of the provided path
     */
    public function readDirectory(File $path = null, array $filters = array(), $recursive = false){
        if ($path) {
            if ($path->isAbsolute()) {
                if (strpos($path->getAbsolutePath(), $this->root->getAbsolutePath()) !== 0) {
                    throw new FileSystemException($path->getPath() . ' is not in the root directory of the file brwoser');
                }

                $path = $path;
            } else {
                $path = $this->root->getChild($path);
            }
        } else {
            $path = $this->root;
        }

        if (!$path->isDirectory()) {
            throw new FileSystemException($path->getPath() . ' is not a directory ');
        }
        if (!$path->isReadable()) {
            throw new FileSystemException($path->getPath() . ' is not readable ');
        }

        $paths = array();

        $files = $path->read($recursive);
        foreach ($files as $file) {
            $path = $this->getPath($file, false);
            $paths[$path->getPath()] = $path;
        }

        if ($filters) {
            $paths = $this->applyFilters($paths , $filters);
        }

        return $paths;


    }

    /**
     * Apply the provided filters on the provided files
     * @param array $files Array with files and directories relative to the
     * root of this browser
     * @param array $filters Array with file browser filters
     * @param array $filters Array with file browser filters
     * @return array Array with the files and directies which passed the
     * provided filters
     */
    public function applyFilters(array $files, array $filters) {
        $this->checkFilters($filters);

        $filteredFiles = array();
        foreach ($files as $key => $file) {
            if (!($file instanceof File)) {
                throw new Exception('Element at key ' . $key . ' is not an instance of ride\\library\\system\file\File');
            }

            $testFile = $this->root->getChild($file);


            $allow = true;
            foreach ($filters as $filter) {
                if (!$filter->isAllowed($testFile)) {
                    $allow = false;

                    break;
                }
            }

            if ($allow) {
                $filteredFiles[$key] = $file;
            }
        }

        return $filteredFiles;
    }

    /**
     * Checks if the provided array of filters are all valid filter objects
     * @param array $filters Array with so called filters
     * @return null
     * @throws ride\rideException when no filters provided
     * @throws ride\rideException when a invalid filter is in the provided array
     */
    private function checkFilters(array $filters) {
        if (!$filters) {
            throw new Exception('No filters provided');
        }

        foreach ($filters as $filter) {
            if (!($filter instanceof Filter)) {
                throw new Exception('Invalid filter provided, use instance of ride\\app\\model\\filebrowser\\filter\\Filter');
            }
        }
    }

    /**
     * Checks if a file exists
     * @param ride\library\system\file\File $path Path to check
     * @return boolean True if the path exists in the root of this browser,
     * false otherwise
     */
    public function exists(File $path) {
        if (!$path->isAbsolute()) {
            $path = $this->root->getChild($path);
        }

        return $path->exists();
    }

    /**
     * Checks if the provided path is a directory in the root of this browser
     * @param ride\library\system\file\File $path Path to check
     * @return boolean True if the path is a directory in the root of this browser, false otherwise
     */
    public function isDirectory(File $path) {
        if (!$path->isAbsolute()) {
            $path = $this->root->getChild($path);
        }

        return $path->isDirectory();
    }

    /**
     * Gets the size of the provided file
     * @param ride\library\system\file\File $path File to get the size of
     * @return integer The size of the file
     */
    public function getSize(File $path) {
        if (!$path->isAbsolute()) {
            $path = $this->root->getChild($path);
        }

        return $path->getSize();
    }

    /**
     * Checks if the provided file is in the provided root path
     * @param ride\library\system\file\File $root The root path
     * @param ride\library\system\file\File $file The file to check
     * @return boolean True if the file is in the root of the browser, false otherwise
     */
    public function isInRootPath(File $file) {
        $fileAbsolutePath = $file->getAbsolutePath();

        if (strpos($fileAbsolutePath, $this->rootAbsolutePath) === false) {
            return false;
        }

        return true;
    }

    /**
     * Gets the relative path of the provided file to the root path of the browser
     * @param ride\library\system\file\File $file File to get the path for
     * @param boolean $addRoot Set to false if the root of the browser is already added to the provided file
     * @return ride\library\system\file\File The relative path of the provided file
     */
    public function getPath(File $file = null, $addRoot = true) {
        if (!$file) {
            return $this->root;
        }


        if ($addRoot) {
            $file = $this->root->getChild($file);
        }
        $fileAbsolutePath = $file->getAbsolutePath();

        $file = str_replace($this->rootAbsolutePath, '', $fileAbsolutePath);
        if (!$file) {
            $file = self::DEFAULT_PATH;
        } else {
            if ($file[0] === File::DIRECTORY_SEPARATOR) {
                $file = substr($file, 1);
            }
        }

        return $this->root->getFileSystem()->getFile($file);
    }

    /**
     * Orders the provided files ascending by name
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered ascending by name
     */
    public function orderByNameAscending(array $files) {
        $files = $this->prepareFilesForComparison($files, true);

        usort($files, array($this, 'compareByNameAscending'));

        $files = $this->prepareFilesForComparison($files, false);

        return $files;
    }

    /**
     * Orders the provided files descending by name
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered descending by name
     */
    public function orderByNameDescending(array $files) {
        $files = $this->prepareFilesForComparison($files, true);

        usort($files, array($this, 'compareByNameDescending'));

        $files = $this->prepareFilesForComparison($files, false);

        return $files;
    }

    /**
     * Orders the provided files ascending by extension
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered ascending by extension
     */
    public function orderByExtensionAscending(array $files) {
        $files = $this->prepareFilesForComparison($files, true);

        usort($files, array($this, 'compareByExtensionAscending'));

        $files = $this->prepareFilesForComparison($files, false);

        return $files;
    }

    /**
     * Orders the provided files descending by extension
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered extension by name
     */
    public function orderByExtensionDescending(array $files) {
        $files = $this->prepareFilesForComparison($files, true);

        usort($files, array($this, 'compareByExtensionDescending'));

        $files = $this->prepareFilesForComparison($files, false);

        return $files;
    }

    /**
     * Orders the provided files ascending by size
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered ascending by size
     */
    public function orderBySizeAscending(array $files) {
        $files = $this->prepareFilesForComparison($files, true);

        usort($files, array($this, 'compareBySizeAscending'));

        $files = $this->prepareFilesForComparison($files, false);

        return $files;
    }

    /**
     * Orders the provided files descending by size
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered descending by size
     */
    public function orderBySizeDescending(array $files) {
        $files = $this->prepareFilesForComparison($files, true);

        usort($files, array($this, 'compareBySizeDescending'));

        $files = $this->prepareFilesForComparison($files, false);

        return $files;
    }

    private function prepareFilesForComparison(array $files, $addRoot) {
        $newFiles = array();

        foreach ($files as $file) {
            if ($addRoot) {
                if ($file->isAbsolute()) {
                    $newFiles[$file->getPath()] = $file;
                } else {
                    $newFiles[$file->getPath()] = $this->root->getChild($file);
                }
            } else {
                if ($file->isAbsolute()) {
                    $path = $file->getPath();
                    $path = str_replace($this->rootAbsolutePath . '/', '', $path);

                    $newFiles[$path] = $this->root->getChild($path);
                } else {
                    $newFiles[$file->getPath()] = $file;
                }
            }
        }

        return $newFiles;
    }

    private function compareByNameDescending($a, $b) {
        $compareDirectory = $this->compareDirectory($a, $b);
        if ($compareDirectory !== 0) {
            return $compareDirectory;
        }

        return strcasecmp($a->getName(), $b->getName()) * -1;
    }

    private function compareByNameAscending($a, $b) {
        $compareDirectory = $this->compareDirectory($a, $b);
        if ($compareDirectory !== 0) {
            return $compareDirectory;
        }

        return strcasecmp($a->getName(), $b->getName());
    }

    private function compareByExtensionDescending($a, $b) {
        $compareDirectory = $this->compareDirectory($a, $b);
        if ($compareDirectory !== 0) {
            return $compareDirectory;
        }

        $a = $a->getExtension() . '---' . $a->getName();
        $b = $b->getExtension() . '---' . $b->getName();

        return strcasecmp($a, $b) * -1;
    }

    private function compareByExtensionAscending($a, $b) {
        $compareDirectory = $this->compareDirectory($a, $b);
        if ($compareDirectory !== 0) {
            return $compareDirectory;
        }

        $a = $a->getExtension() . '---' . $a->getName();
        $b = $b->getExtension() . '---' . $b->getName();

        return strcasecmp($a, $b);
    }

    private function compareBySizeDescending($a, $b) {
        $compareDirectory = $this->compareDirectory($a, $b);
        if ($compareDirectory !== 0) {
            return $compareDirectory;
        }

        $a = $a->getSize();
        $b = $b->getSize();

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? 1 : -1;
    }

    private function compareBySizeAscending($a, $b) {
        $compareDirectory = $this->compareDirectory($a, $b);
        if ($compareDirectory !== 0) {
            return $compareDirectory;
        }

        $a = $a->getSize();
        $b = $b->getSize();

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    private function compareDirectory($a, $b) {
        if (!$a->exists()) {
            return +1;
        }

        if (!$b->exists()) {
            return -1;
        }

        $aIsDirectory = $a->isDirectory();
        $bIsDirectory = $b->isDirectory();

        if ($aIsDirectory && !$bIsDirectory) {
            return -1;
        }

        if ($bIsDirectory && !$aIsDirectory) {
            return +1;
        }

        return 0;
    }

}