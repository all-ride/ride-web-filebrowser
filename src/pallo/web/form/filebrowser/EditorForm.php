<?php

namespace pallo\web\form\filebrowser;

use pallo\library\system\file\File;
use pallo\library\html\form\field\TextField;

/**
 * Form for the editor
 */
class EditorForm extends AbstractForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'form-editor';

    /**
     * Name of the content field
     * @var string
     */
    const FIELD_CONTENT = 'content';

    /**
     * Constructs a new editor form
     * @param string $action URL where this form will point to
     * @param pallo\library\system\file\File $path File to edit or to create a new file, the directory of the new file
     * @return null
     */
    public function __construct($action, File $path = null, $name = null, $content = null) {
        parent::__construct($action, self::NAME, $path);

        $contentField = new TextField(self::FIELD_CONTENT, $content);

        $this->addField($contentField);

        $this->setValue(self::FIELD_NAME, $name);
    }

    /**
     * Gets the content of the form
     * @return string
     */
    public function getFileContent() {
        return $this->getValue(self::FIELD_CONTENT);
    }

}