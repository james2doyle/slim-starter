<?php

namespace Tests\Functional;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

use ParagonIE\EasyDB\Factory as EasyDB;
use Phinx\Console\PhinxApplication;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $rollbackDatabase = true;

    /**
     * @var \Slim\App
     */
    public $app;

    /**
     * @var \ParagonIE\EasyDB\Factory
     */
    public $db;

    /**
     * @var \Phinx\Migration\Manager
     */
    public $phinx;

    /**
     * @var string
     */
    public $app_env;

    /**
     * @var string
     */
    public $auth_token;

    public function setUp()
    {
        $config_file = require(__DIR__ . '/../../phinx.php');
        $env = $config_file['environments'];

        $this->app_env = getenv('APP_ENV') ?? 'testing';

        $this->db = EasyDB::create(
            sprintf('%s:host=%s;dbname=%s', $env[$this->app_env]['adapter'], $env[$this->app_env]['host'], $env[$this->app_env]['name']),
            $env[$this->app_env]['user'],
            $env[$this->app_env]['pass'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            ]
        );

        $this->phinx = new PhinxApplication();
        $this->phinx->setAutoExit(false);
        $this->phinx->setCatchExceptions(false);
        $this->phinx->run(new StringInput("rollback -e {$this->app_env} -t 0"), new NullOutput());
        $this->phinx->run(new StringInput("migrate -e {$this->app_env}"), new NullOutput());
    }

    public function runSeeder()
    {
        $this->phinx->run(new StringInput("seed:run -e {$this->app_env}"), new NullOutput());
    }

    public function runSeed($seed)
    {
        $this->phinx->run(new StringInput("seed:run -e {$this->app_env} -s {$seed}"), new NullOutput());
    }

    public function actingAs(string $email)
    {
        $now = new \DateTime();
        $future = new \DateTime('now +2 hours');

        $payload = [
            'iat' => $now->getTimeStamp(),
            'exp' => $future->getTimeStamp(),
            'sub' => $email,
            'scope' => ['*'],
        ];

        $secret = getenv('JWT_SECRET');
        if (empty($secret)) {
            throw new \Exception('No JWT_SECRET env set', 500);
        }

        $this->auth_token = \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');

        return $this;
    }

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     */
    public function runApp($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $env = [
            'REQUEST_METHOD' => $requestMethod,
            'REQUEST_URI' => $requestUri,
        ];

        if (!empty($this->auth_token)) {
            $env['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->auth_token;
        }

        $environment = Environment::mock($env);

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
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

        $this->app = $app;

        // Return the response
        return $response;
    }

    public function get($requestUri, $requestData = null)
    {
        return $this->runApp('GET', $requestUri, $requestData);
    }

    public function post($requestUri, $requestData = null)
    {
        return $this->runApp('POST', $requestUri, $requestData);
    }
}
