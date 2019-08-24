<?php
namespace torneo\V1\Rest\Nosepresenta;

class NosepresentaResourceFactory
{
    public function __invoke($services)
    {
        // return new NosepresentaResource();
        $mapper = $services->get('torneo\V1\Rest\Nosepresenta\NosepresentaMapper');
        return new NosepresentaResource($mapper);
    }
}
