<?php

namespace pallo\web\controller;

use pallo\library\i18n\I18n;

use pallo\web\model\filebrowser\filter\PathFilter;
use pallo\web\model\filebrowser\filter\Filter;
use pallo\web\model\filebrowser\FileBrowser;

use pallo\web\table\filebrowser\decorator\EditActionDecorator;
use pallo\web\table\filebrowser\decorator\FileActionDecorator;
use pallo\web\table\filebrowser\BrowserTable;
use pallo\web\table\filebrowser\ClipboardTable;

use pallo\web\FileBrowserModule;

use pallo\library\archive\Archive;
use pallo\library\html\Breadcrumbs;
use pallo\library\http\Response;
use pallo\library\validation\exception\ValidationException;
use pallo\library\validation\ValidationError;
use pallo\library\system\file\FileSystem;
use pallo\library\system\file\File;
use pallo\library\String;


use pallo\web\base\controller\AbstractController;

use \Exception;

/**
 * Controller of the file browser application
 */
class FileBrowserController extends AbstractController
{

    /**
     * Route to create a directory
     * @var string
     */
    const ROUTE_CREATE = 'filebrowser.create';

    /**
     * Route to download a file
     * @var string
     */
    const ROUTE_DOWNLOAD = 'filebrowser.download';

    /**
     * Route to edit a file
     * @var string
     */
    const ROUTE_EDIT = 'filebrowser.edit';

    /**
     * Route to view a path
     * @var string
     */
    const ROUTE_PATH = 'filebrowser.path';

    /**
     * Route to rename a file
     * @var string
     */
    const ROUTE_RENAME = 'filebrowser.rename';

    /**
     * The default archive extension
     * @var string
     */
    const ARCHIVE_EXTENSION = 'zip';

    /**
     * URL query argument for the order field
     * @var string
     */
    const ARGUMENT_ORDER_FIELD = 'orderField';

    /**
     * URL query argument for the order direction
     * @var string
     */
    const ARGUMENT_ORDER_DIRECTION = 'orderDirection';

    /**
     * Default directory in the public
     * @var string
     */
    const DEFAULT_DIRECTORY = 'upload';

    /**
     * Parameter for the extension of editable files
     * @var string
     */
    const PARAM_EXTENSIONS = 'filebrowser.extension';

    /**
     * Parameter for the root
     * @var string
     */
    const PARAM_ROOT = 'filebrowser.path.default';

    /**
     * Session key for the contents of the clipboard
     * @var string
     */
    const SESSION_CLIPBOARD = 'filebrowser.clipboard';

    /**
     * Translation key for the file browser description
     * @var string
     */
    const TRANSLATION_DESCRIPTION = 'filebrowser.description';

    /**
     * Translation key for the create directory button
     * @var string
     */
    const TRANSLATION_CREATE_DIRECTORY = 'filebrowser.button.create.directory';

    /**
     * Translation key for the create file button
     * @var string
     */
    const TRANSLATION_CREATE_FILE = 'filebrowser.button.create.file';

    /**
     * Translation key for the table action to copy items to the clipboard
     * @var string
     */
    const TRANSLATION_CLIPBOARD_COPY = 'filebrowser.button.copy.clipboard';

    /**
     * Translation key for the table action to remove items from the clipboard
     * @var string
     */
    const TRANSLATION_CLIPBOARD_REMOVE = 'filebrowser.button.remove.clipboard';

    /**
     * Translation key for the table action to copy items from the clipboard
     * @var string
     */
    const TRANSLATION_COPY = 'filebrowser.button.copy';

    /**
     * Translation key for the edit button
     * @var string
     */
    const TRANSLATION_EDIT = 'button.edit';

    /**
     * Translation key for the editor title
     * @var string
     */
    const TRANSLATION_EDITOR = 'filebrowser.title.editor';

    /**
     * Translation key for the table action to move items from the clipboard
     * @var string
     */
    const TRANSLATION_MOVE = 'filebrowser.button.move';

    /**
     * Translation key for the rename button
     * @var string
     */
    const TRANSLATION_RENAME = 'filebrowser.button.rename';

    /**
     * Translation key for the table action to delete items
     * @var string
     */
    const TRANSLATION_DELETE = 'filebrowser.button.delete';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_CONFIRM_DELETE = 'filebrowser.label.delete.confirm';

