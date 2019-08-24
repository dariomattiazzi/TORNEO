<?php
namespace torneo\V1\Rest\Torneo;

class TorneoResourceFactory
{
    public function __invoke($services)
    {
//         return new TorneoResource();
        $mapper = $services->get('torneo\V1\Rest\Torneo\TorneoMapper');
        return new TorneoResource($mapper);
    }
}
