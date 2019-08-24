<?php
namespace torneo\V1\Rest\Zona;

class ZonaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Zona\ZonaMapper');
      return new ZonaResource($mapper);
    }
}
