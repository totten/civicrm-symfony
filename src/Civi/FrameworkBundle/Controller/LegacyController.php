<?php

namespace Civi\FrameworkBundle\Controller;

use Civi\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyController extends Controller
{
    /**
     * @Route("/admin")
     */
    public function adaptAction(Request $request)
    {
        $item = \CRM_Core_Invoke::getItem(trim($request->getPathInfo(), '/'));
        $content = \CRM_Core_Invoke::runItem($item);
        return new Response($content);
    }

}
