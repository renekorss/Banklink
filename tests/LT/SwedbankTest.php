<?php

namespace RKD\Banklink\Test\LT;

/**
 * Test suite for Swedbank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class SwedbankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\LT\Swedbank";

    protected $requestUrl = [
        'payment' => 'https://www.swedbank.lt/banklink/',
        'auth' => 'https://www.swedbank.lt/banklink/'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://www.swedbank.lt/banklink/',
        'auth' => 'https://www.swedbank.lt/banklink/'
    ];

    /**
     * Set test data.
     */
    public function setUp() : void
    {
        parent::setUp();

        // Swedbank has encoding
        $this->expectedData['VK_SERVICE'] = '1002';
        $this->expectedData['VK_ENCODING'] = 'UTF-8';
        $this->expectedData['VK_MAC'] = 'WnPfkNEbj0lvkdrVeE7j1E8mGWPs1R+mX/JPokmxHXXjDmzrwlY2TfGmQLYqdog6k1m4By4C+olSeZ/BN98UaXgmeonZgtTrrrOttYT1KaK5DfOTa3VptjqzG1k7jNDloFOJft4fYCrxx/A7XUJFXpKhIMf0lpeLkHOuW2x3U3k=';

        unset($this->expectedData['VK_NAME']);
        unset($this->expectedData['VK_ACC']);


        $this->setUpBanklink();
    }

    /**
     * Test custom request data
     */
    public function testCustomRequestData()
    {
        $this->bank = new $this->bankClass($this->protocol);

        $customData = [
            'INAPP' => 1, // new data
            'VK_REF' => 'mycustomref', // override data
            'VK_MAC' => 'Xa/BT7coMmTnRjV/mBBEJU3XLWe7eczWOy0eu8NapJdEH5FpP0lEWP/07g4hY3dpSLCADODXPZss/aiY18C3qhBJNBIoaROqB7+0SRxU+FbJ9PuFLWKHSmSg50c+9xfEJy1z5gybVjZJxGmQNNmdvlzV7eBFKCQ6FvTpE1WnyAo='
        ];

        $this->expectedData = array_merge($this->expectedData, $customData);

        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $customData, $this->timezone);

        // Custom url
        $this->assertEquals($this->expectedData, $request->getRequestData());
    }
}
