<?php

namespace Mopalgen\Paygen;

class Action
{
    // Transaction sales are submitted and immediately flagged for settlement.
    public function sale($security_token, $customer, $order_detail, $payment_method, $payment_token, $transaction_session_id = "", $three_ds_data = null)
    {
        $data['type'] = 'sale';
        // Security token
        $data['security_key'] = $security_token;
        // Payment Details
        $data['payment_method'] = $payment_method;
        $data['payment_token'] = $payment_token;
        
        // Add 3DS data if provided
        if ($three_ds_data !== null) {
            $data['cardholder_auth'] = $three_ds_data['cardholder_auth'];
            $data['cavv'] = $three_ds_data['cavv'];
            $data['xid'] = $three_ds_data['xid'];
            $data['three_ds_version'] = $three_ds_data['three_ds_version'];
            $data['directory_server_id'] = $three_ds_data['directory_server_id'];
        }
        
        // Kount Fraud Detection
        if ($transaction_session_id != "") {
            $data['transaction_session_id'] = $transaction_session_id;
        }
        // Order Information
        $data['ipaddress'] = $customer->_ipAddress;
        $data['orderid'] = $order_detail['order_id'];
        $data['orderdescription'] = ''; // TODO
        $data['tax'] = number_format($order_detail['tax'],2,".","");
        $data['shipping'] = number_format($order_detail['shipping_rate'],2,".","");
        $data['amount'] = number_format($order_detail['total'],2,".","");
        // Billing Information
        $data['firstname'] = $customer->_billingAddress['firstname'];
        $data['lastname'] = $customer->_billingAddress['lastname'];
        $data['address1'] = $customer->_billingAddress['address1'];
        $data['address2'] = $customer->_billingAddress['address2'];
        $data['city'] = $customer->_billingAddress['city'];
        $data['state'] = $customer->_billingAddress['state'];
        $data['country'] = $customer->_billingAddress['country'];
        $data['zip'] = $customer->_billingAddress['zip'];
        // Shipping Information
        $data['shipping_firstname'] = $customer->_shippingAddress['firstname'];
        $data['shipping_lastname'] = $customer->_shippingAddress['lastname'];
        $data['shipping_address1'] = $customer->_shippingAddress['address1'];
        $data['shipping_address2'] = $customer->_shippingAddress['address2'];
        $data['shipping_city'] = $customer->_shippingAddress['city'];
        $data['shipping_state'] = $customer->_shippingAddress['state'];
        $data['shipping_country'] = $customer->_shippingAddress['country'];
        $data['shipping_zip'] = $customer->_shippingAddress['zip'];

        return Api::post($data);
    }

    // Transaction voids will cancel an existing sale or captured authorization.
    // In addition, non-captured authorizations can be voided to prevent any future capture.
    // Voids can only occur if the transaction has not been settled.
    public function void($security_token, $transaction_id)
    {
        $data['type'] = 'void';
        $data['security_key'] = $security_token;
        $data['transactionid'] = $transaction_id;

        return Api::post($data);

    }

    // Transaction refunds will reverse a previously settled or pending settlement transaction.
    // If the transaction has not been settled, a transaction void can also reverse it.
    public function refund($security_token, $transaction_id, $amount = 0)
    {
        $data['type'] = 'refund';
        $data['security_key'] = $security_token;
        $data['transactionid'] = $transaction_id;
        $data['amount'] = number_format($amount,2,".","");

        return Api::post($data);
    }

    public function saleUsingCustomerVault($security_token, $customer, $order_detail, $payment_method, $customer_vault_id, $transaction_session_id = "", $three_ds_data = null)
    {
        $data['type'] = 'sale';
        // Security token
        $data['security_key'] = $security_token;
        // Payment Details
        $data['payment_method'] = $payment_method;
        $data['customer_vault_id'] = $customer_vault_id;
        
        // Add 3DS data if provided
        if ($three_ds_data !== null) {
            $data['cardholder_auth'] = $three_ds_data['cardholder_auth'];
            $data['cavv'] = $three_ds_data['cavv'];
            $data['xid'] = $three_ds_data['xid'];
            $data['three_ds_version'] = $three_ds_data['three_ds_version'];
            $data['directory_server_id'] = $three_ds_data['directory_server_id'];
        }
        
        // Kount Fraud Detection
        if ($transaction_session_id != "") {
            $data['transaction_session_id'] = $transaction_session_id;
        }
        // Order Information
        $data['ipaddress'] = $customer->_ipAddress;
        $data['orderid'] = $order_detail['order_id'];
        $data['orderdescription'] = ''; // TODO
        $data['tax'] = number_format($order_detail['tax'],2,".","");
        $data['shipping'] = number_format($order_detail['shipping_rate'],2,".","");
        $data['amount'] = number_format($order_detail['total'],2,".","");
        // Billing Information
        $data['firstname'] = $customer->_billingAddress['firstname'];
        $data['lastname'] = $customer->_billingAddress['lastname'];
        $data['address1'] = $customer->_billingAddress['address1'];
        $data['address2'] = $customer->_billingAddress['address2'];
        $data['city'] = $customer->_billingAddress['city'];
        $data['state'] = $customer->_billingAddress['state'];
        $data['country'] = $customer->_billingAddress['country'];
        $data['zip'] = $customer->_billingAddress['zip'];
        // Shipping Information
        $data['shipping_firstname'] = $customer->_shippingAddress['firstname'];
        $data['shipping_lastname'] = $customer->_shippingAddress['lastname'];
        $data['shipping_address1'] = $customer->_shippingAddress['address1'];
        $data['shipping_address2'] = $customer->_shippingAddress['address2'];
        $data['shipping_city'] = $customer->_shippingAddress['city'];
        $data['shipping_state'] = $customer->_shippingAddress['state'];
        $data['shipping_country'] = $customer->_shippingAddress['country'];
        $data['shipping_zip'] = $customer->_shippingAddress['zip'];

        return Api::post($data);
    }
}