<?php
namespace torneo\V1\Rest\Reporte;

class ReporteResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Reporte\ReporteMapper');
      return new ReporteResource($mapper);
    }
}