    /**
     * Translation key for the table action to download the selected items in a archive
     * @var string
     */
    const TRANSLATION_DOWNLOAD_ARCHIVE = 'filebrowser.button.download.archive';

    /**
     * Translation key for the home crumb of the breadcrumbs
     * @var string
     */
    const TRANSLATION_NAVIGATION_HOME = 'filebrowser.label.navigation.home';

    /**
     * Translation key for the name field in the order selection
     * @var string
     */
    const TRANSLATION_NAME = 'filebrowser.label.name';

    /**
     * Translation key for the extension field in the order selection
     * @var string
     */
    const TRANSLATION_EXTENSION = 'filebrowser.label.extension';

    /**
     * Translation key for the size field in the order selection
     * @var string
     */
    const TRANSLATION_SIZE = 'filebrowser.label.size';

    /**
     * Translation key for the error message when a path exists already
     * @var string
     */
    const TRANSLATION_ERROR_EXIST = 'filebrowser.error.exist';

    /**
     * Translation key for the error message when a path does not exist
     * @var string
     */
    const TRANSLATION_ERROR_EXIST_NOT = 'filebrowser.error.exist.not';

    /**
     * Translation key for the error message when a path is not writable
     * @var string
     */
    const TRANSLATION_ERROR_WRITABLE = 'filebrowser.error.writable';

    /**
     * Translation key for the error message when the changed path is a file
     * @var string
     */
    const TRANSLATION_ERROR_PATH_FILE = 'filebrowser.error.path.file';

    /**
     * Translation key for the error message when deleting files
     * @var string
     */
    const TRANSLATION_ERROR_DELETED = 'filebrowser.error.deleted';

    /**
     * Translation key for the information message when creating a directory
     * @var string
     */
    const TRANSLATION_SUCCESS_CREATED = 'filebrowser.information.created';

    /**
     * Translation key for the information message when renaming a file or directory
     * @var string
     */
    const TRANSLATION_SUCCESS_RENAMED = 'filebrowser.information.renamed';

    /**
     * Translation key for the information message when saving a file
     * @var string
     */
    const TRANSLATION_SUCCESS_SAVED = 'filebrowser.information.saved';

    /**
     * Translation key for the information message when uploading files
     * @var string
     */
    const TRANSLATION_SUCCESS_UPLOADED = 'filebrowser.information.uploaded';

    /**
     * Translation key for the information message when deleting files
     * @var string
     */
    const TRANSLATION_SUCCESS_DELETED = 'filebrowser.information.deleted';

    /**
     * Root of the file browser
     * @var pallo\library\system\file\File
     */
    protected $root;

    /**
     * Current path
     * @var pallo\library\system\file\File
     */
    protected $path;

    /**
     * Instance of a file browser
     * @var pallo\filebrowser\model\FileBrowser
     */
    protected $fileBrowser;

    /**
     * Files of the clipboard
     * @var array
     */
    protected $clipboard;

    /**
     * Extensions which are editable
     * @var array
     */
    protected $extensions;

    /**
     * Filters for the files
     * @var array
     */
    protected $filters;

    /**
     * Ids of the used routes
     * @var array
     */
    protected $routes;


    protected $form;

    protected $clipboardForm;

    /**
     * Constructs a new file browser controller
     * @return null
     */
    public function __construct()
    {
        $this->root = null;
        $this->path = null;
        $this->fileBrowser = null;
        $this->clipboard = array();
        $this->extensions = array();
        $this->filter = array();
        $this->routes = array(
            self::ROUTE_PATH => self::ROUTE_PATH,
            self::ROUTE_DOWNLOAD => self::ROUTE_DOWNLOAD,
            self::ROUTE_CREATE => self::ROUTE_CREATE,
            self::ROUTE_EDIT => self::ROUTE_EDIT,
            self::ROUTE_RENAME => self::ROUTE_RENAME,
        );
    }

    /**
     * Sets the root path of the browser
     * @param pallo\library\system\file\File $root
     * @return null
     */
    public function setRoot(File $root)
    {
        $this->root = $root;
    }

    /**
     * Gets the root path of the browser
     * @return pallo\library\system\file\File
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets a route for an action
     * @param string $action Action is the original route id
     * @param string $route Id of the new route
     * @return null
     */
    public function setRoute($action, $route)
    {
        $this->routes[$action] = $route;
    }

