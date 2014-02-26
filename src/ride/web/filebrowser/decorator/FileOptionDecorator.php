<?php
/**
 * Created by PhpStorm.
 * User: statik
 * Date: 05/02/14
 * Time: 17:09
 */

namespace ride\web\filebrowser\decorator;

use ride\library\decorator\Decorator;

use ride\library\html\table\FormTable;
use ride\library\system\file\File;

class FileOptionDecorator implements Decorator {

    /**
     * Decorates the cell with an option field for the table actions
     * @param ride\library\html\table\Cell $cell Cell which holds the data object
     * @param ride\library\html\table\Row $row Row of the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array with the values of the remaining rows of the table
     * @return null
     */
    public function decorate($value) {
        if (!$value instanceof File) {
            return '';
        }

        return '<input type="checkbox" name="' . FormTable::FIELD_ID . '[]" value="' . $value->getName() . '" />';
    }

} 