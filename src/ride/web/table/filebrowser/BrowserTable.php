<?php

namespace ride\web\table\filebrowser;

use ride\library\html\table\decorator\ValueDecorator;
use ride\library\html\table\FormTable;
use ride\library\html\Element;
use ride\library\i18n\translator\Translator;

use ride\web\model\filebrowser\FileBrowser;
use ride\web\table\filebrowser\decorator\FileDecorator;
use ride\web\filebrowser\decorator\FileOptionDecorator;

/**
 * Table to show the contents of a directory
 */
class BrowserTable extends FormTable {

    /**
     * The name of the table
     * @var string
     */
    const NAME = 'form-browser';

    /**
     * Constructs a new file browser table
     * @param string $action URL where the table form should point to
     * @param array $files Files to show in the table
     * @param ride\web\model\filebrowser\FileBrowser $fileBrowser
     * @param ride\library\i18n\translator\Translator $translator
     * @param string $directoryAction URL to the action behind a directory
     * @param string $fileAction URL to the action behind a file
     */
    public function __construct($action, array $files, FileBrowser $fileBrowser, Translator $translator, $directoryAction = null, $fileAction = null) {
        parent::__construct($files, $action, self::NAME);
        $fileDecorator = new FileDecorator($fileBrowser, $translator);
        $fileDecorator->setDirectoryAction($directoryAction);
        $fileDecorator->setFileAction($fileAction);

        $this->addDecorator($fileDecorator);
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