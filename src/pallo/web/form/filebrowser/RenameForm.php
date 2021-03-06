<?php

namespace pallo\web\form\filebrowser;

use pallo\library\system\file\File;

/**
 * Form to rename a directory or file
 */
class RenameForm extends AbstractForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'form-rename';

    /**
     * Constructs a new form
     * @param string $action URL where this form will point to
     * @param pallo\library\filesystem\File $path
     * @return null
     */
    public function __construct($action, File $path) {
        parent::__construct($action, self::NAME, $path);

        $this->setValue(self::FIELD_NAME, $path->getName());
    }

}