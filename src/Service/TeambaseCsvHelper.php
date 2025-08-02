<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class TeambaseCsvHelper
{
    protected $data = [];
    protected $kernel;

    const VATTYPE_NO_CHOISE             = '00'; //GEEN KEUZE
    const VATTYPE_COMPANY_BE            = '01'; //BELGISCHE BTW-PLICHTIGE
    const VATTYPE_INTRACOMMUNAUTAIRE    = '04'; //INTRACOMMUNAUTAIRE
    const VATTYPE_OUTSIDE_EEG           = '06'; //BUITEN EEG
    const VATTYPE_PARTICULIER_BELGIE    = '07'; //BELGISCHE PARTICULIER
    const VATTYPE_PARTICULIER_EEG       = '08'; //EEG PARTICULIER
    const VATTYPE_PARTICULIER_OTHER     = '09'; //PARTICULIER OVERIGE
    const COUNTRY_NL                    = 'NL';
    const COUNTRY_BE                    = 'BE';
    const COUNTRY_OTHER                 = '';


    public function __construct(KernelInterface $kernel){
        $this->kernel = $kernel;
    }

    /**
     * @param $bookRecord
     * @param $invoiceNumber
     * @param int $customerId
     * @param $customerName
     * @param $customerStreet
     * @param string $customerCountry
     * @param string $customerZipcode
     * @param string $customerMunicipality
     * @param string $vatType
     * @param string $vatNumber
     * @param string $customerPhoneNumber
     * @param string $period
     * @param \DateTime|null $invoiceDate
     * @param \DateTime|null $invoiceExpirationDate
     * @param string $comments
     * @param string $reference
     * @param float $invoiceTotalPrice
     * @param string $valuta
     */
    public function addInvoiceRecord(
        $bookRecord,
        $invoiceNumber,
        int $customerId,
        $customerName,
        $customerStreet,
        $customerCountry = self::COUNTRY_BE,
        $customerZipcode = '',
        $customerMunicipality = '',
        $vatType = self::VATTYPE_PARTICULIER_BELGIE,
        $vatNumber = '',
        $customerPhoneNumber = '',
        $period = '',
        \DateTime $invoiceDate = null,
        \DateTime $invoiceExpirationDate = null,
        $comments = '',
        $reference = '',
        float $invoiceTotalPrice = 0.00,
        $valuta = 'EUR'
    ){
        $this->data[$invoiceNumber]['invoiceLine'] = [
            $bookRecord,
            $invoiceNumber,
            $customerId,
            preg_replace("/[^A-Za-z0-9 ]/", "", substr($customerName, 0 ,29)),
            preg_replace("/[^A-Za-z0-9 ]/", "", substr($customerStreet, 0 ,29)),
            preg_replace("/[^A-Za-z0-9 ]/", "", $customerCountry),
            preg_replace("/[^A-Za-z0-9 ]/", "", $customerZipcode),
            preg_replace("/[^A-Za-z0-9 ]/", "", substr($customerMunicipality, 0 , 99)),
            preg_replace("/[^A-Za-z0-9 ]/", "", $vatType),
            preg_replace("/[^A-Za-z0-9 ]/", "", $vatNumber),
            preg_replace("/[^A-Za-z0-9 ]/", "", $customerPhoneNumber),
            '',
            $period,
            $invoiceDate->format('Y/m/d'),
            $invoiceExpirationDate->format('Y/m/d'),
            preg_replace("/[^A-Za-z0-9 ]/", "", $comments),
            preg_replace("/[^A-Za-z0-9 ]/", "", $reference),
            $invoiceTotalPrice,
            $valuta,
        ];
    }

    /**
     * @param $bookRecord
     * @param int $index
     * @param $invoiceNumber
     * @param int $accountNumber
     * @param float $price
     * @param string $btwCode
     * @param float $vat
     * @param string $comment
     */
    public function addBookingLine(
        $bookRecord,
        int $index,
        $invoiceNumber,
        int $accountNumber,
        float $price = 0.00,
        $btwCode = 'D21',
        float $vat = 0.00,
        $comment = ''
    ){
        $this->data[$invoiceNumber]['bookingslines'][] = [
            $bookRecord,
            $invoiceNumber,
            $index,
            $accountNumber,
            $price,
            $btwCode,
            $vat,
            preg_replace("/[^A-Za-z0-9 ]/", "", $comment)
        ];
    }


    /**
     * @param $relation
     * @return array
     */
    public function getAdditionSettingsByTypeCustomer($relation): array
    {
        /** defaults */
        $accountNumber  = 700300;                               /** particulier */
        $vatNumber      = '';                                   /** customer BE */

        $be = false;
        $addressDetails = reset($relation->addresses);

        switch (substr(strtolower($addressDetails->country), 0, 2)){
            case 'be':
                $customerCountry = TeambaseCsvHelper::COUNTRY_BE;
                $vatType = self::VATTYPE_PARTICULIER_BELGIE;
                $be = true;
                break;
            case 'nl':
                $customerCountry = TeambaseCsvHelper::COUNTRY_NL;
                $vatType = self::VATTYPE_PARTICULIER_EEG;
                break;
            default:
                $customerCountry = TeambaseCsvHelper::COUNTRY_OTHER;
                $vatType = self::VATTYPE_PARTICULIER_EEG;

        }

        if(strlen($relation->taxRegistration) > 5){
            $vatNumber = str_replace([' ', '.', ','],'', trim(strtoupper($relation->taxRegistration)));
            $accountNumber = 700600; /** bedrijf binnen eu */
            if($be){
                if(substr($vatNumber, 0, 2) != 'BE'){
                    $vatNumber = "BE{$vatNumber}";
                }
                $vatType = self::VATTYPE_COMPANY_BE;
            } else {
                $vatType = self::VATTYPE_INTRACOMMUNAUTAIRE;
            }
        }

        return [
            'vatType'           => $vatType,
            'accountNumber'     => $accountNumber,
            'customerCountry'   => $customerCountry,
            'vatNumber'         => $vatNumber,
        ];
    }


    /**
     * @param $fileName
     * @return string
     */
    public function getCsV($fileName){
        $projectDir = $this->kernel->getProjectDir();

        @unlink("{$projectDir}/invoice/{$fileName}.csv");

        foreach($this->data as $index => $values){
            $string = '"'.implode('","', $values['invoiceLine']).'"'.PHP_EOL;
            file_put_contents("{$projectDir}/invoice/{$fileName}.csv", $string, FILE_APPEND);

            $csvArray[] = $values['invoiceLine'];
            foreach($values['bookingslines'] as $bookingLine){
                $string = '"'.implode('","', $bookingLine).'"'.PHP_EOL;
                file_put_contents("{$projectDir}/invoice/{$fileName}.csv", $string, FILE_APPEND);
            }
        }
        return "{$projectDir}/invoice/{$fileName}.csv";
    }

}