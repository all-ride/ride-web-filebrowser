<?php

namespace ride\web\table\filebrowser\decorator;

use ride\web\model\filebrowser\filter\DirectoryFilter;
use ride\web\model\filebrowser\FileBrowser;

use ride\library\decorator\Decorator;
use ride\library\decorator\StorageSizeDecorator;
use ride\library\system\file\File;
use ride\library\html\table\Cell;
use ride\library\html\table\Row;
use ride\library\i18n\translator\Translator;

use \Exception;

/**
 * Decorator for a file or directory in the browser
 */
class FileDecorator extends AbstractFileDecorator {

    /**
     * Translation key for the files label
     * @var string
     */
    const TRANSLATION_FILES = 'filebrowser.label.files';

    /**
     * Translation key for the size label
     * @var string
     */
    const TRANSLATION_SIZE = 'filebrowser.label.size';

    /**
     * Translation key for the subdirectories label
     * @var string
     */
    const TRANSLATION_SUBDIRECTORIES = 'filebrowser.label.subdirectories';

    /**
     * Decorator for file sizes
     * @var ride\library\decorator\Decorator
     */
    private $sizeDecorator;

    /**
     * Translator instance
     * @var ride\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * URL to the directory action
     * @var string
     */
    private $directoryAction;

    /**
     * URL to the file action
     * @var string
     */
    private $fileAction;

    /**
     * Constructs a new file decorator
     * @param ride\filebrowser\model\FileBrowser $fileBrowser The file browser we're working for
     * @return null
     */
    public function __construct(FileBrowser $fileBrowser, Translator $translator, Decorator $sizeDecorator = null) {
        if (!$sizeDecorator) {
            $sizeDecorator = new StorageSizeDecorator();
        }

        $this->fileBrowser = $fileBrowser;
        $this->translator = $translator;
        $this->sizeDecorator = $sizeDecorator;
    }

    /**
     * Sets the URL to the directory action
     * @param string $action URL to the directory action
     * @return null
     */
    public function setDirectoryAction($action) {
        $this->directoryAction = $action;
    }

    /**
     * Sets the URL to the file action
     * @param string $action URL to the file action
     * @return null
     */
    public function setFileAction($action) {
        $this->fileAction = $action;
    }

    /**
     * Decorates a table cell by setting an anchor to the cell based on the
     * cell's value
     * @param ride\library\html\table\Cell $cell Cell to decorate
     * @param ride\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the
     * remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $file = $cell->getValue();

        if (!$this->fileBrowser->exists($file)) {
            $html = $this->getNameHtml($file->getName(), self::CLASS_BROKEN);
        } elseif ($this->fileBrowser->isDirectory($file)) {
            $html = $this->getDirectoryHtml($file);
        } else {
            $html = $this->getFileHtml($file);
        }

        $cell->setValue($html);
    }

    /**
     * Gets the HTML for a directory
     * @param ride\library\filesystem\File $directory The directory to get the
     * HTML for
     * @return string The HTML of the directory
     */
    private function getDirectoryHtml(File $directory) {
        $action = null;

        try {
            $paths = $this->fileBrowser->readDirectory($directory);

            $directories = $this->fileBrowser->applyFilters($paths, array(new DirectoryFilter()));
            $files = $this->fileBrowser->applyFilters($paths, array(new DirectoryFilter(false)));

            $directories = count($directories);
            $files = count($files);

            $action = $this->directoryAction;
        } catch (Exception $e) {
            echo $e->getMessage() . 'sme';
            $directories = '---';
            $files = '---';
        }

        $html = $this->getNameHtml($directory->getName(), self::CLASS_DIRECTORY, $action);

        $html .= '<div class="help-block">';
        $html .= '<span class="size">' .
            $this->translator->translate(self::TRANSLATION_SUBDIRECTORIES) .
            ': ' . $directories . ' - ' .
            $this->translator->translate(self::TRANSLATION_FILES) .
            ': ' . $files .
            '</span>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Gets the HTML for a file
     * @param ride\library\system\file\File $file The file to get the HTML for
     * @return string The HTML of the file
     */
    private function getFileHtml(File $file) {
        $html = $this->getNameHtml($file->getName(), self::CLASS_FILE, $this->fileAction);
        $html .= '<div class="help-block">';
        $html .= '<span class="size">' .
            $this->translator->translate(self::TRANSLATION_SIZE) .
            ': ' . $this->sizeDecorator->decorate($this->fileBrowser->getSize($file)) .
            '</span>';
        $html .= '</div>';

        return $html;
    }

}