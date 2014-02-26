<?php

namespace ride\web\model\filebrowser\filter;

use ride\library\system\file\File;

use \Exception;

/**
 * Filter for extensions
 */
class ExtensionFilter implements Filter {

    /**
     * Flag to see if directories should be included
     * @var boolean
     */
    private $include;

    /**
     * Extensions to allow or disallow
     * @var array
     */
    private $extensions;

    /**
     * Constructs a new extension filter
     * @param string|array $extensions String or array with extensions
     * @param boolean $include True to allow files with an extension set to
     * this filter, false otherwise
     * @return null
     */
    public function __construct($extensions, $include = true) {
        $this->setExtensions($extensions);
        $this->include = $include;
    }

    /**
     * Sets the extensions for this filter
     * @param string|array $extensions String or array with extensions
     * @return null
     * @throws Exception if the provided extensions variable is not a string
     * or array
     */
    private function setExtensions($extensions) {
        if (is_string($extensions)) {
            $this->extensions = array($extensions);

            return;
        }

        if (is_array($extensions)) {
            $this->extensions = $extensions;

            return;
        }

        throw new Exception('Provided extensions is not a string and not an array');
    }

    /**
     * Checks if the provided file is allowed by this filter
     * @param ride\library\filesystem\File $file File to check
     * @return boolean True if the file is allowed, false otherwise
     */
    public function isAllowed(File $file) {
        $result = !$this->include;

        $extension = $file->getExtension();
        if (in_array($extension, $this->extensions)) {
            $result = !$result;
        }

        return $result;
    }

}