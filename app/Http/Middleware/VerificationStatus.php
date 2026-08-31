<?php

namespace App\Http\Middleware;

use App\Models\ExchangeRate;
use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificationStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Settings::first()?->id == null){
            Settings::create();
        }

        if(ExchangeRate::first()?->id == null){
            ExchangeRate::create([
                'usd_to_bs' => 6.96
            ]);
        }
        return $next($request);
    }
}
