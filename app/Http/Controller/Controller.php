<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @OA\Info(title="My First API", version="0.1")
 */
class Controller extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/resource.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function testRoute(): void
    {
        exit('here');
    }

    /**
     * @OA\Get(
     *     path="/api/resource2.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function testRoute2(): void
    {
        exit('here too');
    }
}
