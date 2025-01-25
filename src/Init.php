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

    public function createOrder($order_details, $payment_method, $payment_token, $transaction_session_id = "", $three_ds_data = null)
    {
        $processor = new Action();
        return $processor->sale($this->_securityToken, $this->_customer, $order_details, $payment_method, $payment_token, $transaction_session_id, $three_ds_data);
    }

    public function createOrderUsingSavedCard($order_details, $payment_method, $customer_vault_id, $transaction_session_id = "", $three_ds_data = null)
    {
        $processor = new Action();
        return $processor->saleUsingCustomerVault($this->_securityToken, $this->_customer, $order_details, $payment_method, $customer_vault_id, $transaction_session_id, $three_ds_data);
    }

    public function createRefund($transaction_id, $amount)
    {
        $processor = new Action();
        return $processor->refund($this->_securityToken, $transaction_id, $amount);
    }

    public function cancelOrder($transaction_id)
    {
        $processor = new Action();
        return $processor->void($this->_securityToken, $transaction_id);
    }
}
