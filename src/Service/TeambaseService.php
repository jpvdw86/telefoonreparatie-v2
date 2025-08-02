<?php

namespace App\Service;

class TeambaseService
{
    public $apiOffice = 'detelefoonreparatiewinkel';
    protected $apiKey = '5628f7aaa2cef384d597f2f5bdb80333'; // this is new api token, after handshake !
    protected $apiBaseUrl = 'https://backend.tbweb03.teambasecrm.com/api/';

    protected $permissions = [
        'relation/contacts'                                       => ['View', 'Insert', 'Update'],
        'relation/relations'                                      => ['View', 'Insert', 'Update'],
        'product/products'                                        => ['View', 'Insert', 'Update'],
        'relation/paymentConditions'                              => ['View'],
        'financial/invoicePaymentTypes'                           => ['View'],
        'product/taxRates'                                        => ['View'],
        'product/storages'                                        => ['View'],
        'product/storageRecords'                                  => ['View'],
        'users'                                                   => ['View'],
        'settings/documentTemplates/documentTemplateGroups'       => ['View'],
        'orderManagement/deliveryTerms'                           => ['View'],
        'orderManagement/salesQuotes'                             => ['View', 'Insert'],
        'orderManagement/changeSalesQuoteStatus'                  => ['Execute'],
        'orderManagement/salesOrders'                             => ['View'],
        'orderManagement/generateSalesOrderTotalInvoiceDocument'  => ['Execute'],
        'financial/invoices'                                      => ['View', 'Insert'],
        'financial/performInvoicePayment'                         => ['Execute'],
        'financial/generateInvoiceDocument'                       => ['Execute'],
    ];

    public function getPermissions(){
        return $this->permissions;
    }

    /**
     * Perform GET
     */
    public function get($url, $parameters = [], $headers = []) {
        return $this->execute($url,'GET',$parameters, $headers);
    }

    /**
     * Perform POST
     */
    public function post($url, $parameters = [], $headers = [] ) {
        return $this->execute($url, 'POST', $parameters, $headers);
    }

    /**
     * Perform PUT
     */
    public function put($url, $parameters = [], $headers = []) {
        return $this->execute($url, 'PUT', $parameters, $headers);
    }

    /**
     * Perform DELETE
     */
    public function delete($url, $parameters = [], $headers = []) {
        return $this->execute($url, 'DELETE', $parameters, $headers);
    }

    /**
     * Perform HEAD
     */
    public function head($url, $parameters = [], $headers = []) {
        return $this->execute($url, 'HEAD', $parameters, $headers);
    }

    /**
     * Format query parameters
     */
    private function format_query($parameters, $primary = '=', $secondary = '&') {
        $query = "";
        foreach( $parameters as $key => $value ) {
            $pair = [ urlencode($key), urlencode($value) ];
            $query .= implode($primary, $pair) . $secondary;
        }
        return rtrim($query, $secondary);
    }

    /**
     * @param $url
     * @param $method
     * @param $parameters
     * @param array $headers
     * @return \stdClass
     */
    private function execute($url, $method, $parameters, $headers = []): \stdClass
    {

        $defaultHeaders = [
            'Teambase-Api-Office' => $this->apiOffice,
            'Teambase-Api-Key'    => $this->apiKey,
            'Content-type'        => 'application/json',
            'Accept'              => 'application/json',
        ];
        $headers = array_merge($headers, $defaultHeaders);

        $ch = curl_init();
        $curlopt = [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'DTCRW invoice module',
            CURLINFO_HEADER_OUT => true,
        ];
        if (count( $headers ) ) {
            $curlopt[CURLOPT_HTTPHEADER] = [];
            foreach( $headers as $key => $value ) {
                $curlopt[CURLOPT_HTTPHEADER][] = sprintf( "%s:%s", $key, $value );
            }
        }

        if(strtoupper($method) === 'POST') {
            $curlopt[CURLOPT_POST] = TRUE;
        }
        if(strtoupper($method) === 'DELETE') {
            $curlopt[CURLOPT_CUSTOMREQUEST] = "DELETE";
        }
        if(strtoupper($method) === 'PUT') {
            $curlopt[CURLOPT_CUSTOMREQUEST] = "PUT";
        }

        if((strtoupper($method) === 'POST') || (strtoupper($method) === 'PUT')) {
            if (is_array($parameters)) {
                $parameters = $this->format_query($parameters);
            }
            $curlopt[CURLOPT_POSTFIELDS] = $parameters;
        }
        elseif (count($parameters)) {
            $url .= strpos($url, '?')? '&' : '?';
            $url .= $this->format_query($parameters);
        }

        $url = rtrim($this->apiBaseUrl, '/' ) . '/' . ltrim($url, '/');

        $curlopt[CURLOPT_URL] = $url;

        curl_setopt_array($ch, $curlopt);

        $response = curl_exec($ch);
        $result = new \stdClass();

        list($result->headers, $result->response) = array_merge(explode("\r\n\r\n", $response, 2), ['', '']);
        if (strncasecmp($result->headers, 'HTTP/1.1 100 Continue', strlen( 'HTTP/1.1 100 Continue')) == 0) {
            list($continueHeader, $result->headers, $result->response) = explode( "\r\n\r\n", $response, 3 );
        }

        $result->info = (object) curl_getinfo($ch);
        $result->error = curl_error($ch);
        $result->request = $parameters;
        curl_close($ch);

        return $result;
    }
}