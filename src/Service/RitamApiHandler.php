<?php
declare(strict_types=1);

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

    public function __construct($host, $apiVersion, $username, $password)
    {
        $this->host = $host;
        $this->apiVersion = $apiVersion;
        $this->username = $username;
        $this->password = $password;
    }

    public function getRitamProducts()
    {
        $url = $this->generateApiUrl('/products/list', 'GET');

        $data  = $this->executeCurlRequest($url);

        return $this->parseResult($data)->List;
    }

    public function getRitamProductStock()
    {
        $url = $this->generateApiUrl('/products/instock', 'GET');

        $data  = $this->executeCurlRequest($url);

        return $this->parseResult($data)->List;
    }

    public function getRitamProductPrices()
    {
        $url = $this->generateApiUrl('/products/refreshprices', 'GET');

        $data  = $this->executeCurlRequest($url);

        return $this->parseResult($data)->List;
    }

    private function executeCurlRequest(string $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $data = curl_exec($curl);
        curl_close($curl);

        if ($data === false) {
            return curl_error($curl);
        }

        return $data;
    }

    private function parseResult($response)
    {
        $data = json_decode($response);

        return $data;
    }

    private function generateApiUrl($resource, $httpVerb)
    {
        $dateTime = new \DateTime();

        return $this->host.$this->apiVersion.$resource.'?username='.$this->username.'&date='.$dateTime->format(
                'd.m.Y%20H:i:s'
            ).'&signature='.$this->generateSignature($httpVerb, $dateTime, $resource);
    }

    private function generateSignature($httpVerb, \DateTime $dateTime, $resource)
    {
        $strToSign = $httpVerb."\r\n".$this->apiVersion.$resource."\r\n".$dateTime->format('d.m.Y H:i:s');

        return hash_hmac('sha1', $strToSign, $this->password);
    }
}