<?php
namespace torneo\V1\Rest\Equipo;

class EquipoResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Equipo\EquipoMapper');
      return new EquipoResource($mapper);
    }
}
