<?php

namespace pallo\web\model\filebrowser\filter;

use pallo\library\system\file\File;

/**
 * Filter interface for the FileBrowser object
 */
interface Filter {

    /**
     * Checks if the provided file is allowed by this filter
     * @param pallo\library\filesystem\File $file File to check
     * @return boolean True if the file is allowed, false otherwise
     */
    public function isAllowed(File $file);

}