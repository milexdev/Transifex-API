<?php declare(strict_types=1);

namespace Mautic\Transifex;

/**
 * Interface defining a factory responsible for creating Transifex API objects.
 */
interface FactoryInterface
{
    /**
     * Factory method to create API connectors.
     *
     * @param string $name    Name of the API object to retrieve
     * @param array  $options Transifex options array
     *
     * @return ApiConnector
     *
     * @throws Exception\UnknownApiConnectorException
     */
    public function createApiConnector(string $name, array $options = []): ApiConnector;
}
