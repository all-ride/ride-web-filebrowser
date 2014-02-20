<?php

namespace pallo\web\table\filebrowser\decorator;

use pallo\library\html\table\decorator\Decorator;

/**
 * Decorator for a file or directory in the browser
 */
abstract class AbstractFileDecorator implements Decorator {

    /**
     * Style class for a directory
     * @var string
     */
    const CLASS_DIRECTORY = 'directory';

    /**
     * Style class for a file
     * @var string
     */
    const CLASS_FILE = 'file';

    /**
     * Style class for a broken file
     * @var string
     */
    const CLASS_BROKEN = 'broken';

    /**
     * Gets the html of the file name
     * @param string $fileName Name of the file
     * @param string $class Style class for the name container
     * @param string $action URL for the action of the name
     * @return The HTML of the file name
     */
    protected function getNameHtml($fileName, $class, $action = null) {
        $html = '<div class="name ' . $class . '">';

        if ($action) {
            $html .= '<a href="' . $action . '/' . urlencode($fileName) . '">' . $fileName . '</a>';
        } else {
            $html .= $fileName;
        }

        $html .= '</div>';

        return $html;
    }

}