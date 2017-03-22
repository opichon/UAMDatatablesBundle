<?php

namespace UAM\Bundle\DatatablesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class UAMDatatablesExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (true === isset($bundles['AsseticBundle'])) {
            $this->configureAsseticAssets($container, $config);
        }
    }

    protected function configureAsseticAssets($container, $config)
    {
        $container->prependExtensionConfig(
            'assetic',
            array(
                'assets' => array(
                    'uamdatatables_bootstrap_css' => array(
                        'inputs' => array(
                            'bundles/uamdatatables/vendor/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css',
                            'bundles/uamdatatables/css/uamdatatables.css',
                        ),
                    ),
                    'uamdatatables_bootstrap_fa_css' => array(
                        'inputs' => array(
                            'bundles/uamdatatables/vendor/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css',
                            'bundles/uamdatatables/vendor/datatables-plugins/integration/font-awesome/dataTables.fontAwesome.css',
                            'bundles/uamdatatables/css/uamdatatables.css',
                        ),
                    ),
                    'uamdatatables_bootstrap_js' => array(
                        'inputs' => array(
                            'bundles/uamdatatables/vendor/datatables/media/js/jquery.dataTables.min.js',
                            'bundles/uamdatatables/vendor/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js',
                            'bundles/uamdatatables/js/uamdatatables.js',
                        ),
                    ),
                    'uamdatatables_bootstrap_mustache_js' => array(
                        'inputs' => array(
                            'bundles/uamdatatables/vendor/datatables/media/js/jquery.dataTables.min.js',
                            'bundles/uamdatatables/vendor/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js',
                            'bundles/uamdatatables/vendor/mustache/mustache.min.js',
                            'bundles/uamdatatables/js/uamdatatables.js',
                        ),
                    ),
                ),
            )
        );
    }
}
