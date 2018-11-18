<?php
declare(strict_types=1);

namespace App;

use App\Exception\RoutingException;

use Laravel\Lumen\Application as LumenApplication;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use OpenApi;

/**
 * Swagger application.
 *
 * Extends Lumen, adding in the ability to return an Open API JSON payload
 * compatible with swagger. Uses the Open API annotations to generate
 * and cache the application routing.
 */
class SwaggerApplication extends LumenApplication
{
    private $swaggerJson;

    /**
     * @{inheritdoc}
     *
     * @param ?SymfonyRequest $request
     * @return void
     */
    public function run(SymfonyRequest $request = null): void
    {
        $this->swaggerBootstrap();

        parent::run($request);
    }

    /**
     * Returns true if the application is in development mode. Otherwise returns false.
     *
     * @return bool
     */
    public function isDev(): bool
    {
        return 'local' === config('app.env');
    }

    /**
     * Bootstrap swagger application.
     */
    private function swaggerBootstrap()
    {
        // Add the open API JSON route (local development only).
        if ($this->isDev()) {
            throw new \Exception;
            $app = $this;
            $this->router->get('/open-api.json', function () use ($app) {
                return $app->swaggerJson();
            });
        }

        if ($this->isDev()) {
            $routes = $this->swaggerBuild();
        } else {
            $routes = $this->swaggerFetch();
            if ($routes === null) {
                $routes = $this->swaggerBuild();
            }
        }

        $this->swaggerApply($routes);
    }

    /**
     * Builds the swagger array data and serializes it to a cache on the disk.
     *
     * @return array
     */
    private function swaggerBuild(): array
    {
        $data       = json_decode($this->swaggerJson(), true);
        $path       = $this->swaggerCachePath();
        $serialized = base64_encode(serialize($data));

        if (false === @file_put_contents($path, $serialized)) {
            throw new RoutingException(sprintf(
                'Could not write to routing cache file: "%s".',
                $path
            ));
        }

        return $data;
    }

    /**
     * Fetch swagger data array from the cache, or return null if cache does not exist.
     *
     * @return ?array
     */
    private function swaggerFetch(): ?array
    {
        $path = $this->swaggerCachePath();
        if (false === file_exists($path)) {
            return null;
        }

        $data = @file_get_contents($path);
        if (false === $data) {
            throw new RoutingException(sprintf(
                'Could not read from routing cache file: "%s".',
                $path
            ));
        }

        return unserialize(base64_decode($data));
    }

    /**
     * Apply swagger data to application generating routes.
     */
    private function swaggerApply(array $data): void
    {
        foreach ($data['paths'] as $route => $calls) {
            foreach ($calls as $method => $data) {
                $this->router->$method(
                    $route,
                    str_replace('::', '@', $data['operationId'])
                );
            }
        }
    }

    /**
     * Returns the swagger cache path.
     *
     * @return string
     */
    private function swaggerCachePath(): string
    {
        return $this->basePath('var/routes.data.txt');
    }

    /**
     * Generates and returns the open API JSON data.
     *
     * @return string
     */
    private function swaggerJson(): string
    {
        if ($this->swaggerJson === null) {
            $this->swaggerJson
                = OpenApi\scan($this->basePath('app/Http/Controller'))->toJson();
        }

        return $this->swaggerJson;
    }
}
