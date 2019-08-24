<?php
namespace torneo\V1\Rest\Reglas;

class ReglasResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Reglas\ReglasMapper');
      return new ReglasResource($mapper);
    }
}
