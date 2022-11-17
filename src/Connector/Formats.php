<?php declare(strict_types=1);

namespace Mautic\Transifex\Connector;

use Mautic\Transifex\ApiConnector;
use Psr\Http\Message\ResponseInterface;

/**
 * Transifex API Formats class.
 *
 * @link http://docs.transifex.com/api/formats/
 */
final class Formats extends ApiConnector
{
    /**
     * Get the supported file formats.
     *
     * @return ResponseInterface
     */
    public function getFormats(): ResponseInterface
    {
        return $this->client->sendRequest($this->createRequest('GET', $this->createUri('/api/2/formats')));
    }
}
