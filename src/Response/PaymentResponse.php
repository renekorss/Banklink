<?php
/**
 * RKD Banklink
 *
 * @package Banklink\Response
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

namespace RKD\Banklink\Response;

/**
 * Payment response wrapper
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class PaymentResponse extends Response{

    /**
     * Order id
     * @var string
     */
    protected $orderId;

    /**
     * Transaction sum
     * @var string
     */
    protected $sum;

    /**
     * Currency used
     * @var string
     */
    protected $currency;

    /**
     * Sender data (name and account)
     * @var object
     */
    protected $sender;

    /**
     * Transaction id
     * @var string
     */
    protected $transactionId;

    /**
     * Transaction date
     * @var string
     */
    protected $transactionDate;

    /**
     * Set order ID
     *
     * @param string $orderId Order ID
     */

    public function setOrderId($orderId){
        $this->orderId = $orderId;
    }

    /**
     * Get order ID
     *
     * @return string Order ID
     */

    public function getOrderId(){
        return $this->orderId;
    }

    /**
     * Set sum
     *
     * @param double $sum Sum
     */

    public function setSum($sum){
        $this->sum = $sum;
    }

    /**
     * Get sum
     *
     * @return double Sum
     */

    public function getSum(){
        return $this->sum;
    }

    /**
     * Set currency
     *
     * @param string $currency Currency
     */

    public function setCurrency($currency){
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return string Currency
     */

    public function getCurrency(){
        return $this->currency;
    }

    /**
     * Set sender
     *
     * @param string $senderName Sender name
     * @param string $senderAccount Sender account
     */

    public function setSender($senderName, $senderAccount){
        $this->sender          = new \stdClass();
        $this->sender->name    = $senderName;
        $this->sender->account = $senderAccount;
    }

    /**
     * Get sender
     *
     * @return object Sender object, containing name and account
     */

    public function getSender(){
        return $this->sender;
    }

    /**
     * Set transactionId
     *
     * @param string $transactionId Transaction ID
     */

    public function setTransactionId($transactionId){
        $this->transactionId = $transactionId;
    }

    /**
     * Get transactionId
     *
     * @return string Transaction ID
     */

    public function getTransactionId(){
        return $this->transactionId;
    }

    /**
     * Set transactionDate
     *
     * @param string $transactionDate Transaction date
     */

    public function setTransactionDate($transactionDate){
        $this->transactionDate = $transactionDate;
    }

    /**
     * Get transactionDate
     *
     * @return string Transaction date
     */

    public function getTransactionDate(){
        return $this->transactionDate;
    }
}
