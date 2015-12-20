<?php

namespace Gis1\App\Controller;


use Doctrine\MongoDB\Cursor;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BuildingController
{
    use ApiControllerTrait;

    /**
     * @var Application
     */
    protected $app;


    /**
     * @param array $building
     * @return array
     */
    public static function rebuildBuildingData(array $building)
    {
        if (isset($building['_id'])) {
            $building['id'] = (string)$building['_id'];
            unset($building['_id']);
        }

        return $building;
    }


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getListAction(Request $request)
    {
        $maxLimit = 50;

        // Slicing
        $limit = min($request->get('limit', $maxLimit), $maxLimit); // Limit cannot be higher than max limit
        $offset = $request->get('offset', 0);

        // Get building
        /** @var Cursor $cursor */
        $cursor = $this->createQueryBuilder('buildings')
            ->limit($limit)
            ->skip($offset)
            ->getQuery()->execute()
        ;

        $buildings = $cursor->toArray();

        return $this->collectionResponse(
            $cursor->count(),
            $this->prepareDataToResponse($buildings)
        );
    }

    /**
     * @param array $buildings
     * @return array
     */
    protected function prepareDataToResponse(array $buildings)
    {
        foreach ($buildings as $idx => $building) {
            $buildings[$idx] = static::rebuildBuildingData($building);
        }

        return $buildings;
    }

    /**
     * @return Application
     */
    protected function getApp()
    {
        return $this->app;
    }
}
