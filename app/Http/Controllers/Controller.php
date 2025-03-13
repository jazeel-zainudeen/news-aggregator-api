<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="A RESTful API built with Laravel that aggregates news articles from multiple sources, allowing users to browse, search, and personalize their news feed."
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 */
abstract class Controller
{
    //
}
