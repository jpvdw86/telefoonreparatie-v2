<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostcodeApi {

    protected $container;
    protected $postcodeKey = 'bEUWcAFBFV10OViSzm3T48kRhRL4YoaBacEZQpM3';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $zipcode
     * @param int $houseNumber
     * @return JsonResponse
     */
    public function postcodeophalen($zipcode, $houseNumber = 0)
    {

        $zipcode = str_replace(" ","",strtoupper($zipcode));
        $houseNumber = preg_replace('/\D/', '', $houseNumber);

        $headers = [];
        $headers[] = 'x-api-key: '.$this->postcodeKey;
        $headers[] = 'accept: application/hal+json';

        $url = 'https://api.postcodeapi.nu/v2/addresses/?postcode=' . $zipcode . '&number=' . $houseNumber;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        $data = json_decode($response);

        curl_close($curl);
        if(isset($data->_embedded->addresses['0'])) {
            $output = array(
                "status" => "success",
                "zipcode" => $data->_embedded->addresses['0']->postcode,
                "streetName" => $data->_embedded->addresses['0']->street,
                "province" => $data->_embedded->addresses['0']->province->label,
                "city" => $data->_embedded->addresses['0']->municipality->label
            );
            return new JsonResponse($output);
        }
        return new JsonResponse(["status" => "error"]);

    }

}