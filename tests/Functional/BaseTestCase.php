<?php

namespace Tests\Functional;

use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Slim\Http\UploadedFile;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{

	protected $settings;

    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct( $name, $data, $dataName );
		$this->settings = require __DIR__ . '/../settings.php';
	}

	/**
	 * Process the application given a request method and URI
	 *
	 * @param $method
	 * @param null $postData
	 * @param array $uploadedFiles
	 * @return \Slim\Http\Response
	 */
    public function runApiCall($method, $postData = null, $uploadedFiles = [])
    {
    	$requestMethod = $postData ? 'POST' : 'GET';
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => "/bot{$this->settings['botApiToken']}/{$method}"
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($postData)) {
            $request = $request->withParsedBody($postData);
        }

        if (isset($uploadedFiles)) {
			foreach ($uploadedFiles as &$uploadedFile) {
				$pathinfo = pathinfo($uploadedFile);
				$uploadedFile = new UploadedFile(
					$uploadedFile
				);
        	}
            $request = $request->withUploadedFiles($uploadedFiles);
        }

		// Set up a response object
        $response = new Response();

        // Use the application settings
        $settings = require __DIR__ . '/../../src/settings.php';

        // Instantiate the application
        $app = new App($settings);

        // Set up dependencies
        require __DIR__ . '/../../src/dependencies.php';

        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../../src/middleware.php';
        }

        // Register routes
        require __DIR__ . '/../../src/routes.php';

        // Process the application
        $response = $app->process($request, $response);

        // Return the response
        return $response;
    }

    function isOk(ResponseInterface $response) {
    	$body = json_decode($response->getBody()->getContents());
		$this->assertEquals(true, $body->ok);
    	$this->assertEquals(200, $response->getStatusCode());
	}
}
