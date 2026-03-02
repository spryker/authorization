<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Authorization\Authorization;

use Generated\Shared\Transfer\AuthorizationRequestTransfer;
use Generated\Shared\Transfer\AuthorizationResponseTransfer;

interface AuthorizationCheckerInterface
{
    public function authorize(AuthorizationRequestTransfer $authorizationRequestTransfer): AuthorizationResponseTransfer;
}
