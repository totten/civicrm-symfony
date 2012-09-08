<?php

namespace Civi\FrameworkBundle;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class CiviControllerResolver implements ControllerResolverInterface
{
    private $defaultResolver;

    public function __construct(BaseControllerResolver $resolver)
    {
        $this->defaultResolver = $resolver;
    }

    public function getController(Request $request)
    {
        $args = explode('/', trim($request->getPathInfo(), '/'));

        \CRM_Core_Invoke::hackMenuRebuild($args); // may exit
        \CRM_Core_Invoke::init($args);
        \CRM_Core_Invoke::hackStandalone($args);
        $item = \CRM_Core_Invoke::getItem($args);

        return function() use ($item) {
            $content = \CRM_Core_Invoke::runItem($item);
            return new \Symfony\Component\HttpFoundation\Response($content);
        };
//        return $this->defaultResolver->getController($request);
    }

    public function getArguments(Request $request, $controller)
    {
        return $this->defaultResolver->getArguments($request, $controller);
    }
/*
    public function foo($config, $item, $args) {

        if ($config->userFramework == 'Joomla' && $item) {
          $config->userFrameworkURLVar = 'task';

          // joomla 1.5RC1 seems to push this in the POST variable, which messes
          // QF and checkboxes
          unset($_POST['option']);
          \CRM_Core_Joomla::sidebarLeft();
        }

        // set active Component
        $template = \CRM_Core_Smarty::singleton();
        $template->assign('activeComponent', 'CiviCRM');
        $template->assign('formTpl', 'default');

        if ($item) {
          // CRM-7656 - make sure we send a clean sanitized path to create printer friendly url
          $printerFriendly = \CRM_Utils_System::makeURL('snippet', FALSE, FALSE,
            \CRM_Utils_Array::value('path', $item)
          ) . '2';
          $template->assign('printerFriendly', $printerFriendly);

          if (!array_key_exists('page_callback', $item)) {
            \CRM_Core_Error::debug('Bad item', $item);
            \CRM_Core_Error::fatal(ts('Bad menu record in database'));
          }

          // check that we are permissioned to access this page
          if (!\CRM_Core_Permission::checkMenuItem($item)) {
            \CRM_Utils_System::permissionDenied();
            return;
          }

          // check if ssl is set
          if (\CRM_Utils_Array::value('is_ssl', $item)) {
            \CRM_Utils_System::redirectToSSL();
          }

          if (isset($item['title'])) {
            \CRM_Utils_System::setTitle($item['title']);
          }

          if (isset($item['breadcrumb']) && !isset($item['is_public'])) {
            \CRM_Utils_System::appendBreadCrumb($item['breadcrumb']);
          }

          $pageArgs = NULL;
          if (\CRM_Utils_Array::value('page_arguments', $item)) {
            $pageArgs = \CRM_Core_Menu::getArrayForPathArgs($item['page_arguments']);
          }

          $template = \CRM_Core_Smarty::singleton();
          if (isset($item['is_public']) &&
            $item['is_public']
          ) {
            $template->assign('urlIsPublic', TRUE);
          }
          else {
            $template->assign('urlIsPublic', FALSE);
          }

          if (isset($item['return_url'])) {
            $session = \CRM_Core_Session::singleton();
            $args = \CRM_Utils_Array::value('return_url_args',
              $item,
              'reset=1'
            );
            $session->pushUserContext(\CRM_Utils_System::url($item['return_url'],
                $args
              ));
          }

          // \CRM_Core_Error::debug( $item ); exit( );
          $result = NULL;
          if (is_array($item['page_callback'])) {
            $newArgs = explode('/',
              $_GET[$config->userFrameworkURLVar]
            );
            require_once (str_replace('_',
                DIRECTORY_SEPARATOR,
                $item['page_callback'][0]
              ) . '.php');
            $result = call_user_func($item['page_callback'],
              $newArgs
            );
          }
          elseif (strstr($item['page_callback'], '_Form')) {
            $wrapper = new \CRM_Utils_Wrapper();
            $result = $wrapper->run(\CRM_Utils_Array::value('page_callback', $item),
              \CRM_Utils_Array::value('title', $item),
              isset($pageArgs) ? $pageArgs : NULL
            );
          }
          else {
            $newArgs = explode('/',
              $_GET[$config->userFrameworkURLVar]
            );
            require_once (str_replace('_',
                DIRECTORY_SEPARATOR,
                $item['page_callback']
              ) . '.php');
            $mode = 'null';
            if (isset($pageArgs['mode'])) {
              $mode = $pageArgs['mode'];
              unset($pageArgs['mode']);
            }
            $title = \CRM_Utils_Array::value('title', $item);
            if (strstr($item['page_callback'], '_Page')) {
              eval('$object = ' .
                "new {$item['page_callback']}( \$title, \$mode );"
              );
            }
            elseif (strstr($item['page_callback'], '_Controller')) {
              $addSequence = 'false';
              if (isset($pageArgs['addSequence'])) {
                $addSequence = $pageArgs['addSequence'];
                $addSequence = $addSequence ? 'true' : 'false';
                unset($pageArgs['addSequence']);
              }
              eval('$object = ' .
                "new {$item['page_callback']} ( \$title, true, \$mode, null, \$addSequence );"
              );
            }
            else {
              \CRM_Core_Error::fatal();
            }
            $result = $object->run($newArgs, $pageArgs);
          }

          \CRM_Core_Session::storeSessionObjects();
          return $result;
        }

        \CRM_Core_Menu::store();
        \CRM_Core_Session::setStatus(ts('Menu has been rebuilt'));
        return \CRM_Utils_System::redirect();
    }
    */
}