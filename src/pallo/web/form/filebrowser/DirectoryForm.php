<?php

namespace pallo\web\form\filebrowser;

use pallo\library\system\file\File;

/**
 * Form to create a directory
 */
class DirectoryForm extends AbstractForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'form-directory';

    /**
     * Constructs a new form
     * @param string $action URL where this form will point to
     * @param pallo\library\filesystem\File $path Path for the new directory
     * @return null
     */
    public function __construct($action, File $path = null) {
        parent::__construct($action, self::NAME, $path);
    }

}