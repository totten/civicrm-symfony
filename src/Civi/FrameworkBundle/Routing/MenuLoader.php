<?php
namespace Civi\FrameworkBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class MenuLoader implements LoaderInterface
{
    const CONTROLLER = "CiviFrameworkBundle:Legacy:adapt";
    private $loaded = false;

    public function __construct(\Civi\FrameworkBundle\CiviCRM $civicrm) {
        $this->civicrm = $civicrm;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();

        \CRM_Core_Menu::store();
        $xmlItems = \CRM_Core_Menu::xmlItems();
        foreach ($xmlItems as $path => $xmlItems) {
            // FIXME: Detect whether $path is an exact dir or a base dir
            $route = new Route("/$path", array(
              '_controller' => self::CONTROLLER,
            ));
            $name = 'civi_' . preg_replace('/[^a-zA-Z0-9_\.]/', '_', $path);
            $routes->add($name, $route);
        }

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'civicrm_menu' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
