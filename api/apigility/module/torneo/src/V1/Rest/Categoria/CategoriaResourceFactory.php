<?php
namespace torneo\V1\Rest\Categoria;

class CategoriaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Categoria\CategoriaMapper');
      return new CategoriaResource($mapper);
    }
}
