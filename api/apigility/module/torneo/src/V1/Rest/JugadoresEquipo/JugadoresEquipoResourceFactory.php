<?php
namespace torneo\V1\Rest\JugadoresEquipo;

class JugadoresEquipoResourceFactory
{
    public function __invoke($services)
    {
//        return new JugadoresEquipoResource();
        $mapper = $services->get('torneo\V1\Rest\JugadoresEquipo\JugadoresEquipoMapper');
        return new JugadoresEquipoResource($mapper);
    }
}
