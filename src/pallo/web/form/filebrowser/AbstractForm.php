<?php

namespace pallo\web\form\filebrowser;

use pallo\library\system\file\File;
use pallo\library\html\form\field\HiddenField;
use pallo\library\html\form\field\StringField;
use pallo\library\html\form\SubmitCancelForm;
use pallo\library\validation\validator\RequiredValidator;

/**
 * Form to with a hidden path field and a string name field
 */
abstract class AbstractForm extends SubmitCancelForm {

    /**
     * Name of the path field
     * @var string
     */
    const FIELD_PATH = 'path';

    /**
     * Name of the name field
     * @var string
     */
    const FIELD_NAME = 'name';

    /**
     * Constructs a new form
     * @param string $action URL where this form will point to
     * @param string $name Name of the form
     * @param pallo\library\system\file\File $path Path of the file or directory
     * @return null
     */
    public function __construct($action, $name, File $path = null) {
        parent::__construct($action, $name);

        $pathValue = '.';
        if ($path != null) {
            $pathValue = $path->getPath();
        }

        $requiredValidator = new RequiredValidator();

        $pathField = new HiddenField(self::FIELD_PATH, $pathValue);
        $pathField->addValidator($requiredValidator);

        $nameField = new StringField(self::FIELD_NAME);
        $nameField->addValidator($requiredValidator);

        $this->addField($pathField);
        $this->addField($nameField);
    }

    /**
     * Gets the path of the form
     * @return string
     */
    public function getFilePath() {
        return $this->getValue(self::FIELD_PATH);
    }

    /**
     * Gets the name of the form
     * @return string
     */
    public function getFileName() {
        return $this->getValue(self::FIELD_NAME);
    }

}