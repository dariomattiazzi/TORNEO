<?php
namespace torneo\V1\Rest\Turno;

class TurnoResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Turno\TurnoMapper');
      return new TurnoResource($mapper);
    }
}
