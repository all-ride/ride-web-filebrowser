<?php

namespace pallo\web\table\filebrowser\decorator;

use pallo\library\system\file\File;
use pallo\library\html\form\field\OptionField;
use pallo\library\html\table\decorator\Decorator;
use pallo\library\html\table\Cell;
use pallo\library\html\table\FormTable;
use pallo\library\html\table\Row;

/**
 * Decorator to create an option field of a File
 */
class FileOptionDecorator implements Decorator {

    /**
     * Decorates the cell with the option for the File
     * @param pallo\library\html\table\Cell $cell Cell to decorate
     * @param pallo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $file = $cell->getValue();

        if (!($file instanceof File)) {
            return;
        }

        $field = new OptionField(FormTable::FIELD_ID, $file->getPath());
        $field->setIsMultiple(true);

        $cell->addToClass(FileActionDecorator::CLASS_ACTION);
        $cell->setValue($field->getHtml());
    }

}