<?php

namespace ride\web\view\filebrowser;

use ride\web\form\filebrowser\DirectoryForm;

/**
 * View to create a directory
 */
class CreateView extends BaseFormView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'app/filebrowser/create';

    /**
     * Constructs a new create view
     * @param ride\library\html\form\Form $form
     * @param string $path
     * @return null
     */
    public function __construct(DirectoryForm $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}