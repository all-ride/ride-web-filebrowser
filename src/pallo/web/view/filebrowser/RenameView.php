<?php

namespace pallo\web\view\filebrowser;

use pallo\web\form\filebrowser\RenameForm;

/**
 * View to rename a file or directory
 */
class RenameView extends BaseFormView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'app/filebrowser/rename';

    /**
     * Constructs a new rename view
     * @param pallo\web\form\filebrowser\RenameForm $form
     * @param string $path
     * @return null
     */
    public function __construct(RenameForm $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}