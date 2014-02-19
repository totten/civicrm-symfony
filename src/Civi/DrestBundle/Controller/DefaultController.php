<?php

namespace Civi\DrestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Drest\Configuration;
use Drest\Event;

class DefaultController extends Controller {
  /**
   * @Route("/drest/{relpath}", name="_drest", requirements={"relpath" = ".+"})
   * @Template()
   */
  public function indexAction(Request $request, $relpath) {
    $em = \CRM_DB_EntityManager::singleton();
    $em->getConfiguration();

    $evm = new Event\Manager();

    global $civicrm_root;
    $drestConfig = new Configuration();
    $drestConfig->setDetectContentOptions(array(
      Configuration::DETECT_CONTENT_HEADER => 'Accept',
      Configuration::DETECT_CONTENT_EXTENSION => TRUE,
      Configuration::DETECT_CONTENT_PARAM => 'format'
    ));
    $drestConfig->setExposureDepth(3);
    $drestConfig->setExposeRequestOption(Configuration::EXPOSE_REQUEST_PARAM_GET, 'expose');
    $drestConfig->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
    $drestConfig->setDebugMode(TRUE);
    $drestConfig->addPathsToConfigFiles(array(
      $civicrm_root . '/src/Civi'
    ));
    $drestConfig->registerRequestAdapterClasses(array(
      'Drest\\Request\\Adapter\\Symfony2'
    ));
    $drestConfig->registerResponseAdapterClasses(array(
      'Drest\\Response\\Adapter\\Symfony2',
    ));
    $drestManager = \Drest\Manager::create($em, $drestConfig, $evm);

    // FIXME: drest router will look at the absolute request path, but it would be better
    // if it looked at the path relative to this bundle's starting point (eg "/civicrm")

    $response = new Response();
    $drestManager->dispatch($request, $response);
    return $response;
  }
}
