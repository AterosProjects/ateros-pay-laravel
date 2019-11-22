<?php

namespace Ateros\Pay;

use Exception;

class Gateway
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
        $this->setAppToken(config('pay.token'));
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
        $this::assert(isset($this->app_token), "app_token must be set to use this function");
        $this::assert($request['app_token'] == hash('sha256', $this->app_token), "callback message could not be verified");

        if ($this::getNamefromId($request['id']) == $type){
            $handler($request);
        }
    }

    /**
     * @param string $type
     * @param array $payment
     * @return mixed
     * @throws Exception
     */
    public function create(string $type, array $payment)
    {
        $this::assert(isset($this->app_token), 'app_token must be set to use this function');

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payment);
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . '/' . $type);
        $response = curl_exec($this->curl);
        if (!$response) {
            throw new Exception(curl_error($this->curl), curl_errno($this->curl));
        }
        curl_close($this->curl);

        $object = json_decode($response);
        $object->success = $object->message == ucfirst($type) . ' created successfully' ? True : False;
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
