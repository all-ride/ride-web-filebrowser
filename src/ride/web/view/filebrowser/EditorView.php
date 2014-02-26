<?php

namespace ride\web\view\filebrowser;

use ride\web\form\filebrowser\EditorForm;

use ride\library\system\file\File;

/**
 * View for the editor
 */
class EditorView extends BaseFormView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'app/filebrowser/editor';

    /**
     * Constructs a new editor view
     * @param ride\web\form\filebrowser\EditorForm $form
     * @param string $path
     * @return null
     */
    public function __construct(EditorForm $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}