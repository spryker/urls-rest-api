<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Ratepay\Business\Request\Payment\RequestPayment;

use Functional\Spryker\Zed\Ratepay\Business\Api\Adapter\Http\RequestPaymentPrepaymentAdapterMock;
use Functional\Spryker\Zed\Ratepay\Business\Request\Payment\PrepaymentAbstractTest;
use Spryker\Shared\Ratepay\RatepayConstants;

class PrepaymentTest extends PrepaymentAbstractTest
{

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->quoteTransfer = $this->getQuoteTransfer();
    }

    /**
     * @return \Functional\Spryker\Zed\Ratepay\Business\Api\Adapter\Http\RequestPaymentPrepaymentAdapterMock
     */
    protected function getPaymentSuccessResponseAdapterMock()
    {
        return new RequestPaymentPrepaymentAdapterMock();
    }

    /**
     * @return \Functional\Spryker\Zed\Ratepay\Business\Api\Adapter\Http\RequestPaymentPrepaymentAdapterMock
     */
    protected function getPaymentFailureResponseAdapterMock()
    {
        return (new RequestPaymentPrepaymentAdapterMock())->expectFailure();
    }

    /**
     * @param \Spryker\Zed\Ratepay\Business\RatepayFacade $facade
     *
     * @return \Generated\Shared\Transfer\RatepayResponseTransfer
     */
    protected function runFacadeMethod($facade)
    {
        return $facade->requestPayment($this->quoteTransfer);
    }

    /**
     * @return void
     */
    public function testPaymentWithSuccessResponse()
    {
        parent::testPaymentWithSuccessResponse();

        $this->assertEquals(RatepayConstants::PREPAYMENT, $this->responseTransfer->getPaymentMethod());
        $this->assertEquals($this->expectedResponseTransfer->getPaymentMethod(), $this->responseTransfer->getPaymentMethod());
    }

}
