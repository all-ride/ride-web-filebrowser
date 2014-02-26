<?php

namespace ride\web\view\filebrowser;

use ride\web\table\filebrowser\ClipboardTable;

use ride\library\template\view\HtmlTemplateView;

/**
 * View for the clipboard of the file browser
 */
class ClipboardView extends HtmlTemplateView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'app/filebrowser/clipboard';

    /**
     * Constructs a new browser sidebar view
     * @param ride\web\table\filebrowser\ClipboardTable $clipboardTable Table
     * with the contents of the clipboard
     * @return null
     */
    public function __construct(ClipboardTable $clipboardTable = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('clipboard', $clipboardTable);

        $this->addJavascript(BaseView::SCRIPT_TABLE);
    }

}