<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'https://newgamer.lt/gatesCallback/rth87k4uil15632v1df9847894ikl156g1fQW941G8K41UIG98L6',
    ];
}
