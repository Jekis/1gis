<?php

$app->get('/api/v1/buildings', 'app.building_controller:getListAction');
$app
    ->get('/api/v1/buildings/{buildingId}/companies', 'app.company_controller:getBuildingCompaniesAction')
    ->assert('buildingId', '[a-z0-9]{24}')
;
