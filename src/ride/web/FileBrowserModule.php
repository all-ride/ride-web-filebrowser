<?php

namespace ride\web;

use ride\web\base\view\MenuItem;

use ride\library\event\Event;



/**
 * File browser module
 */
class FileBrowserModule {

    /**
     * Route to the file browser
     * @var string
     */
    const ROUTE_FILE_BROWSER = 'filebrowser';

    /**
     * Translation key for the title of the file browser
     * @var string
     */
    const TRANSLATION_FILE_BROWSER = 'filebrowser.title';

    /**
     * Add the menu item for the file browser to the taskbar
     * @param ride\library\event\Event $event
     * @return null
     */
    public function prepareTaskbar(Event $event) {
        $taskbar = $event->getArgument("taskbar");

        $menuItem = new MenuItem();
        $menuItem->setTranslation(self::TRANSLATION_FILE_BROWSER);
        $menuItem->setRoute(self::ROUTE_FILE_BROWSER);

        $applicationsMenu = $taskbar->getApplicationsMenu();
        $applicationsMenu->addMenuItem($menuItem);
    }

}