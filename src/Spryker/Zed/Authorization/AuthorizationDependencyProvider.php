<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Authorization;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Authorization\AuthorizationConfig getConfig()
 */
class AuthorizationDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGINS_AUTHORIZATION_STRATEGIES = 'PLUGINS_AUTHORIZATION_STRATEGIES';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addAuthorizationStrategyPlugins($container);

        return $container;
    }

    protected function addAuthorizationStrategyPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_AUTHORIZATION_STRATEGIES, function () {
            return $this->getAuthorizationStrategyPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Shared\AuthorizationExtension\Dependency\Plugin\AuthorizationStrategyPluginInterface>
     */
    protected function getAuthorizationStrategyPlugins(): array
    {
        return [];
    }
}
