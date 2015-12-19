<?php

namespace Gis1\App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiControllerTrait
{
    protected function apiResponse($data = null, $status = 200)
    {
        $response = JsonResponse::create($data, $status);

        return $response;
    }
}
