<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Ratepay\Business\Api\Converter;

use Generated\Shared\Transfer\RatepayProfileResponseTransfer;
use Spryker\Shared\Library\Currency\CurrencyManager;
use Spryker\Zed\Ratepay\Business\Api\Constants;
use Spryker\Zed\Ratepay\Business\Api\Model\Response\ResponseInterface;

class ProfileResponseConverter extends BaseConverter
{

    /**
     * @var \Spryker\Zed\Ratepay\Business\Api\Converter\TransferObjectConverter
     */
    protected $responseTransfer;

    /**
     * @param \Spryker\Zed\Ratepay\Business\Api\Model\Response\ResponseInterface $response
     * @param \Spryker\Shared\Library\Currency\CurrencyManager $currencyManager
     * @param \Spryker\Zed\Ratepay\Business\Api\Converter\TransferObjectConverter $responseTransfer
     */
    public function __construct(
        ResponseInterface $response,
        CurrencyManager $currencyManager,
        TransferObjectConverter $responseTransfer
    ) {
        parent::__construct($response, $currencyManager);

        $this->responseTransfer = $responseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\RatepayProfileResponseTransfer
     */
    public function convert()
    {
        $baseResponse = $this->responseTransfer->convert();

        $responseTransfer = new RatepayProfileResponseTransfer();
        $responseTransfer
            ->setBaseResponse($baseResponse);

        $successCode = Constants::REQUEST_CODE_SUCCESS_MATRIX[Constants::REQUEST_MODEL_PROFILE];
        if ($successCode == $baseResponse->getResultCode()) {
            $responseTransfer
                ->setMasterData($this->response->getMasterData())
                ->setInstallmentConfigurationResult($this->response->getInstallmentConfigurationResult());
        }

        return $responseTransfer;
    }

}
