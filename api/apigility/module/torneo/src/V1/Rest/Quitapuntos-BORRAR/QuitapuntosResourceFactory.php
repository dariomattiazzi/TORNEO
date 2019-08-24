<?php
namespace torneo\V1\Rest\Quitapuntos;

class QuitapuntosResourceFactory
{
    public function __invoke($services)
    {
        // return new QuitapuntosResource();
        $mapper = $services->get('torneo\V1\Rest\Quitapuntos\QuitapuntosMapper');
        return new QuitapuntosResource($mapper);

    }

}
