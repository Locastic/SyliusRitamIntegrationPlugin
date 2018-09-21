<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Service;


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

    /**
     * RitamApiHandler constructor.
     * @param $host
     * @param $username
     * @param $password
     * @param $apiVersion
     */
    public function __construct($host,$apiVersion, $username, $password)
    {
        $this->host = $host;
        $this->apiVersion = $apiVersion;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRitamProducts()
    {
        $url = $this->generateApiUrl('/products/list', 'GET');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $data = curl_exec($curl);

        if ($data === FALSE) {
            return curl_error($curl);
        }

        curl_close($curl);

        return $this->parseResult($data)->List;
    }

    /**
     * @param $response
     * @return mixed
     */
    private function parseResult($response)
    {
        $data = json_decode($response);

        return $data;
    }

    /**
     * @param string $resource
     * @param string $httpVerb
     * @return string
     */
    private function generateApiUrl($resource, $httpVerb)
    {
        $dateTime = new \DateTime();

        return $this->host.$this->apiVersion.$resource.'?username='.$this->username.'&date='.$dateTime->format(
                'd.m.Y%20H:i:s'
            ).'&signature='.$this->generateSignature($httpVerb, $dateTime, $resource);
    }

    /**
     * @param string $httpVerb
     * @param \DateTime $dateTime
     * @param string $resource
     * @return string
     */
    private function generateSignature($httpVerb, \DateTime $dateTime, $resource)
    {
        $strToSign = $httpVerb."\r\n".$this->apiVersion.$resource."\r\n".$dateTime->format('d.m.Y H:i:s');

        return hash_hmac('sha1', $strToSign, $this->password);
    }
}