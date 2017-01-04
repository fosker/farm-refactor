<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class FoodService extends Model
{
    const ORDER_CHECK = 'http://dev.sushivesla.by/api/order/checking';
    const ORDER_DELIVERY = 'http://dev.sushivesla.by/api/order/delivery';

    const PHARMSET1 = 805;
    const PHARMSET2 = 807;

    /** @var  Client $client*/
    private $client;

    public $present_id;
    public $user;

    public function sendRequest()
    {
        $this->client = new Client();
        $client = new Client();
        $data = [
            'id' => [$this->present_id],
            'amount' => [1],
            'half' => [0]
        ];
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('post')
            ->setUrl(static::ORDER_CHECK)
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ])
            ->setData($data)
            ->send();

        if ($response->isOk) {
            $cookie = $response->getCookies()->get('SESSd2ec49ca5e7e7506c542e4aceab7101a');
            var_dump($cookie);
            $this->sendDeliveryRequest($cookie);
        }
    }

    private function sendDeliveryRequest($cookie)
    {
        $data = [
            'delivery-type' => 0,
            'street' => $this->user->pharmacist->pharmacy->address,
            'building_number' => 1,
            'apartment' => 1,
            'customer' => $this->user->name,
            'phone_code' => 0,
            'phone_number' => $this->user->phone,
            'email' => $this->user->email,
            'terms_accepted' => 1,
            'order_payment' => 0
        ];
        $response = $this->client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('post')
            ->setUrl(static::ORDER_DELIVERY)
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ])
            ->addCookies([
                [
                    'name' => $cookie->name,
                    'value' => $cookie->value
                ]
            ])
            ->setData($data)
            ->send();

        if ($response->isOk) {
            echo '<pre>';
            var_dump($response->getData());
            echo '</pre>';
            die();
        }
    }
}