<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FacebookApi
{

    protected $container;
    protected $facebooktoken = [
        "384907524953595" => "EAAC40DRLaLoBAL6IkDbtb0TUp7c54miGXp8YAqqlZAL6nPAtVtu7k6vM2qiIPw2KZBTyigIAF73P7OZAeRlzKZADqA8cJkPqvPRau0h0HUFeiUyZCRREAkrIx5ouDjJW0WDGLg9XCcZBboOYqDmu8C6ZBJyK7tDwbSG0eGOpYJNggZDZD",
        "1434407740164140" => "EAAC40DRLaLoBAGZBm0LCq8ky4VHSMi8lJJIiPee1Sr0f693hdlqcDM5tjE5oSZBbiwnZBaB64XZBRi6wSde3N6bKYPY8vN0CBdeySnHZBi5pEUcm3Y0guzF7upTYTFU19H8dddLf9RqsRLcOwZCuMzSggNbRvWQtDSHVsEeDjmEQZDZD"
    ];

    /**
     * FacebookApi constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $pageid
     * @return array|bool
     * @throws \Exception
     */
    public function getFacebookratingstoSave($pageid = '384907524953595')
    {

        if (!isset($this->facebooktoken[$pageid])) {
            return false;
        }

        $url = 'https://graph.facebook.com/' . $pageid . '/ratings?limit=500&access_token=' . $this->facebooktoken[$pageid];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = json_decode(curl_exec($ch));

        $output = array();
        foreach ($result->data as $value) {
            if (isset($value->review_text) && $value->recommendation_type == 'positive') {
                $rootdir = dirname( $this->container->get('kernel')->getRootDir() );
                $image = file_get_contents('http://graph.facebook.com/' . $value->reviewer->id . '/picture?height=75&type=normal&width=75');
                file_put_contents($rootdir.'/public/images/facebook/'.$value->reviewer->id.'.jpg', $image);
                $outputPrepair = [
                    "Date" => new \DateTime($value->created_time),
                    "FacebookName" => $value->reviewer->name,
                    "FacebookUserId" => $value->reviewer->id,
                    "FacebookUserImage" => 'images/facebook/'.$value->reviewer->id.'.jpg',
                    "Rating" => 100,  //precentages 20% -> 100%
                    "Review" => $value->review_text
                ];
                if(isset($value->rating)){
                    $outputPrepair["Rating"] = (int)($value->rating * 2) . 0;  //precentages 20% -> 100%
                }

                $output[] = $outputPrepair;
            }
        }
        return $output;
    }
}