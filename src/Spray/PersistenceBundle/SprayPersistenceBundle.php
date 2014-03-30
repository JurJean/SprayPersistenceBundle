<?php

namespace Spray\PersistenceBundle;

use Spray\PersistenceBundle\DependencyInjection\CompilerPass\EntityFilterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SprayPersistenceBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new EntityFilterCompilerPass());
    }
}
