<?php

namespace pallo\web\model\filebrowser\filter;

use pallo\library\system\file\File;

use \Exception;

/**
 * Filter for paths
 */
class PathFilter implements Filter {

    /**
     * Flag to see if directories should be included
     * @var boolean
     */
    private $include;

    /**
     * Paths to allow or disallow
     * @var array
     */
    private $paths;

    /**
     * Constructs a new path filter
     * @param string|array $paths String or array with paths
     * @param boolean $include True to allow files with an path set to
     * this filter, false otherwise
     * @return null
     */
    public function __construct($paths, $include = true) {
        $this->setPaths($paths);
        $this->include = $include;
    }

    /**
     * Sets the paths for this filter
     * @param string|array $paths String or array with paths
     * @return null
     * @throws Exception if the provided paths variable is not a string
     * or array
     */
    private function setPaths($paths) {
        if (is_string($paths)) {
            $this->paths = array($paths);

            return;
        }

        if (is_array($paths)) {
            $this->paths = $paths;

            return;
        }

        throw new Exception('Provided paths is not a string and not an array');
    }

    /**
     * Checks if the provided file is allowed by this filter
     * @param pallo\library\filesystem\File $file File to check
     * @return boolean True if the file is allowed, false otherwise
     */
    public function isAllowed(File $file) {
        $result = !$this->include;

        $path = $file->getPath();
        if (in_array($path, $this->paths)) {
            $result = !$result;
        }

        return $result;
    }

}