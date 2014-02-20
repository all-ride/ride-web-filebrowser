<?php

namespace pallo\web\view\filebrowser;

use pallo\web\table\filebrowser\BrowserTable;
use pallo\web\table\filebrowser\ClipboardTable;

use pallo\library\html\Breadcrumbs;

/**
 * View for the file browser
 */
class BrowserView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'app/filebrowser/browser';

    /**
     * Constructs a new browser view
     * @param pallo\web\table\filebrowser\BrowserTable $browserTable Table with
     * the contents of the current directory
     * @param pallo\library\html\Breadcrumbs $breadcrumbs
     * @param pallo\web\table\filebrowser\ClipboardTable $clipboardTable Table
     * with the contents of the clipboard
     * @return null
     */
    public function __construct(BrowserTable $browserTable, $breadcrumbs, ClipboardTable $clipboardTable = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('browser', $browserTable);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->addJavascript(self::SCRIPT_TABLE);
        $this->addInlineJavascript($this->generateTableJavascript($browserTable));
        if ($clipboardTable) {
            $this->addInlineJavascript($this->generateTableJavascript($clipboardTable));
        }
    }

}