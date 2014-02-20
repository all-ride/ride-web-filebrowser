<?php

namespace pallo\web\table\filebrowser;

use pallo\web\table\filebrowser\decorator\ClipboardFileDecorator;
//use pallo\web\table\filebrowser\decorator\FileOptionDecorator;
use pallo\web\filebrowser\decorator\FileOptionDecorator;
//use pallo\library\html\table\decorator;
use pallo\library\html\table\decorator\Decorator;

use pallo\library\system\file\File;
use pallo\library\html\table\decorator\ValueDecorator;
use pallo\library\html\table\FormTable;
use pallo\library\html\Element;

/**
 * Table to show the contents of the clipboard
 */
class ClipboardTable extends FormTable {

    /**
     * Name of the table form
     * @var string
     */
    const FORM_NAME = 'form-clipboard';

    /**
     * Constructs a new clipboard table
     * @param string $action URL where the table form will point to
     * @param pallo\library\system\file\File $root Path of the root for the filebrowser
     * @param array $files The values for the table: array with File objects
     * @return null
     */
    public function __construct($action, File $root, array $files) {
        parent::__construct($files, $action, self::FORM_NAME);

       // $this->addDecorator(new FileOptionDecorator());
        $this->addDecorator(new ClipboardFileDecorator($root));
    }


    /**
     * Gets the HTML of this table
     * @param string $part The part to get
     * @return string
     */
    public function getHtml($part = Element::FULL) {
        if (!$this->isPopulated && $this->actions) {
            $decorator = new ValueDecorator(null, new FileOptionDecorator());
            $decorator->setCellClass('option');

            $this->addDecorator($decorator, null, true);
        }

        return parent::getHtml($part);
    }

}