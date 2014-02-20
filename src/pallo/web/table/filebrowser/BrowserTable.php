<?php

namespace pallo\web\table\filebrowser;

use pallo\library\html\table\decorator\ValueDecorator;
use pallo\library\html\table\FormTable;
use pallo\library\html\Element;
use pallo\library\i18n\translator\Translator;

use pallo\web\model\filebrowser\FileBrowser;
use pallo\web\table\filebrowser\decorator\FileDecorator;
use pallo\web\filebrowser\decorator\FileOptionDecorator;

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
     * @param pallo\web\model\filebrowser\FileBrowser $fileBrowser
     * @param pallo\library\i18n\translator\Translator $translator
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