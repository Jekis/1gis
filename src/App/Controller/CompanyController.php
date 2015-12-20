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
        $companiesTotal = $cursor->count();
        $preparedCompanies = $this->prepareDataToResponse($companies);

        $data = array(
            'total' => $companiesTotal,
            'items' => array_values($preparedCompanies)
        );

        return $this->apiResponse($data);
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
