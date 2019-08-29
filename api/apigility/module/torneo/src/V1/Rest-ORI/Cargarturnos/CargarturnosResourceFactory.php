<?php
namespace torneo\V1\Rest\Cargarturnos;

class CargarturnosResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Cargarturnos\CargarturnosMapper');
      return new CargarturnosResource($mapper);
    }
}
