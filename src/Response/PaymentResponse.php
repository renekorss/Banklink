<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016-2020 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink\Response;

use stdClass;

/**
 * Payment response wrapper.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class PaymentResponse extends Response
{
    /**
     * Order id.
     *
     * @var string
     */
    protected $orderId;

    /**
     * Transaction sum.
     *
     * @var string
     */
    protected $sum;

    /**
     * Currency used.
     *
     * @var string
     */
    protected $currency;

    /**
     * Sender data (name and account).
     *
     * @var object
     */
    protected $sender;

    /**
     * Transaction id.
     *
     * @var string
     */
    protected $transactionId;

    /**
     * Transaction date.
     *
     * @var string
     */
    protected $transactionDate;

    /**
     * Set order ID.
     *
     * @param string $orderId Order ID
     *
     * @return self
     */
    public function setOrderId(string $orderId) : self
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * Get order ID.
     *
     * @return string Order ID
     */
    public function getOrderId() : string
    {
        return $this->orderId;
    }

    /**
     * Set sum.
     *
     * @param float $sum Sum
     *
     * @return self
     */
    public function setSum(float $sum) : self
    {
        $this->sum = $sum;
        return $this;
    }

    /**
     * Get sum.
     *
     * @return float Sum
     */
    public function getSum() : ?float
    {
        return $this->sum;
    }

    /**
     * Set currency.
     *
     * @param string $currency Currency
     *
     * @return self
     */
    public function setCurrency(string $currency) : self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency.
     *
     * @return string Currency
     */
    public function getCurrency() : ?string
    {
        return $this->currency;
    }

    /**
     * Set sender.
     *
     * @param string $senderName    Sender name
     * @param string $senderAccount Sender account
     *
     * @return self
     */
    public function setSender(string $senderName, string $senderAccount) : self
    {
        $this->sender = new stdClass();
        $this->sender->name = $senderName;
        $this->sender->account = $senderAccount;
        return $this;
    }

    /**
     * Get sender.
     *
     * @return object Sender object, containing name and account
     */
    public function getSender() : ?\stdClass
    {
        return $this->sender;
    }

    /**
     * Set receiver.
     *
     * @param string $receiverName    Receiver name
     * @param string $receiverAccount Receiver account
     *
     * @return self
     */
    public function setReceiver(string $receiverName, string $receiverAccount) : self
    {
        $this->receiver = new stdClass();
        $this->receiver->name = $receiverName;
        $this->receiver->account = $receiverAccount;
        return $this;
    }

    /**
     * Get receiver.
     *
     * @return object Receiver object, containing name and account
     */
    public function getReceiver() : ?\stdClass
    {
        return $this->receiver;
    }

    /**
     * Set transactionId.
     *
     * @param string $transactionId Transaction ID
     *
     * @return self
     */
    public function setTransactionId(string $transactionId) : self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * Get transactionId.
     *
     * @return string Transaction ID
     */
    public function getTransactionId() : ?string
    {
        return $this->transactionId;
    }

    /**
     * Set transactionDate.
     *
     * @param string $transactionDate Transaction date
     *
     * @return self
     */
    public function setTransactionDate(string $transactionDate) : self
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }

    /**
     * Get transactionDate.
     *
     * @return string Transaction date
     */
    public function getTransactionDate() : ?string
    {
        return $this->transactionDate;
    }

    /**
     * Set transaction message
     *
     * @param string $message Transaction message
     *
     * @return self
     */
    public function setMessage(string $message) : ResponseInterface
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get transaction message
     *
     * @return string Message
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * Set if response is automatic
     *
     * @param bool $isAutomatic Boolean representing if response is automatically sent by bank
     *
     * @return self
     */
    public function setAutomatic(bool $isAutomatic) : ResponseInterface
    {
        $this->isAutomatic = $isAutomatic;
        return $this;
    }

    /**
     * Get if response is automatic.
     *
     * @return bool True if is automatic response, false otherwise
     */
    public function isAutomatic() : bool
    {
        return $this->isAutomatic;
    }
}
