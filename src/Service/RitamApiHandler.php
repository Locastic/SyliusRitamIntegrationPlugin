<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Symfony\Component\HttpFoundation\Request;

class RitamApiHandler
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct($host, $apiVersion, $username, $password)
    {
        $this->host = $host;
        $this->apiVersion = $apiVersion;
        $this->username = $username;
        $this->password = $password;
    }

    public function getRitamProducts()
    {
        return $this->executeCurlGetRequest('/products/list');
    }

    public function getRitamProductStock()
    {
        return $this->executeCurlGetRequest('/products/instock');
    }

    public function getRitamProductPrices()
    {
        return $this->executeCurlGetRequest('/products/refreshprices');
    }

    public function postOrderToRitam(string $data)
    {
        return $this->executeCurlPostRequest('/webshop/orders/create', $data);
    }

    private function executeCurlGetRequest(string $resource)
    {
        $url = $this->generateApiUrl($resource, Request::METHOD_GET);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_VERBOSE, true);


        $data = curl_exec($curl);

        if (empty($data)) {
            $error = curl_error($curl);
            curl_close($curl);

            return $error;
        }

        curl_close($curl);

        return $this->parseResult($data)->List;
    }

    private function executeCurlPostRequest(string $resource, string $data)
    {
        $url = $this->generateApiUrl($resource, Request::METHOD_POST);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: '.strlen($data),
            )
        );

        $response = curl_exec($curl);

        if (empty($response)) {
            $error = curl_error($curl);
            curl_close($curl);

            return $error;
        }

        curl_close($curl);

        return $response;
    }

    private function parseResult($response)
    {
        return json_decode($response);
    }

    private function generateApiUrl(string $resource, string $httpVerb)
    {
        $dateTime = new \DateTime();

        return $this->host.$this->apiVersion.$resource.'?username='.$this->username.'&date='.$dateTime->format(
                'd.m.Y%20H:i:s'
            ).'&signature='.$this->generateSignature($httpVerb, $dateTime, $resource);
    }

    private function generateSignature(string $httpVerb, \DateTime $dateTime, string $resource)
    {
        $strToSign = $httpVerb."\r\n".$this->apiVersion.$resource."\r\n".$dateTime->format('d.m.Y H:i:s');

        return hash_hmac('sha1', $strToSign, $this->password);
    }
}