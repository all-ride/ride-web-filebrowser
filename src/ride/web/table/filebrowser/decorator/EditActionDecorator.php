<?php

namespace ride\web\table\filebrowser\decorator;

use ride\web\model\filebrowser\FileBrowser;

use ride\library\system\file\File;
use ride\library\html\table\Cell;
use ride\library\html\table\Row;

/**
 * Decorator to create a edit action of a File
 */
class EditActionDecorator extends FileActionDecorator {

    /**
     * Extensions which are allowed to be edited
     * @var array
     */
    private $extensions;

    /**
     * Constructs a new edit action decorator
     * @param string $action The URL to the edit action
     * @param string $label Label for the action
     * @param ride\filebrowser\model\FileBrowser $fileBrowser The file browser
     * @param array $extensions Array with extensions which are allowed to be edited
     * @return null
     */
    public function __construct($action, $label, FileBrowser $fileBrowser, array $extensions = array('txt' => 'txt')) {
        parent::__construct($action, $label, $fileBrowser);

        $this->fileBrowser = $fileBrowser;
        $this->extensions = $extensions;
    }

    /**
     * Decorates a table cell by setting an anchor to the cell based on the cell's value
     * @param ride\library\html\table\Cell $cell Cell to decorate
     * @param ride\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $cell->addToClass(self::CLASS_ACTION);

        $file = $cell->getValue();

        if (!$this->fileBrowser->exists($file) || $this->fileBrowser->isDirectory($file)) {
            $cell->setValue('');

            return;
        }

        $extension = $file->getExtension();
        if (isset($this->extensions[$extension])) {
            parent::decorate($cell, $row, $rowNumber, $remainingValues);
        } else {
            $cell->setValue('');
        }
    }

}