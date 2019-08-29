<?php
namespace torneo\V1\Rest\Cargarlistas;
class CargarlistasResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Cargarlistas\CargarlistasMapper');
      return new CargarlistasResource($mapper);
    }
}
