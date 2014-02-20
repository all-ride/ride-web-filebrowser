<?php

namespace pallo\web\view\filebrowser;

use pallo\web\form\filebrowser\EditorForm;

use pallo\library\system\file\File;

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
     * @param pallo\web\form\filebrowser\EditorForm $form
     * @param string $path
     * @return null
     */
    public function __construct(EditorForm $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}