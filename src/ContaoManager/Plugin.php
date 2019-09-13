<?php


namespace GeorgPreissl\Imagefilter\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;

use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;



use GeorgPreissl\Imagefilter\GeorgPreisslImageFilter;

/**
 * Plugin for the Contao Manager.
 *
 * @author Georg Preissl  <https://github.com/georgpreissl>
 */
class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(GeorgPreisslImageFilter::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }



    /**
     * Returns a collection of routes for this bundle.
     *
     * @param LoaderResolverInterface $resolver
     * @param KernelInterface         $kernel
     *
     * @return RouteCollection|null
     *
     * @throws \Exception
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
            ->resolve(__DIR__.'/../Resources/config/routing.yml')
            ->load(__DIR__.'/../Resources/config/routing.yml');

    }



}
