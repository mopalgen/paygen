<?php

namespace Mopalgen\Paygen;

class Api
{
    const ENDPOINT= 'https://paygen.transactiongateway.com/api/transact.php';
    const POST_STATUS = [
        'APPROVED' => 1,
        'DECLINED' => 2,
        'ERROR' => 3
    ];

    private static function generateQueryString($data)
    {
        $query = '';
        foreach ($data as $key => $value) {
            $query .= $key . "=" . urlencode($value) . "&";
        }
        $query = rtrim($query, '&');
        return $query;
    }

    private static function generateArrayFromString($queryString)
    {
        $data = [];
        parse_str($queryString, $data);
        return $data;
    }

    public static function post($post_data)
    {
        $query = self::generateQueryString($post_data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, API::ENDPOINT);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        curl_setopt($curl, CURLOPT_POST, 1);

        if (!($data = curl_exec($curl))) {
            return API::POST_STATUS['ERROR'];
        }

        curl_close($curl);
        unset($curl);

        return self::generateArrayFromString($data);
    }
}