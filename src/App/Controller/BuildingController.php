<?php

namespace Gis1\App\Controller;


use Doctrine\MongoDB\Cursor;
use Doctrine\MongoDB\Query\Builder;
use Silex\Application;

class BuildingController
{
    use ApiControllerTrait;

    /**
     * @var Application
     */
    protected $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getListAction()
    {
        /** @var Builder $qb */
        $qb = $this->app['mongodb']
            ->selectCollection('1gis', 'buildings')
            ->createQueryBuilder()
        ;

        /** @var Cursor $cursor */
        $cursor = $qb->find()->limit(50)->getQuery()->execute();
        $data = $cursor->toArray();

        return $this->apiResponse($data);
    }

    /**
     * @return Application
     */
    protected function getApp()
    {
        return $this->app;
    }
}
