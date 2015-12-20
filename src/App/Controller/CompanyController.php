<?php

namespace Gis1\App\Controller;


use Doctrine\MongoDB\Cursor;
use MongoId;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyController
{
    use ApiControllerTrait;

    /**
     * @var Application
     */
    protected $app;


    /**
     * @param array $company
     * @return array
     */
    public static function rebuildCompanyData(array $company)
    {
        if (isset($company['_id'])) {
            $company['id'] = (string)$company['_id'];
            unset($company['_id']);
        }

        if (isset($company['building']['_id'])) {
            $company['building']['id'] = (string)$company['building']['_id'];
            unset($company['building']['_id']);
        }

        return $company;
    }


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getCompaniesAction(Request $request)
    {
        $maxLimit = 50;

        // Limit cannot be higher than max limit
        $limit = min($request->get('limit', $maxLimit), $maxLimit);
        $offset = $request->get('offset', 0);


        // Get building companies
        /** @var Cursor $cursor */
        $cursor = $this->createQueryBuilder('companies')
            ->limit($limit)
            ->skip($offset)
            ->getQuery()->execute()
        ;

        $companies = $cursor->toArray();

        return $this->collectionResponse(
            $cursor->count(),
            $this->prepareDataToResponse($companies)
        );
    }

    public function getBuildingCompaniesAction($buildingId, Request $request)
    {
        // Check building exists
        $building = $this->selectCollection('buildings')->findOne(array('_id' => new MongoId($buildingId)));

        if (!$building) {
            throw new NotFoundHttpException();
        }

        // Get building companies
        /** @var Cursor $cursor */
        $cursor = $this->createQueryBuilder('companies')
            ->field('building._id')->equals(new MongoId($buildingId))
            ->exclude('building')
            ->getQuery()->execute()
        ;

        $companies = $cursor->toArray();

        return $this->collectionResponse(
            $cursor->count(),
            $this->prepareDataToResponse($companies)
        );
    }

    /**
     * @return Application
     */
    protected function getApp()
    {
        return $this->app;
    }

    protected function prepareDataToResponse(array $companies)
    {
        foreach ($companies as $idx => $company) {
            $companies[$idx] = static::rebuildCompanyData($company);
        }

        return $companies;
    }
}
