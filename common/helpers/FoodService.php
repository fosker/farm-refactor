<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class FoodService extends Model
{
    const ORDER_CHECK = 'http://sushivesla.by/api/order/checking';
    const ORDER_DELIVERY = 'http://sushivesla.by/api/order/delivery';

    const DEV_COOKIE = 'SESSd2ec49ca5e7e7506c542e4aceab7101a';
    const PROD_COOKIE = 'SESSe6e55b740931d7b6afa222824bc2aeb4';

    const PHARMSET1 = 816;
    const PHARMSET2 = 818;

    /** @var  Client $client*/
    private $client;

    public $present_id;
    public $user;

    public function sendRequest()
    {
        $this->client = new Client();
        $data = [
            'id' => [$this->present_id],
            'amount' => [1],
            'half' => [0]
        ];
        $response = $this->client->createRequest()
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
            $cookie = $response->getCookies()->get(static::PROD_COOKIE);
            if ($this->sendDeliveryRequest($cookie)) {
                return true;
            }
        }

        return false;
    }

    private function sendDeliveryRequest($cookie)
    {
        $address = explode(',', $this->user->pharmacist->pharmacy->address);
        $code = substr($this->user->phone, 0, 4);
        $number = substr($this->user->phone, 4);

        $data = [
            'delivery-type' => 0,
            'street' => $address[0],
            'building_number' => $address[1],
            'apartment' => 1,
            'customer' => $this->user->name,
            'phone_code' => $code,
            'phone_number' => $number,
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

        if ($response->data['code'] != 17) {
            return true;
        }

        return false;
    }
}