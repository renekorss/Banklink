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
     * @return string Order ID
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
     * @return string Sum
     */

    public function setSum($sum){
        $this->sum = $sum;
    }

    /**
     * Get sum
     *
     * @return string Sum
     */

    public function getSum(){
        return $this->sum;
    }

    /**
     * Set currency
     *
     * @return string Currency
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
     * @return array Sender
     */

    public function setSender($sender){
        $this->sender = $sender;
    }

    /**
     * Get sender
     *
     * @return array Sender
     */

    public function getSender(){
        return $this->sender;
    }

    /**
     * Set transactionId
     *
     * @return string Transaction ID
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
     * @return string Transaction ID
     */

    public function setTransactionDate($transactionDate){
        $this->transactionDate = $transactionDate;
    }

    /**
     * Get transactionDate
     *
     * @return string Transaction ID
     */

    public function getTransactionDate(){
        return $this->transactionDate;
    }
}
