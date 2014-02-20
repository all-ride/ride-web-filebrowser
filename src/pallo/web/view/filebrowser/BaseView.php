<?php

namespace pallo\web\view\filebrowser;

use pallo\web\view\BaseView as AppBaseView;

/**
 * Base view for the file browser
 */
class BaseView extends AppBaseView {

    /**
     * Path to the JS of this view
     * @var string
     */
    const SCRIPT_BROWSER = 'js/filebrowser/filebrowser.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_BROWSER = 'css/filebrowser/filebrowser.css';

    /**
     * Constructs a new base view for the file browser
     * @param string $template Path to the template of this view
     * @return null
     */
    public function __construct($template) {
        parent::__construct($template);

        $this->addStyle(self::STYLE_BROWSER);
    }

}