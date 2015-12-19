<?php

namespace Gis1\App\Controller;


class BuildingController
{
    use ApiControllerTrait;

    public function getListAction()
    {
        $data = array(
            array(
                'location' => 'Bluher 32, Novosibirsk, Russia',
                'x' => 1,
                'y' => 2,
            ),
        );

        return $this->apiResponse($data);
    }
}
