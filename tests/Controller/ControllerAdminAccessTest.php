<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use DirectoryIterator;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Controller "smoke" test
 */
class ControllerAdminAccessTest extends WebTestCase
{
    private array $exceptions
        = [
            'default'                  => [
                'statusCodes' => ['GET' => 200],
            ],
            'login'                    => [
                'statusCodes' => ['GET' => 200],
            ],
            'user_index'               => [
                'statusCodes' => ['GET' => 200],
            ],
            'user_new'                 => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'user_show'                => [
                'statusCodes' => ['GET' => 200],
            ],
            'user_edit'                => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'connect_google_api_token' => [
                'statusCodes' => ['GET' => 200],
            ],
        ];

    /**
     * @throws Exception
     */
    public function testRoutes(): void
    {
        $client = static::createClient();

        $user = static::$container->get(UserRepository::class)
            ->findOneByEmail('admin@example.com');

        $routeLoader = static::$container
            ->get('routing.loader');

        foreach (
            new DirectoryIterator(__DIR__.'/../../src/Controller') as $item
        ) {
            if (
                $item->isDot()
                || $item->isDir()
                || in_array(
                    $item->getBasename(),
                    ['.gitignore', 'GoogleController.php']
                )
            ) {
                continue;
            }

            $client->loginUser($user);
            $routerClass = 'App\Controller\\'.basename(
                    $item->getBasename(),
                    '.php'
                );
            $routes = $routeLoader->load($routerClass)->all();

            $this->processRoutes($routes, $client);
        }
    }

    private function processRoutes(array $routes, KernelBrowser $browser): void
    {
        foreach ($routes as $routeName => $route) {
            $defaultId = 1;
            $expectedStatusCodes = [];
            if (array_key_exists($routeName, $this->exceptions)) {
                if (array_key_exists(
                    'statusCodes',
                    $this->exceptions[$routeName]
                )
                ) {
                    $expectedStatusCodes = $this->exceptions[$routeName]['statusCodes'];
                }
                if (array_key_exists('params', $this->exceptions[$routeName])) {
                    $params = $this->exceptions[$routeName]['params'];
                    if (array_key_exists('id', $params)) {
                        $defaultId = $params['id'];
                    }
                }
            }

            $methods = $route->getMethods() ?: ['GET'];
            $path = str_replace('{id}', $defaultId, $route->getPath());
            $out = false;
            foreach ($methods as $method) {
                $expectedStatusCode = 302;
                if (array_key_exists($method, $expectedStatusCodes)) {
                    $expectedStatusCode = $expectedStatusCodes[$method];
                }
                if ($out) {
                    echo sprintf(
                        'Testing: %s - %s Expected: %s ... ',
                        $method,
                        $path,
                        $expectedStatusCode,
                    );
                }

                $browser->request($method, $path);

                if ($out) {
                    echo sprintf(
                            ' got: %s',
                            $browser->getResponse()->getStatusCode()
                        ).PHP_EOL;
                }

                self::assertEquals(
                    $expectedStatusCode,
                    $browser->getResponse()->getStatusCode(),
                    sprintf(
                        'failed: %s (%s) with method: %s',
                        $routeName,
                        $path,
                        $method
                    )
                );
            }
        }
    }
}
