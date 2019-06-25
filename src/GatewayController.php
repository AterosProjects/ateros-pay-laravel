<?php

namespace Ateros\Pay;

use App\Http\Controllers\Controller;
use Exception;

class GatewayController extends Controller
{
    public $endpoint = 'https://pay.ateros.fr/api';
    private $app_token;
    private $curl;

    /**
     * Gateway constructor.
     */
    public function __construct()
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param $bool
     * @param $message
     * @throws Exception
     */
    private static function assert($bool, $message)
    {
        if (!$bool){
            throw new Exception($message);
        }
    }

    /**
     * @param $id
     * @return string
     */
    private static function getNamefromId($id)
    {
        $code = substr($id, 0, 3);

        if($code === "sub"){
            return "subscription";
        }

        if ($code === "pmt"){
            return "payment";
        }
    }

    /**
     * @param array $request
     * @param string $type
     * @param callable $handler
     * @throws Exception
     */
    public function handle(array $request, string $type, callable $handler)
    {
        $body = $request->getContent();
        $this::assert(isset($this->app_token), "app_token must be set to use this function");
        $this::assert($body['app_token'] == hash('sha256', $this->app_token), "callback message could not be verified");

        if ($this::getNamefromId($body['id']) == $type){
            $handler($request);
        }
    }

    /**
     * @param array $payment
     * @return mixed
     * @throws Exception
     */
    public function createPayment(array $payment)
    {
        $this::assert(isset($this->app_token), "app_token must be set to use this function");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payment);

        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . "/payment");

        $response = curl_exec($this->curl);
        curl_close($this->curl);

        $object = json_decode($response);

        if(!$object){
            throw new Exception('Could not connect to Ateros Pay');
        }

        $object->success = $object->message == "Payment created successfully" ? True : False;

        return $object;
    }

    /**
     * @param array $subscription
     * @return mixed
     * @throws Exception
     */
    public function createSubscription(array $subscription)
    {
        $this::assert(isset($this->app_token), "app_token must be set to use this function");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $subscription);

        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . "/subscription");

        $response = curl_exec($this->curl);
        curl_close($this->curl);

        $object = json_decode($response);

        if(!$object){
            throw new Exception('Could not connect to Ateros Pay');
        }

        $object->success = $object->message == "Subscription created successfully" ? True : False;

        return $object;
    }

    /**
     * @param mixed $app_token
     */
    public function setAppToken($app_token)
    {
        $this->app_token = $app_token;
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            [
                "Authorization: Bearer " . $this->app_token,
                "Accept: application/json",
            ]
        );
    }
}
