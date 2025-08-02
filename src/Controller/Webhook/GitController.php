<?php


namespace App\Controller\Webhook;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\GitHandler;

class GitController extends AbstractController {

    /**
     * @param GitHandler $gitHandler
     * @param Request $request
     * @return Response
     */
    public function github(GitHandler $gitHandler, Request $request){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        if($status = $gitHandler->parseGithubRequest($request)){
            return new Response('Success -> '. $status,200);
        }
        return new Response('Error',400);
    }

    /**
     * @param GitHandler $gitHandler
     * @param Request $request
     * @return Response
     */
    public function bitbucket(GitHandler  $gitHandler, Request $request){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        if($status = $gitHandler->parseBitbucketRequest($request)){
            return new Response('Success -> '. $status,200);
        }
        return new Response('Error',400);
    }

}