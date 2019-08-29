<?php
namespace torneo\V1\Rest\Sancionadosvuelven;

class SancionadosvuelvenResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('torneo\V1\Rest\Sancionadosvuelven\SancionadosvuelvenMapper');
        return new SancionadosvuelvenResource($mapper);
    }
}
