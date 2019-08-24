<?php
namespace torneo\V1\Rest\Goleadoresporcategoria;

class GoleadoresporcategoriaResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('torneo\V1\Rest\Goleadoresporcategoria\GoleadoresporcategoriaMapper');
        return new GoleadoresporcategoriaResource($mapper);
    }
}
