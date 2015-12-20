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
     * @return array
     */
    protected function getParameters()
    {
        return $this->getApp()['parameters'];
    }

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
        $parameters = $this->getParameters();

        return $app['mongodb']->selectCollection($parameters['db_name'], $collection);
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

    protected function collectionResponse($total, $collection)
    {
        $data = array(
            'total' => $total,
            'items' => array_values($collection),
        );

        return $this->apiResponse($data);
    }
}
