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

        if (isset($company['building'])) {
            $company['building'] = BuildingController::rebuildBuildingData($company['building']);
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

        // Slicing
        $limit = min($request->get('limit', $maxLimit), $maxLimit); // Limit cannot be higher than max limit
        $offset = $request->get('offset', 0);

        /**
         * Filters
         */

        // Geo
        $lng = (float)$request->get('lng');
        $lat = (float)$request->get('lat');
        $radius = (float)$request->get('radius'); // km

        // Get building companies
        /** @var Cursor $cursor */
        $qb = $this->createQueryBuilder('companies')
            ->limit($limit)
            ->skip($offset)
        ;

        // Search within circle
        $doGeoSearch = isset($lng, $lat, $radius) && ($lng > 0 || $lat > 0 || $radius > 0);
        if ($doGeoSearch) {
            /**
             * Convert km to radians
             * @see https://docs.mongodb.org/v3.2/tutorial/calculate-distances-using-spherical-geometry-with-2d-geospatial-indexes/
             */
            $radiusRad = $radius / 6378.1;
            $qb->field('building.loc')->geoWithinCenterSphere($lng, $lat, $radiusRad);
        }

        // Search by category

        /**
         * cat1: matches "cat0/cat1", "cat0/cat1/*"
         * cat1/cat2: matches "cat1/cat2", "cat1/cat2/*"
         * /cat1: matches "cat1", "cat1/*", but not "cat0/cat1"
         */
        $category = $request->get('category');

        if (is_scalar($category)) {
            $pathIsFromRoot = preg_match('~^\/~', $category); // Check if starts from slash "/"
            $category = trim($category, '/');

            if (!preg_match('~^[\w_\/]+$~', $category)) {
                // Not valid category path. Force to find 0 companies
                $category = '/_invalid_category_';
            }

            $pathRegexStr = str_replace('/', '\/', $category);
            if ($pathIsFromRoot) {
                $pathRegexStr = '^'. $pathRegexStr;
            }

            $qb->field('categories')->in(array(new \MongoRegex('/'. $pathRegexStr. '/i')));
        }

        $cursor = $qb->getQuery()->execute();
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
