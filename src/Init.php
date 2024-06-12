<?php

namespace Mopalgen\Paygen;

class Init
{
    private $_securityToken;
    private $_customer;
    private $_order;

    public function __construct(
        $securityToken,
        Customer $customer
    ) {
        $this->_securityToken = $securityToken;
        $this->_customer = $customer;
    }

    public function createOrder($order_details, $payment_method, $payment_token, $transaction_session_id = "")
    {
        $processor = new Action();
        $processor->sale($this->_securityToken, $this->_customer, $order_details, $payment_method, $payment_token, $transaction_session_id);
    }

    public function createOrderUsingSavedCard($order_details, $payment_method, $customer_vault_id, $transaction_session_id = "")
    {
        $processor = new Action();
        $processor->saleUsingCustomerVault($this->_securityToken, $this->_customer, $order_details, $payment_method, $customer_vault_id, $transaction_session_id);
    }

    public function createRefund($transaction_id, $amount)
    {
        $processor = new Action();
        $processor->refund($this->_securityToken, $transaction_id, $amount);
    }

    public function cancelOrder($transaction_id)
    {
        $processor = new Action();
        $processor->void($this->_securityToken, $transaction_id);
    }
}