    /**
     * Prefixes the route ids with the provided prefix
     * @param string $prefix
     * @return null
     */
    public function setRoutePrefix($prefix)
    {
        foreach($this->routes as $action => $route) {
            $this->routes[$action] = $prefix . $route;
        }
    }

    /**
     * Sets the extensions which are editable
     * @param array $extensions
     * @return null
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Gets the extensions which are editable
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Sets a filter for the files displayed in the browser
     * @param pallo\web\model\filebrowser\filter\Filter $filter
     * @return null
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Hook before every action, reads the clipboard and the current path
     * @return boolean True to invoke the action
     */
    public function preAction()
    {

        if(!$this->root) {
            $fileBrowser = $this->dependencyInjector->get('pallo\\library\\system\\file\\browser\\FileBrowser');

            $this->root = $this->config->get(self::PARAM_ROOT);
            if($this->root) {
                $this->root = $fileBrowser->getFileSystem()->getFile($this->root);
            } else {
                $this->root = $fileBrowser->getPublicDirectory()->getChild(self::DEFAULT_DIRECTORY);

                if(!$this->filters) {
                    $path = $this->root->getAbsolutePath() . '/';

                    $paths = array(
                        $path . 'cache',
                        $path . '.htaccess',
                        $path . 'index.php',
                    );

                    $this->filters[] = new PathFilter($paths, false);
                }
            }

            if(!$this->root->exists()) {
                $this->root->create();
            }
        }

        $this->fileBrowser = new FileBrowser($this->root);

        if(!$this->extensions) {
            $this->extensions = $this->config->get(self::PARAM_EXTENSIONS, array());
        }

        if($this->request->hasSession()) {
            $session = $this->request->getSession();

            $this->clipboard = $session->get(self::SESSION_CLIPBOARD, array());
        }

        return true;
    }

    /**
     * Hook after every action, stores the clipboard and the current path
     * @return null
     */
    public function postAction()
    {
        $session = $this->request->getSession();
        $session->set(self::SESSION_CLIPBOARD, $this->clipboard);
    }

    /**
     * Default action of the file browser
     * @return null
     */
    public function indexAction(FileSystem $fileSystem)
    {

        return $this->pathAction($fileSystem);
    }

    /**
     * Action to view the contents of a path
     * Every argument to this method is a part of the path. eg.
     * $fb->pathAction('application', 'config') would display application/config
     * @return null
     */
    public function pathAction(FileSystem $fileSystem)
    {
        $tokens = func_get_args();
        array_shift($tokens);


        $path = $this->getFileFromTokens($tokens, false);
        if($path) {
            $absolutePath = $this->fileBrowser->getRoot()->getChild($path);
        } else {
            $absolutePath = $this->getRoot();
        }

        $this->path = $this->fileBrowser->getPath($path);


        if(!$absolutePath->exists() || !$absolutePath->isDirectory()) {
            $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);

            return;
        }

        /*$pathAction = $this->getUrl($this->routes[self::ROUTE_PATH]);
        $tableAction = $pathAction . ($path ? '/' . $this->fileBrowser->getPath($path, true) : '');
*/

        $pathAction = $this->getUrl($this->routes[self::ROUTE_PATH]);
        $tableAction = $pathAction . ($path ? '/' . $path : '');
        //$clipboardAction = $pathAction .($path ? '/' . $path : '');

        $downloadAction = $this->getUrl($this->routes[self::ROUTE_DOWNLOAD]) . ($path ? '/' . $path : '');
        $editAction = $this->getUrl($this->routes[self::ROUTE_EDIT]) . '/';
        $createAction = $this->getUrl($this->routes[self::ROUTE_CREATE]);
        $renameAction = $this->getUrl($this->routes[self::ROUTE_RENAME]) . '/';

        $editLink = $this->getUrl($this->routes[self::ROUTE_EDIT]) . ($path ? '/' . $path : '');
        $createLink = $this->getUrl($this->routes[self::ROUTE_CREATE]) . ($path ? '/' . $path : '');

        $actions = array();
        array_push($actions, $createLink);
        array_push($actions, $editLink);


        $orderField = $this->request->getQueryParameter(self::ARGUMENT_ORDER_FIELD);
        $orderDirection = $this->request->getQueryParameter(self::ARGUMENT_ORDER_DIRECTION);;
        $browserTable = $this->getBrowserTable($tableAction, $path, $tableAction, $downloadAction, $renameAction, $editAction, $orderField, $orderDirection);

