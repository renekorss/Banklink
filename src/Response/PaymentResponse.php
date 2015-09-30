<?php
namespace RKD\Banklink\Response;

/**
 * Payment response wrapper
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class PaymentResponse extends Response{

    protected $orderId;
    protected $sum;
    protected $currency;
    protected $sender;
    protected $transactionId;
    protected $transactionDate;

    /**
     * Set order ID
     *
     * @param string Order ID
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
     * @param double Sum
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
     * @param string Currency
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
     * @param string Sender name
     * @param string Sender account
     */

    public function setSender($senderName, $senderAccount){
        $this->sender['name']    = $senderName;
        $this->sender['account'] = $senderAccount;
    }

    /**
     * Get sender
     *
     * @return array Sender array, containing name and account
     */

    public function getSender(){
        return $this->sender;
    }

    /**
     * Set transactionId
     *
     * @param string Transaction ID
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
     * @param string Transaction date
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
