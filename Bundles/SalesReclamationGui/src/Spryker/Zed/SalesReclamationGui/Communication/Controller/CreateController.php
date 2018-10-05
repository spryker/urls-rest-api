<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesReclamationGui\Communication\Controller;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ReclamationCreateRequestTransfer;
use Generated\Shared\Transfer\ReclamationTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\SalesReclamationGui\SalesReclamationGuiConfig;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\SalesReclamationGui\Communication\SalesReclamationGuiCommunicationFactory getFactory()
 */
class CreateController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idSalesOrder = $this->castId($request->get(SalesReclamationGuiConfig::PARAM_ID_SALES_ORDER));

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($idSalesOrder);

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->showForm($orderTransfer);
        }

        $idsOrderItem = $request->request->getDigits(SalesReclamationGuiConfig::PARAM_IDS_SALES_ORDER_ITEMS);

        if (!$idsOrderItem) {
            $this->addErrorMessage('No order items provided');

            return $this->showForm($orderTransfer);
        }

        $reclamationTransfer = $this->createReclamation($orderTransfer, ...$idsOrderItem);

        if ($reclamationTransfer) {
            $this->addSuccessMessage(sprintf(
                'Reclamation id:%s for order %s successfully created',
                $reclamationTransfer->getIdSalesReclamation(),
                $orderTransfer->getOrderReference()
            ));
        }

        return $this->redirectResponse(
            Url::generate(
                '/sales-reclamation-gui/detail',
                [
                    SalesReclamationGuiConfig::PARAM_ID_RECLAMATION => $reclamationTransfer->getIdSalesReclamation(),
                ]
            )->build()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function showForm(OrderTransfer $orderTransfer)
    {
        $reclamation = $this->getFactory()
            ->getSalesReclamationFacade()
            ->hydrateReclamationByOrder($orderTransfer);

        return $this->viewResponse([
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int ...$idsOrderItem
     *
     * @return \Generated\Shared\Transfer\ReclamationTransfer|null
     */
    protected function createReclamation(OrderTransfer $orderTransfer, int ... $idsOrderItem): ?ReclamationTransfer
    {
        $reclamationCreateRequestTransfer = new ReclamationCreateRequestTransfer();
        $reclamationCreateRequestTransfer->setOrder($orderTransfer);

        foreach ($idsOrderItem as $idOrderItem) {
            $orderItemsTransfer = $this->getOrderItemById($orderTransfer, $idOrderItem);

            if (!$orderItemsTransfer) {
                $this->addErrorMessage(sprintf(
                    'OrderItem with id %s not belong to order %s',
                    $idOrderItem,
                    $orderTransfer->getIdSalesOrder()
                ));

                return null;
            }

            $reclamationCreateRequestTransfer->addItem($orderItemsTransfer);
        }

        $reclamationTransfer = $this->getFactory()
            ->getSalesReclamationFacade()
            ->createReclamation($reclamationCreateRequestTransfer);

        if (!$reclamationTransfer) {
            $this->addErrorMessage('Can not create reclamation');
        }

        return $reclamationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idOrderItem
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    protected function getOrderItemById(OrderTransfer $orderTransfer, int $idOrderItem): ?ItemTransfer
    {
        foreach ($orderTransfer->getItems() as $orderItemTransfer) {
            if ($orderItemTransfer->getIdSalesOrderItem() === $idOrderItem) {
                return $orderItemTransfer;
            }
        }

        return null;
    }
}
