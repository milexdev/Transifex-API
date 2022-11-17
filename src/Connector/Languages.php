<?php declare(strict_types=1);

namespace Mautic\Transifex\Connector;

use Mautic\Transifex\ApiConnector;
use Mautic\Transifex\Exception\InvalidConfigurationException;
use Psr\Http\Message\ResponseInterface;

/**
 * Transifex API Languages class.
 *
 * @link http://docs.transifex.com/api/languages/
 */
final class Languages extends ApiConnector
{
    /**
     * Create a language for a project.
     *
     * @param string   $slug                The slug for the project
     * @param string   $langCode            The language code for the new language
     * @param string[] $coordinators        An array of coordinators for the language
     * @param array    $options             Optional additional params to send with the request
     * @param bool     $skipInvalidUsername If true, the API call does not fail and instead will return a list of invalid usernames
     *
     * @return ResponseInterface
     *
     * @throws InvalidConfigurationException
     */
    public function createLanguage(
        string $slug,
        string $langCode,
        array $coordinators,
        array $options = [],
        bool $skipInvalidUsername = false
    ): ResponseInterface {
        // Make sure the $coordinators array is not empty
        if (!\count($coordinators)) {
            throw new InvalidConfigurationException('The coordinators array must contain at least one username.');
        }

        $uri = $this->createUri("/api/2/project/$slug/languages/");

        if ($skipInvalidUsername) {
            $uri = $uri->withQuery('skip_invalid_username');
        }

        // Build the required request data.
        $data = [
            'language_code' => $langCode,
            'coordinators'  => $coordinators,
        ];

        // Valid options to check
        $validOptions = ['translators', 'reviewers', 'list'];

        // Loop through the valid options and if we have them, add them to the request data
        foreach ($validOptions as $option) {
            if (isset($options[$option])) {
                $data[$option] = $options[$option];
            }
        }

        $request = $this->createRequest('POST', $uri);
        $request = $request->withBody($this->streamFactory->createStream(\json_encode($data)));

        return $this->client->sendRequest($request);
    }

    /**
     * Delete a language within a project.
     *
     * @param string $project  The project to retrieve details for
     * @param string $langCode The language code to retrieve details for
     *
     * @return ResponseInterface
     */
    public function deleteLanguage(string $project, string $langCode): ResponseInterface
    {
        return $this->client->sendRequest($this->createRequest('DELETE', $this->createUri("/api/2/project/$project/language/$langCode/")));
    }

    /**
     * Get the coordinators for a language team in a project.
     *
     * @param string $project  The project to retrieve details for
     * @param string $langCode The language code to retrieve details for
     *
     * @return ResponseInterface
     */
    public function getCoordinators(string $project, string $langCode): ResponseInterface
    {
        return $this->client->sendRequest($this->createRequest('GET', $this->createUri("/api/2/project/$project/language/$langCode/coordinators/")));
    }

    /**
     * Get information about a given language in a project.
     *
     * @param string $project  The project to retrieve details for
     * @param string $langCode The language code to retrieve details for
     * @param bool   $details  True to add the ?details fragment
     *
     * @return ResponseInterface
     */
    public function getLanguage(string $project, string $langCode, bool $details = false): ResponseInterface
    {
        $uri = $this->createUri("/api/2/project/$project/language/$langCode/");

        if ($details) {
            $uri = $uri->withQuery('details');
        }

        return $this->client->sendRequest($this->createRequest('GET', $uri));
    }

    /**
     * Get a list of languages for a specified project.
     *
     * @param string $project The project to retrieve details for
     *
     * @return ResponseInterface
     */
    public function getLanguages(string $project): ResponseInterface
    {
        return $this->client->sendRequest($this->createRequest('GET', $this->createUri("/api/2/project/$project/languages/")));
    }

    /**
     * Get the reviewers for a language team in a project.
     *
     * @param string $project  The project to retrieve details for
     * @param string $langCode The language code to retrieve details for
     *
     * @return ResponseInterface
     */
    public function getReviewers(string $project, string $langCode): ResponseInterface
    {
        return $this->client->sendRequest($this->createRequest('GET', $this->createUri("/api/2/project/$project/language/$langCode/reviewers/")));
    }

    /**
     * Get the translators for a language team in a project.
     *
     * @param string $project  The project to retrieve details for
     * @param string $langCode The language code to retrieve details for
     *
     * @return ResponseInterface
     */
    public function getTranslators(string $project, string $langCode): ResponseInterface
    {
        return $this->client->sendRequest($this->createRequest('GET', $this->createUri("/api/2/project/$project/language/$langCode/translators/")));
    }

