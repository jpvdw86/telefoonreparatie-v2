<?php

namespace App\Service;

class TeambaseHelper
{
    protected $teambaseService;

    /**
     * TeambaseHelper constructor.
     * @param TeambaseService $teambaseService
     */
    public function __construct(TeambaseService $teambaseService)
    {
        $this->teambaseService = $teambaseService;
    }


    /**
     * @return object
     */
    private function getPermissionsRequest(){
        foreach($this->teambaseService->getPermissions() as $resource => $resource_permissions) {
            foreach($resource_permissions as $resource_permission) {
                $permissions[] = (object) [
                    'resource' => $resource,
                    'permission' => $resource_permission,
                ];
            }
        }

        return (object) [
            'title' => "Invoice module DTCRW",
            'externalReference' => md5( serialize(['Jean-Paul van der Wegen', "Invoice module DTCRW", "1.0.0", "www.telefoonreparatiebus.nl"])),
            'isUserLocked' => true,
            'permissions' => $permissions,
        ];
    }


    /**
     * @param $tempApiToken
     * @return string
     */
    public function getHandshakeApiToken($tempApiToken)
    {
        $result = $this->teambaseService->post(
            'keyManagement/performHandshake',
            json_encode($this->getPermissionsRequest()),
            [
                'Teambase-Api-Office' => $this->teambaseService->apiOffice,
                'Teambase-Api-Key'    => $tempApiToken,
                'Content-type'        => 'application/json',
                'Accept'              => 'application/json',
            ]
        );
        $handshake_response = @json_decode($result->response);
        var_dump($result);
        if(($result->info->http_code == 200) && is_object($handshake_response))
        {
            if ($handshake_response->isDisabled == "0") {
                echo ">> ".$handshake_response->apiKey. " <<";
                return $handshake_response->apiKey;
            }
        }
        return "Handschake error";
    }


    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return false|mixed
     */
    public function getInvoiceBetween(\DateTime $start, \DateTime $end): ?object
    {
        $result = $this->teambaseService->get(
            'financial/invoices',
            [
                'entityFilter' => '(invoiceDate ge "'.$start->getTimestamp().'") AND (invoiceDate le "'.$end->getTimestamp().'")'
            ]
        );

        $result->maxPageNumbers = 1;
        if($result->info->http_code === 200){
            foreach (explode("\r\n", $result->headers) as $i => $line) {
                if(strpos($line, 'Teambase-Entity-Page-Count') !== false){
                    $pageSize = str_replace('Teambase-Entity-Page-Count: ', '' , $line);
                    $result->maxPageNumbers = intval($pageSize);
                }
            }


            $output = @json_decode($result->response, true, 512, JSON_THROW_ON_ERROR);
            if ($result->maxPageNumbers && $result->maxPageNumbers > 1) {
                for ($i = 1; $i < $result->maxPageNumbers; $i++) {
                    $entitiesSkip = 25 * $i;
                    $resultExtended = $this->teambaseService->get(
                        'financial/invoices',
                        [
                            'entityFilter' => '(invoiceDate ge "' . $start->getTimestamp() . '") AND (invoiceDate le "' . $end->getTimestamp() . '")',
                            'entitiesSkip' => $entitiesSkip
                        ]
                    );
                    if ($resultExtended) {
                        $extend = json_decode($resultExtended->response, true, 512, JSON_THROW_ON_ERROR);
                        if(is_array($extend)) {
                            $output = array_merge($output, $extend);
                        } else {
                            echo $resultExtended->response.PHP_EOL;
                        }
                    }
                }
                return json_decode(json_encode($output));
            }
        }

        echo "Error Search invoice: ";
        return null;
    }

    /**
     * @param $invoice_reference
     * @return mixed|null
     */
    public function loadInvoice($invoice_reference) {
        $invoice_entity = null;
        $result = $this->teambaseService->get($invoice_reference);

        $invoice_entity_response = @json_decode($result->response);
        if(($result->info->http_code == 200) && is_object($invoice_entity_response)){
            $invoice_entity = $invoice_entity_response;
        }
        elseif ($result->info->http_code != 200) {
            echo "Error Load invoice: ". var_dump($result);
        }
        return $invoice_entity;
    }

    /**
     * @param $invoice_number
     * @return mixed|null
     */
    public function searchInvoiceEntityByInvoiceNumber($invoice_number) {
        $invoice_entity = NULL;
        $result = $this->teambaseService->get(
            'financial/invoices',
            [ 'entityFilter' => 'invoiceNumberFull eq "' . preg_replace( "/[^C0-9]/", "", $invoice_number) . '"' ]
        );

        $invoices = @json_decode($result->response);
        if(($result->info->http_code == 200) && is_array($invoices) && count($invoices))
        {
            $invoice_entity = $invoices[0];
        }
        elseif($result->info->http_code != 200) {
            echo "Error Search invoice: " . var_dump($result);
        }
        return $invoice_entity;
    }

    /**
     * @param $invoice_entity
     * @return mixed
     */
    public function insertInvoice($invoice_entity) {
        $result = $this->teambaseService->post(
            'financial/invoices',
            json_encode($invoice_entity)
        );
        $invoices = @json_decode($result->response);
        if(($result->info->http_code == 201 ) && is_array($invoices) && count($invoices)) {
            $invoice_entity = $invoices[0];
        }
        else {
           echo "Error insert Invoice: " . var_dump($result);
        }
        return $invoice_entity;
    }

    /**
     * @param $id
     * @return false|mixed
     */
    public function getRelationByDebitorNumber($id) {
        $user_entity = null;
        $result = $this->teambaseService->get(
            'relation/relations',
            ['entityFilter' => 'debitorNumber eq ' . ((int) $id) ]
        );
        $relation = @json_decode($result->response );
        if(( $result->info->http_code == 200 ) && is_array($relation) && count($relation)) {
            return $relation[0];
        }
        return false;
    }


    /**
     * @param $invoiceHref
     * @return false|mixed
     */
    public function generateInvoiceDocument($invoiceNumber, $invoiceHref) {
        $generate_invoice_document_input = (object) [
            'invoice'       => $invoiceHref,
            'user'          => '',
            'preferredName' => $invoiceNumber,
            'isByEmail'     => false
        ];


        $generated_document_object = null;
        $result = $this->teambaseService->post(
            'financial/generateInvoiceDocument',
            json_encode( $generate_invoice_document_input)
        );

        $generated_document = @json_decode( $result->response );
        if(($result->info->http_code == 200 ) && is_object($generated_document))
        {
            return $generated_document;
        }
        echo "Generate Invoice error: ". var_dump($result);

        return false;
    }
}