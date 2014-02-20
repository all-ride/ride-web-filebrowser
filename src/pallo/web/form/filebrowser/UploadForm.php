<?php

namespace pallo\web\form\filebrowser;

use pallo\library\system\file\File;
use pallo\library\html\form\field\FileField;
use pallo\library\form\SubmitCancelForm;
use pallo\library\validation\validator\RequiredValidator;

/**
 * Form to upload a file
 */
class UploadForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'form-upload';

    /**
     * Name of the file field
     * @var string
     */
    const FIELD_FILE = 'file';

    /**
     * Constructs a new upload form
     * @param string $action URL where this form will point to
     * @param pallo\library\system\file\File $uploadPath Path to upload to
     * @return null
     */
    public function __construct($action, File $uploadPath) {
        parent::__construct($action, self::NAME);

        $fileField = new FileField(self::FIELD_FILE);
        $fileField->addValidator(new RequiredValidator());
        $fileField->setIsMultiple(true);
        $fileField->setUploadPath($uploadPath);
        $fileField->setWillOverwrite(true);

        $this->addField($fileField);
    }

    /**
     * Gets the names of the uploaded files
     * @return array
     */
    public function getFiles() {
        $values = $this->getValue(self::FIELD_FILE);

        if (!is_array($values)) {
            $values = array($values);
        }

        return $values;
    }

}