    /**
     * Update the coordinators for a language team in a project.
     *
     * @param string   $project             The project to retrieve details for
     * @param string   $langCode            The language code to retrieve details for
     * @param string[] $coordinators        An array of coordinators for the language
     * @param bool     $skipInvalidUsername If true, the API call does not fail and instead will return a list of invalid usernames
     *
     * @return ResponseInterface
     */
    public function updateCoordinators(
        string $project,
        string $langCode,
        array $coordinators,
        bool $skipInvalidUsername = false
    ): ResponseInterface {
        return $this->updateTeam($project, $langCode, $coordinators, $skipInvalidUsername, 'coordinators');
    }

    /**
     * Update a language within a project.
     *
     * @param string   $slug         The slug for the project
     * @param string   $langCode     The language code for the new language
     * @param string[] $coordinators An array of coordinators for the language
     * @param array    $options      Optional additional params to send with the request
     *
     * @return ResponseInterface
     *
     * @throws InvalidConfigurationException
     */
    public function updateLanguage(
        string $slug,
        string $langCode,
        array $coordinators,
        array $options = []
    ): ResponseInterface {
        // Make sure the $coordinators array is not empty
        if (!\count($coordinators)) {
            throw new InvalidConfigurationException('The coordinators array must contain at least one username.');
        }

        // Build the required request data.
        $data = ['coordinators' => $coordinators];

        // Set the translators if present
        if (isset($options['translators'])) {
            $data['translators'] = $options['translators'];
        }

        // Set the reviewers if present
        if (isset($options['reviewers'])) {
            $data['reviewers'] = $options['reviewers'];
        }

        $request = $this->createRequest('PUT', $this->createUri("/api/2/project/$slug/language/$langCode/"));
        $request = $request->withBody($this->streamFactory->createStream(\json_encode($data)));
        $request = $request->withHeader('Content-Type', 'application/json');

        return $this->client->sendRequest($request);
    }

    /**
     * Update the reviewers for a language team in a project.
     *
     * @param string   $project             The project to retrieve details for
     * @param string   $langCode            The language code to retrieve details for
     * @param string[] $reviewers           An array of reviewers for the language
     * @param bool     $skipInvalidUsername If true, the API call does not fail and instead will return a list of invalid usernames
     *
     * @return ResponseInterface
     */
    public function updateReviewers(string $project, string $langCode, array $reviewers, bool $skipInvalidUsername = false): ResponseInterface
    {
        return $this->updateTeam($project, $langCode, $reviewers, $skipInvalidUsername, 'reviewers');
    }

    /**
     * Base method to update a given language team in a project.
     *
     * @param string   $project             The project to retrieve details for
     * @param string   $langCode            The language code to retrieve details for
     * @param string[] $members             An array of the team members for the language
     * @param bool     $skipInvalidUsername If true, the API call does not fail and instead will return a list of invalid usernames
     * @param string   $team                The team to update
     *
     * @return ResponseInterface
     *
     * @throws InvalidConfigurationException
     */
    protected function updateTeam(
        string $project,
        string $langCode,
        array $members,
        bool $skipInvalidUsername,
        string $team
    ): ResponseInterface {
        // Make sure the $members array is not empty
        if (!\count($members)) {
            throw new InvalidConfigurationException('The team array must contain at least one username.');
        }

        $uri = $this->createUri("/api/2/project/$project/language/$langCode/$team/");

        if ($skipInvalidUsername) {
            $uri = $uri->withQuery('skip_invalid_username');
        }

        $request = $this->createRequest('PUT', $uri);
        $request = $request->withBody($this->streamFactory->createStream(\json_encode($members)));
        $request = $request->withHeader('Content-Type', 'application/json');

        return $this->client->sendRequest($request);
    }

    /**
     * Update the translators for a language team in a project.
     *
     * @param string   $project             The project to retrieve details for
     * @param string   $langCode            The language code to retrieve details for
     * @param string[] $translators         An array of translators for the language
     * @param bool     $skipInvalidUsername If true, the API call does not fail and instead will return a list of invalid usernames
     *
     * @return ResponseInterface
     */
    public function updateTranslators(
        string $project,
        string $langCode,
        array $translators,
        bool $skipInvalidUsername = false
    ): ResponseInterface {
        return $this->updateTeam($project, $langCode, $translators, $skipInvalidUsername, 'translators');
    }
}