        $clipboardTable = null;
        if($this->clipboard) {
            $clipboardTable = $this->getClipboardTable($tableAction);
        }

        if($this->response->willRedirect() || $this->response->getView()) {

            return;
        }

        $translator = $this->getTranslator();


        $breadcrumbs = array();
        $breadcrumbs[$pathAction] = $translator->translate(self::TRANSLATION_NAVIGATION_HOME);

        $upload = $this->createFormBuilder();
        $upload->addRow("files", "collection", array(
            'type' => 'file',
            'options' => array(
                'path' => $absolutePath,
            ),
            'validators' => array(
                'required' => array(),
            ),
        ));
        $upload->addRow('path', 'hidden', array(
            'path' => $absolutePath,
        ));
        $upload->setRequest($this->request);
        $upload = $upload->build();

        $query = $this->request->getQueryParameter('query');


        if($upload->isSubmitted($this->request) && $this->request->getBodyParameter('upload')) {

            try {
                $upload->validate();
                // $data = $upload->getData();

                $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]) . ($path ? '/' . $path : '');
                $this->response->setRedirect($redirectUrl);

            } catch(ValidationException $exception) {
                $this->response->setStatusCode(Response::STATUS_CODE_UNPROCESSABLE_ENTITY);
                $this->addError('error.validation');
            }


        }


        $breadcrumbPath = '';
        foreach($tokens as $token) {
            $breadcrumbPath = '/' . $token;
            $breadcrumbs[$pathAction . $breadcrumbPath] = $token;
            $pathAction = $pathAction . $breadcrumbPath;
        }


        if(!empty($this->clipboard)){
            $variables = array(
                "breadcrumbs" => $breadcrumbs,
                "browser" => $browserTable,
                "tableForm" => $this->form->getView(),
                "tableAction" => $tableAction,
                "actionField" => $pathAction,
                'form' => $upload->getView(),
                'clipboard' => $clipboardTable,
                'clipboardForm' => $this->clipboardForm->getView(),
                'actions' => $actions,
                'query' => $query,
            );
        }
        else{
            $variables = array(
                "breadcrumbs" => $breadcrumbs,
                "browser" => $browserTable,
                "tableForm" => $this->form->getView(),
                "tableAction" => $tableAction,
                "actionField" => $pathAction,
                'form' => $upload->getView(),
                'actions' => $actions,
                'clipboard' => $clipboardTable,
                'query' => $query,

            );
        }

        $view = $this->setTemplateView('app/filebrowser/browser', $variables );


        if(!$absolutePath->isWritable()) {
            $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $path));

        }

    }


    /**
     * Action to download a file.
     *
     * Every argument to this method is a part of the file name. eg.
     * $fb->downloadAction('application', 'config', 'system.ini') would access application/config/system.ini
     * @return null
     */
    public function downloadAction()
    {
        $tokens = func_get_args();
        $file = $this->getFileFromTokens($tokens);


        if(!$file->exists() || $file->isDirectory()) {
            $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
        } else {
            $this->setDownloadView($file);
        }
    }

    /**
     * Action to create a new directory
     *
     * Every argument to this method is a part of the create path. eg.
     * $fb->createAction('application', 'test') would create application/test
     * @return null
     */
    public function createAction()
    {
        $tokens = func_get_args();
        $path = $this->getFileFromTokens($tokens, true);

        if($tokens) {
            $absolutePath = $path;
        } else {
            $absolutePath = $this->getRoot();
        }

        $data = array(
            'path' => $absolutePath,
        );

        $form = $this->createFormBuilder($data);

        $form->addRow('path', 'label', array(
            'path' => $absolutePath,

        ));
        $form->addRow('name', 'string', array(
            'type' => 'string',
            'options' => array(
                'path' => $path
            ),
            'validators' => array(
                'required' => array(),
            ),
        ));
        $form->setRequest($this->request);
        $form = $form->build();

        $view = $this->setTemplateView('app/filebrowser/create', array(
            'form' => $form->getView(),
        ));

        if($form->isSubmitted($this->request)) {

            try {
                $form->validate();

                $formdata = $form->getData();
                $name = $formdata["name"];

                $newPath = $this->getRoot()->getPath($absolutePath);

                //mkdir($absolutePath . '/' . $name, 0777);

                 $newPath = $newPath->getCopyFile();
                $newPath->create();

                $this->addSuccess(self::TRANSLATION_SUCCESS_CREATED, array('path' => $this->fileBrowser->getPath($newPath, false)));

                $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]) . ($path ? '/' . $path : '');
                $this->response->setRedirect($redirectUrl);

                return;
            } catch(ValidationException $e) {
                $this->response->setStatusCode(Response::STATUS_CODE_BAD_REQUEST);

            } catch(Exception $exception) {
                /*   $log = $this->zibo->getLog();
                   if ($log) {
                       $log->logException($exception);
                   }

                   $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));
   */
                $this->response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
                $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]);
                $this->response->setRedirect($redirectUrl);
            }
        }

          if (!$absolutePath->isWritable()) {
              $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $this->fileBrowser->getPath($path)));

              $form->setIsDisabled(true, DirectoryForm::BUTTON_SUBMIT);
          }


        $translator = $this->getTranslator();

    }

    /**
     * Action to rename a file or directory
     *
     * Every argument to this method is a part of the rename path. eg.
     * $fb->renameAction('application', 'data', 'test.txt') would rename to application/data/test.txt
     * @return null
     */
    public function renameAction()
    {
        $tokens = func_get_args();
        $file = $this->getFileFromTokens($tokens, false);
        $path = $this->getRoot()->getChild($file);

        if(!$file) {
            $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);

            return;
        } else {
            $absoluteFile = $this->getRoot()->getChild($file);

            if(!$absoluteFile->exists()) {
                $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);

                return;
            }
        }

        $parent = $absoluteFile->getParent();

        $data = array(
            'oldvalue' => $file->getName(),
            'newvalue' => $file->getName(),
        );

        $form = $this->createFormBuilder($data);

        $form->addRow('oldvalue', 'label', array(
            'oldvalue' => $file->getName(),
        ));
        $form->addRow('newvalue', 'string', array(
            'validators' => array(
                'required' => array(),
            ),

        ));

        $form->setRequest($this->request);
        $form = $form->build();


        $view = $this->setTemplateView('app/filebrowser/rename', array(
            'form' => $form->getView(),
        ));

        if($form->isSubmitted()) {

            try {
                $form->validate();


                $dataform = $form->getData();

                echo 'submit';

                $name = $dataform['newvalue'];
                //$name = String::safeString($name);
                //   $destination = new File($parent, $name);
                $destination = $parent->getChild($dataform['newvalue']);

                if($destination->getAbsolutePath() != $absoluteFile->getPath() && $destination->exists()) {
                    $error = new ValidationError(self::TRANSLATION_ERROR_EXIST, '%path% exists already', array('path' => $this->fileBrowser->getPath($destination, false)));

                    $exception = new ValidationException();
                    // $exception->addErrors(RenameForm::FIELD_NAME, array($error));

                    throw $exception;
                }

                $absoluteFile->move($destination);

                $this->addSuccess(self::TRANSLATION_SUCCESS_RENAMED, array('old' => $file->getName(), 'new' => $name));

                $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]) . '/' . $this->fileBrowser->getPath($parent, false);
                $this->response->setRedirect($redirectUrl);

                return;
            } catch(ValidationException $exception) {
                $form->setValidationException($exception);

                $this->response->setStatusCode(Response::STATUS_CODE_BAD_REQUEST);

            } catch(Exception $exception) {
                /*   $log = $this->zibo->getLog();

                    if ($log) {

                        $log->logException($exception);

                    }
    */

                $this->addError(self::TRANSLATION_ERROR_PATH_FILE, array('error' => $exception->getMessage()));

                $this->response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
            }
        }

        if(!$absoluteFile->isWritable() || !$parent->isWritable()) {
            $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $file));
            $form->setIsDisabled(true, RenameForm::BUTTON_SUBMIT);
        }

        $translator = $this->getTranslator();


    }


    /**
     * Action to edit or create a file
     *
     * Every argument to this method is a part of the file to edit. eg.
     * $fb->editAction('application', 'data', 'test.txt') would show the editor for application/data/test.txt
     *
     * To create a new file in a directory, give the arguments to a directory instead of a file.
     * @return null
     */
    public function editAction() {
        $tokens = func_get_args();
        $path = $this->getFileFromTokens($tokens, false);

        if($tokens) {
            $absolutePath = $this->getRoot()->getChild($path);
            $file = $this->getRoot()->getChild($path);
        } else {
            $absolutePath = $this->getRoot();
            $file = $this->getRoot();
        }

        $name = null;
        $content = null;

        if($path) {
            if(!$absolutePath->exists()) {
                $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);

                return;
            }

            if(!$absolutePath->isDirectory()) {
                $name = $absolutePath->getName();
                $path = $absolutePath->getParent();
                $content = $absolutePath->read();

            } else {
                $path = $absolutePath;
            }
        } else {
            $path = $absolutePath;
        }

        $isWritable = $absolutePath->isWritable();

        $data = array(

            'path' => $path,
            'content' => $content,
            'name' => $name,

        );

        $form = $this->createFormBuilder($data);

        $form->addRow('path', 'label', array(
            'path' => $absolutePath,

        ));
        $form->addRow('name', 'string', array(
            'type' => 'label',
            'options' => array(
                'path' => $absolutePath
            ),
            'validators' => array(
                'required' => array(),
            ),
        ));
        $form->addRow('content', 'text', array(
            'type' => 'text',
            'options' => array(
                'path' => $absolutePath
            ),
            'validators' => array(
                'required' => array(),
            )
        ));
        $form->setRequest($this->request);
        $form = $form->build();

        if($form->isSubmitted()) {
            $formdata = $form->getData();

            $content = $formdata['content'];
            $name = $formdata['name'];
            $path = $absolutePath->getParent();
            try {
                $form->validate();


               if(!file_exists($path.'/'.$name)){
                   $file = $name;
                   $path = $this->getRoot()->getChild($file);var_dump($path);
                   $new = fopen($path, 'x+');
                   fwrite($new,$content);
                   fclose($new);


               }


                elseif($file->isWritable()) {
                    $file->write($content);
                    $this->addSuccess(self::TRANSLATION_SUCCESS_SAVED, array('path' => $this->fileBrowser->getPath($file, false)));


                }
                else {
                    $this->addError(self::TRANSLATION_ERROR_WRITABLE, array('path' => $this->fileBrowser->getPath($file, false)));

                    $form->setIsDisabled(true, EditorForm::BUTTON_SUBMIT);

                    $isWritable = true;
                }
                $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]);
                $this->response->setRedirect($redirectUrl);
            } catch(ValidationException $exception) {
                $this->response->setStatusCode(Response::STATUS_CODE_BAD_REQUEST);
            } catch(Exception $exception) {
                /*   $log = $this->pallo->getLog();
                   if($log) {
                       $log->logException($exception);
                   }*/

//            $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));

                $this->response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
            }


            if(!$isWritable) {
                $form->setIsDisabled(true, EditorForm::BUTTON_SUBMIT);
                $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $path . ($name ? '/' . $name : '')));
            }

            $translator = $this->getTranslator();

        }

        $view = $this->setTemplateView("app/filebrowser/editor", array(
            'form' => $form->getView(),

        ));



    }

    /**
     * Deletes files and directories
     * @param string|array $files String with the filename, relative to the
     * root path of theor an array of filenames
     * @return null
     */
    public function delete($files = null)
    {

        if($files == null) {
            return;
        }

        if(!is_array($files)) {
            $files = array($files);
        }

        foreach($files as $file) {

            $file = $this->fileBrowser->getRoot()->getChild($file);


            try {
                $file->delete();
            } catch(Exception $exception) {
                $this->addError(self::TRANSLATION_ERROR_DELETED, array('error' => $exception->getMessage()));
            }

            $path = $this->fileBrowser->getPath($file, false)->getPath();
            if(array_key_exists($path, $this->clipboard)) {
                unset($this->clipboard[$path]);
            }

            $this->addSuccess(self::TRANSLATION_SUCCESS_DELETED, array('path' => $path));
        }

        $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]);
        $this->response->setRedirect($redirectUrl);
    }

    /**
     * Downloads a set of files and directories in an archive
     * @param array $files The files and directories to download
     * @return null
     */
    public function archive(array $files = null)
    {
        if(!$files) {
            $this->response->setRedirect($this->getReferer());

            return;
        }

        $path = $this->fileBrowser->getPath($this->path);

        $name = $path->getName();
        if($name == FileBrowser::DEFAULT_PATH) {
            $name = $this->getTranslator()->translate(BrowserTable::TRANSLATION_NAVIGATION_HOME);
        }
        $name .= '.' . self::ARCHIVE_EXTENSION;

        $archiveFile = new File($this->pallo->getApplicationDirectory(), pallo::DIRECTORY_DATA . '/' . $name);
        $archiveFile = $archiveFile->getCopyFile();

        $archive = $this->pallo->getDependency('pallo\\library\\archive\\Archive', $archiveFile->getExtension(), array('file' => $archiveFile));

        $browserRootPath = $this->fileBrowser->getRoot();
        foreach($files as $file) {
            $file = new File($browserRootPath, $file);

            $archive->compress($file);
        }

        $this->setDownloadView($archiveFile, null, true);
    }

    /**
     * Sets files and directories to the clipboard
     * @param array $files Array with relative paths
     * @return null
     */
    public function clipboardAdd(array $files = null)
    {


        if($files == null) {
            return;
        }

        foreach($files as $file) {

            $file = $this->fileBrowser->getRoot()->getChild($file);

            if(!$file->exists()) {
                continue;
            }

            $file = $this->fileBrowser->getPath($file, false);

            $this->clipboard[$file->getPath()] = $file;
        }
    }

    /**
     * Removes files and directories from the clipboard
     * @param array $files Array with relative paths
     * @return null
     */
    public function clipboardRemove(array $files = null) {


        if($files == null) {
            return;
        }

        foreach($files as $file) {
            //$file = new File($file);
            $file = $this->getRoot()->getChild($file);
            $file = $file->getName();
           // $file = $file->getPath();

            if(array_key_exists($file, $this->clipboard)) {
                unset($this->clipboard[$file]);
            }
        }

        $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]);
        $this->response->setRedirect($redirectUrl);
    }

    /**
     * Copy files and directories from the clipboard to the current path
     * @param array $files Array with the files to copy
     * @return null
     */
    public function clipboardCopy(array $files = null)
    {
        $this->clipboardFileAction('copy', $files);
    }

    /**
     * Move files and directories from the clipboard to the current path
     * @param array $files Array with the files to copy
     * @return null
     */
    public function clipboardMove(array $files = null)
    {
        $this->clipboardFileAction('move', $files);
    }

    /**
     * Process files and directories from the clipboard to the current path
     * @param string $action The method to invoke (copy or move)
     * @param array $files Array with the files to copy
     * @return null
     */
    private function clipboardFileAction($action, array $files = null) {

        if($files == null) {
            return;
        }

        $root = $this->fileBrowser->getRoot();
        //$baseDestination = new File($root, $this->path);
        $baseDestination = $this->getRoot()->getChild($this->path);


        foreach($files as $file) {
            //$file = new File($file);
            $file = $this->getRoot()->getPath($file); var_dump($file);exit;
            $path = $file->getAbsolutePath();

            if(!array_key_exists($path, $this->clipboard)) {
                continue;
            }

            //  $source = new File($root, $file);
            $source = $this->getRoot()->getPath($file, true);
          //  $destination = new File($baseDestination, $file->getName());
            $destination = $this->getRoot()->getPath($baseDestination, true);
            if(!$destination->isWritable()) {
                $this->addError(self::TRANSLATION_ERROR_WRITABLE, array('path' => $this->fileBrowser->getPath($destination)));
                continue;
            }

            $source->$action($destination);

            unset($this->clipboard[$path]);
        }
        $redirectUrl = $this->getUrl($this->routes[self::ROUTE_PATH]) . ($path ? '/' . $path : '');
        $this->response->setRedirect($redirectUrl);
    }

    /**
     * Gets the table of the browser
     * @param string $action URL to the action of the table form
     * @param pallo\library\system\file\File $path The current path
     * @param string $pathAction URL to change the current path
     * @param string $downloadAction URL to download a file
     * @param string $renameAction URL to rename a file or directory
     * @param string $editAction URL to edit a file
     * @param string $orderField The label of the order method
     * @param string $orderDirection The order direction
     * @return pallo\filebrowser\table\BrowserTable
     */
    private function getBrowserTable($action, File $path = null, $pathAction = null, $downloadAction = null, $renameAction = null, $editAction = null, $orderField = null, $orderDirection = null)
    {
        if(is_null($path)) {
            $path = $this->root;
        }
        $files = $this->fileBrowser->readDirectory($path);

        if($this->filters) {
            $files = $this->fileBrowser->applyFilters($files, $this->filters);
        }

        $translator = $this->getTranslator();


        $table = new BrowserTable($action, $files, $this->fileBrowser, $translator, $pathAction, $downloadAction);
        $table->setId('table-browser');
        $table->addDecorator(new EditActionDecorator($editAction, $translator->translate(self::TRANSLATION_EDIT), $this->fileBrowser, $this->extensions));
        $table->addDecorator(new FileActionDecorator($renameAction, $translator->translate(self::TRANSLATION_RENAME), $this->fileBrowser));


        if(interface_exists('pallo\\library\\archive\\Archive')) {
            $table->addAction($translator->translate(self::TRANSLATION_DOWNLOAD_ARCHIVE), array($this, 'archive'));

        }
        $table->addAction($translator->translate(self::TRANSLATION_CLIPBOARD_COPY), array($this, 'clipboardAdd'));
        $table->addAction($translator->translate(self::TRANSLATION_DELETE), array($this, 'delete'), $translator->translate(self::TRANSLATION_CONFIRM_DELETE));

        $table->addOrderMethod($translator->translate(self::TRANSLATION_NAME), array($this->fileBrowser, 'orderByNameAscending'), array($this->fileBrowser, 'orderByNameDescending'));
        $table->addOrderMethod($translator->translate(self::TRANSLATION_EXTENSION), array($this->fileBrowser, 'orderByExtensionAscending'), array($this->fileBrowser, 'orderByExtensionDescending'));
        $table->addOrderMethod($translator->translate(self::TRANSLATION_SIZE), array($this->fileBrowser, 'orderBySizeAscending'), array($this->fileBrowser, 'orderBySizeDescending'));

        $session = $this->request->getSession();

        if($orderDirection) {
            $table->setOrderDirection($orderDirection);
        }
        if($orderField) {
            $table->setOrderMethod($orderField);
        }

        $this->form = $this->buildForm($table);;

        $table->processForm($this->form);

        $orderField = $table->getOrderMethod();

        if($orderDirection == BrowserTable::ORDER_DIRECTION_ASC) {
            $orderDirection = BrowserTable::ORDER_DIRECTION_DESC;
        } else {
            $orderDirection = BrowserTable::ORDER_DIRECTION_ASC;
        }
        $orderQuery = '?' . self::ARGUMENT_ORDER_FIELD . '=' . $orderField . '&' . self::ARGUMENT_ORDER_DIRECTION . '=' . $orderDirection;

        $table->setOrderDirectionUrl($action . $orderQuery);

        return $table;
    }

    /**
     * Gets the table of the clipboard
     * @param string $action URL to the action of the table form
     * @return pallo\filebrowser\table\ClipboardTable
     */
    private function getClipboardTable($action) {
        $clipboardTable = new ClipboardTable($action, $this->fileBrowser->getRoot(),$this->clipboard);
        //$clipboardTable = new ClipboardTable($action, $this->fileBrowser->getRoot(), $this->clipboard);
        $clipboardTable->setId('table-clipboard');

        $translator = $this->getTranslator();

        $clipboardTable->addAction($translator->translate(self::TRANSLATION_COPY), array($this, 'clipboardCopy'));
        $clipboardTable->addAction($translator->translate(self::TRANSLATION_MOVE), array($this, 'clipboardMove'));
        $clipboardTable->addAction($translator->translate(self::TRANSLATION_CLIPBOARD_REMOVE), array($this, 'clipboardRemove'));

        $this->clipboardForm = $this->buildForm($clipboardTable);;

        $clipboardTable->processForm($this->clipboardForm);

        return $clipboardTable;
    }

    /**
     * Gets the file object from the provided tokens
     * @param array $tokens Tokens of the file name from the request
     * @param boolean $addRoot Flag to see if the root of the browser should be added
     * @return pallo\library\system\file\File The file object of the tokens
     */
    private function getFileFromTokens(array $tokens, $addRoot = true)
    {
        if(empty($tokens)) {
            return null;
        }

        $path = implode(File::DIRECTORY_SEPARATOR, $tokens);

        if($addRoot) {
            $file = $this->root->getChild($path);
        } else {
            $file = $this->root->getFileSystem()->getFile($path);
        }

        return $file;
    }

}