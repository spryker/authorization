<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Authorization\Business\Authorization;

use Generated\Shared\Transfer\AuthorizationRequestTransfer;
use Generated\Shared\Transfer\AuthorizationResponseTransfer;
use Spryker\Zed\Authorization\AuthorizationConfig;
use Spryker\Zed\Authorization\Business\Exception\AuthorizationStrategyNotFoundException;

class AuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var \Spryker\Zed\Authorization\Business\Authorization\AuthorizationStrategyCollectionInterface
     */
    protected AuthorizationStrategyCollectionInterface $authorizationStrategyCollection;

    /**
     * @var \Spryker\Zed\Authorization\AuthorizationConfig
     */
    protected AuthorizationConfig $authorizationConfig;

    /**
     * @param \Spryker\Zed\Authorization\Business\Authorization\AuthorizationStrategyCollectionInterface $authorizationStrategyCollection
     * @param \Spryker\Zed\Authorization\AuthorizationConfig $authorizationConfig
     */
    public function __construct(AuthorizationStrategyCollectionInterface $authorizationStrategyCollection, AuthorizationConfig $authorizationConfig)
    {
        $this->authorizationStrategyCollection = $authorizationStrategyCollection;
        $this->authorizationConfig = $authorizationConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\AuthorizationRequestTransfer $authorizationRequestTransfer
     *
     * @throws \Spryker\Zed\Authorization\Business\Exception\AuthorizationStrategyNotFoundException
     *
     * @return \Generated\Shared\Transfer\AuthorizationResponseTransfer
     */
    public function authorize(AuthorizationRequestTransfer $authorizationRequestTransfer): AuthorizationResponseTransfer
    {
        $authorizationRequestTransfer = $this->addDefaultStrategy($authorizationRequestTransfer);

        $authorizationResponseTransfer = new AuthorizationResponseTransfer();

        if (!$authorizationRequestTransfer->getStrategies()) {
            return $authorizationResponseTransfer->setIsAuthorized(true);
        }

        foreach ($authorizationRequestTransfer->getStrategies() as $strategy) {
            if (!$this->authorizationStrategyCollection->has($strategy)) {
                throw new AuthorizationStrategyNotFoundException(sprintf(
                    'Authorization strategy `%s` not found.',
                    $strategy,
                ));
            }
            $isAuthorized = $this->authorizationStrategyCollection
                ->get($strategy)
                ->authorize($authorizationRequestTransfer);

            $authorizationResponseTransfer->setIsAuthorized($isAuthorized);

            if ($isAuthorized) {
                return $authorizationResponseTransfer;
            }

            $authorizationResponseTransfer->setFailedStrategy($strategy);

            if (!$this->authorizationConfig->isMultistrategyAuthorizationAllowed()) {
                return $authorizationResponseTransfer;
            }
        }

        return $authorizationResponseTransfer;
    }

    /**
     * @deprecated Exists for BC reasons. Will be removed in the next major release.
     *
     * @param \Generated\Shared\Transfer\AuthorizationRequestTransfer $authorizationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AuthorizationRequestTransfer
     */
    protected function addDefaultStrategy(AuthorizationRequestTransfer $authorizationRequestTransfer): AuthorizationRequestTransfer
    {
        if ($authorizationRequestTransfer->getStrategy()) {
            $authorizationRequestTransfer->addStrategy($authorizationRequestTransfer->getStrategy());
        }

        return $authorizationRequestTransfer;
    }
}
