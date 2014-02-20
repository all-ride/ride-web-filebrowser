<?php

namespace pallo\web\table\filebrowser\decorator;

use pallo\library\system\File\File;
use pallo\library\html\table\Cell;
use pallo\library\html\table\Row;

/**
 * Decorator for a file in the clipboard
 */
class ClipboardFileDecorator extends AbstractFileDecorator {

    /**
     * The root of the file browser
     * @var pallo\library\system\file\File
     */
    private $root;

    /**
     * Constructs a new clipboard file decorator
     * @param pallo\library\system\file\File $root The root of the file browser
     * @return null
     */
    public function __construct(File $root) {
        $this->root = $root;
    }

    /**
     * Decorates the cell with the path of the file
     * @param pallo\library\html\table\Cell $cell Cell to decorate
     * @param pallo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the
     * remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $file = $cell->getValue();
       // $absoluteFile = new File($this->root, $file);
        $absoluteFile = $this->root->getChild($file);


        if (!$absoluteFile->exists()) {
            $cell->setValue('---');

            return;
        }

        if ($absoluteFile->isDirectory()) {
            $class = self::CLASS_DIRECTORY;
        } else {
            $class = self::CLASS_FILE;
        }

        $html = $this->getNameHtml($file->getPath(), $class);

        $cell->setValue($html);
    }

}