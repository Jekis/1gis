<?php

namespace Gis1\App\Controller;


use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Query\Builder;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiControllerTrait
{
    /**
     * @return Application
     */
    abstract protected function getApp();

    /**
     * @param string $collection
     * @return Builder
     */
    protected function createQueryBuilder($collection)
    {
        return $this->selectCollection($collection)->createQueryBuilder();
    }

    /**
     * @param string $collection
     * @return Collection
     */
    protected function selectCollection($collection)
    {
        $app = $this->getApp();

        // TODO: Get DB name from parameters
        return $app['mongodb']->selectCollection('1gis', $collection);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function apiResponse($data = null, $status = 200)
    {
        $response = JsonResponse::create($data, $status);

        return $response;
    }
}
