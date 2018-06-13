<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListGui\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\ProductListGui\Communication\Form\ProductListForm;
use Spryker\Zed\ProductListGui\Communication\Table\ProductListTable;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Spryker\Zed\ProductListGui\ProductListGuiConfig getConfig()
 */
class ProductListGuiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Spryker\Zed\ProductListGui\Communication\Table\ProductListTable
     */
    public function createProductListTable(): ProductListTable
    {
        return new ProductListTable();
    }

    /**
     * @param array|null $data
     * @param array $options
     *
     * @return \Spryker\Zed\ProductListGui\Communication\Form\ProductListForm|\Symfony\Component\Form\FormInterface
     */
    public function getProductListForm($data = null, array $options = []): FormInterface
    {
        return $this->getFormFactory()->create(ProductListForm::class, $data, $options);
    }
}
