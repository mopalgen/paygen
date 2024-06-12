<?php

namespace Mopalgen\Paygen;

class Customer
{
    public $_shippingAddress;
    public $_billingAddress;
    public $_ipAddress;

    public function setShippingAddress(
        $firstname,
        $lastname,
        $address1,
        $address2,
        $city,
        $state,
        $country,
        $zip
    )
    {
        $this->_shippingAddress['firstname'] = $firstname;
        $this->_shippingAddress['lastname'] = $lastname;
        $this->_shippingAddress['address1'] = $address1;
        $this->_shippingAddress['address2'] = $address2;
        $this->_shippingAddress['city'] = $city;
        $this->_shippingAddress['state'] = $state;
        $this->_shippingAddress['country'] = $country;
        $this->_shippingAddress['zip'] = $zip;
    }

    public function setBillingAddress(
        $firstname,
        $lastname,
        $address1,
        $address2,
        $city,
        $state,
        $country,
        $zip
    )
    {
        $this->_billingAddress['firstname'] = $firstname;
        $this->_billingAddress['lastname'] = $lastname;
        $this->_billingAddress['address1'] = $address1;
        $this->_billingAddress['address2'] = $address2;
        $this->_billingAddress['city'] = $city;
        $this->_billingAddress['state'] = $state;
        $this->_billingAddress['country'] = $country;
        $this->_billingAddress['zip'] = $zip;
    }

    public function setIPAddress($ip_address)
    {
        $this->_ipAddress = $ip_address;
    }

    public function addCardToCustomerVault($security_token, $payment_token)
    {
        $data['customer_vault'] = 'add_customer';
        $data['security_key'] = $security_token;
        $data['payment_token'] =  $payment_token;

        return Api::post($data);
    }

    public function updateCardFromCustomerVault($security_token, $customer_vault_id, $payment_token)
    {
        $data['customer_vault'] = 'update_customer';
        $data['security_key'] = $security_token;
        $data['customer_vault_id'] = $customer_vault_id;
        $data['payment_token'] =  $payment_token;

        return Api::post($data);

    }

    public function deleteCardFromCustomerVault($security_token, $customer_vault_id)
    {
        $data['customer_vault'] = 'delete_customer';
        $data['security_key'] = $security_token;
        $data['customer_vault_id'] =  $customer_vault_id;

        return Api::post($data);
    }
}