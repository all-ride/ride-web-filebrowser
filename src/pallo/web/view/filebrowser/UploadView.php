<?php

namespace pallo\web\view\filebrowser;

use pallo\web\form\filebrowser\UploadForm;

use pallo\library\template\view\HtmlTemplateView;

/**
 * View to upload a file
 */
class UploadView extends HtmlTemplateView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'app/filebrowser/upload';

    /**
     * Constructs a new upload view
     * @param pallo\library\html\form\Form $form
     * @return null
     */
    public function __construct(UploadForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);

        $fileField = $form->getField(UploadForm::FIELD_FILE);
        $fileField->setAttribute('onChange', 'updateUploadForm(this)');

        $this->addJavascript(BaseView::SCRIPT_BROWSER);
    }

}