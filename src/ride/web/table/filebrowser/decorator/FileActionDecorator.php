<?php

namespace ride\web\table\filebrowser\decorator;

use ride\library\system\file\File;
use ride\library\html\table\decorator\AnchorDecorator;
use ride\library\html\table\Cell;
use ride\library\html\table\Row;
use ride\library\html\Anchor;


use ride\web\model\filebrowser\FileBrowser;

/**
 * Base decorator for a File action
 */
class FileActionDecorator extends AnchorDecorator {

    /**
     * Style class for a action cell
     * @var string
     */
    const CLASS_ACTION = 'action';

    /**
     * Style class for a button
     * @var string
     */
    const CLASS_BUTTON = 'btn';

    /**
     * The label for the button
     * @var string
     */
    private $label;

    /**
     * Instance of a file browser
     * @var ride\filebrowser\model\FileBrowser
     */
    private $filebrowser;

    /**
     * Constructs a new file action decorator
     * @param string $href The URL to the action
     * @param string $label Label of the button
     * @return null
     */
    public function __construct($href, $label, FileBrowser $fileBrowser) {
        parent::__construct($href);

        $this->label = $label;
        $this->filebrowser = $fileBrowser;
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
        $file = $this->filebrowser->getPath($file, false);
        $cell->setValue($file);
        if (!($file instanceof File)) {
            $cell->setValue('');

            return;
        }

        parent::decorate($cell, $row, $rowNumber, $remainingValues);
    }

    /**
     * Perform the actual decorating of the value
     * @param mixed $value Value to decorate
     * @return string Decorated value
     */
    protected function decorateValue($value) {
        return $this->label;
    }

    /**
     * Gets the href attribute for the anchor
     * @param mixed $value Value of the cell
     * @return string Href attribute for the anchor
     */
    protected function getHrefFromValue($value) {
        return $this->href . $value->getPath();
    }


    /**
     * Hook to perform extra processing on the generated anchor
     * @param ride\library\html\Anchor $anchor Generated anchor for the cell
     * @param mixed $value Value of the cell
     * @return null
     */
    protected function processAnchor(Anchor $anchor, $value) {
        parent::processAnchor($anchor, $value);

        $anchor->addToClass(self::CLASS_BUTTON);
    }

}