<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductCartConnector\Business;

use Generated\Shared\Transfer\ChangeTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method ProductCartConnectorBusinessFactory getFactory()
 */
class ProductCartConnectorFacade extends AbstractFacade
{

    /**
     * @param ChangeTransfer $change
     *
     * @return \Generated\Shared\Transfer\ChangeTransfer
     */
    public function expandItems(ChangeTransfer $change)
    {
        return $this->getFactory()->createProductManager()->expandItems($change);
    }

}
