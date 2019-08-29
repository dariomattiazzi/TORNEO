<?php
namespace torneo\V1\Rest\Fixture;

class FixtureResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('torneo\V1\Rest\Fixture\FixtureMapper');
        return new FixtureResource($mapper);

    }
}
