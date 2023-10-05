<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Swagger Fstudio",
 *     @OA\Contact(
 *         email="apiteam@swagger.io"
 *     ),
 * )
 *  @OA\Server(
 *      url="http://127.0.0.1:8000/api/",
 *      description="Development Environment"
 *  )
 *
 *  @OA\Server(
 *      url="https://fstudiovn.id.vn/api/",
 *      description="Staging  Environment"
 * )
 */

class Controller extends BaseController
{

    use AuthorizesRequests, ValidatesRequests;
}
