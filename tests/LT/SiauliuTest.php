<?php

namespace RKD\Banklink\Test\LT;

use RKD\Banklink\Response\PaymentResponse;

/**
 * Test suite for Šiaulių bank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class SiauliuTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\LT\Siauliu";

    protected $requestUrl = [
        'payment' => 'https://online.sb.lt/ib/site/ibpay/login',
        'auth' => 'https://online.sb.lt/ib/site/ibpay/login'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://online.sb.lt/ib/site/ibpay/login',
        'auth' => 'https://online.sb.lt/ib/site/ibpay/login'
    ];

    /**
     * Test failed payment response.
     */
    public function testHandlePaymentResponseError1201()
    {
        // SEB has VK_ACC, Siauliu has VK_REC_ACC
        $responseData = [
            'VK_SERVICE'  => '1201',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => $this->senderName,
            'VK_REC_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_AMOUNT'   => $this->amount,
            'VK_CURR'     => $this->currency,
            'VK_REC_ACC'  => $this->sellerAccount,
            'VK_REC_NAME' => $this->sellerName,
            'VK_SND_ACC'  => '101032423434543',
            'VK_SND_NAME' => 'Mart Mets',
            'VK_REF'      => $this->orderId,
            'VK_MSG'      => $this->message,
            'VK_MAC'      => 'o4rju0oEwITuIheUdtDjp2njKhBzvQv8RjKg+rdCB+fwGiUS8zpXzr0I+wj0vl13h+ACGAR1LO9gR2+IG1yq+AJdQdVszJIbeA1jcg1GFtl1xyLN8LXYfubHHUB/7EWwiEGZKcHrNp3pAsADlLwySQLRWatheMLPqRRk2FX96Ko=',
            'VK_AUTO'     => 'N'
        ];

        $response = $this->bank->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertSame(PaymentResponse::STATUS_ERROR, $response->getStatus());

        // This is not valid response, so validation should fail
        $this->assertFalse($response->wasSuccessful());

        // User pressed "Back to merchant" button
        $this->assertFalse($response->isAutomatic());
    }
}
