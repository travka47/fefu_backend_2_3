<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;

class SuggestAppeal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('appealed') === true) {
            return $next($request);
        }

        if (!$request->session()->exists('suggestion_counter'))
        {
            $request->session()->put('suggestion_counter', 0);
            $request->session()->put('transaction_counter', 0);
        }
        $setting = app(Settings::class);
        if ($request->session()->get('suggestion_counter') < $setting->max)
        {
            if ($request->session()->get('transaction_counter') < $setting->frequency) {
                $request->session()->increment('transaction_counter');
            }
            else
            {
                $request->session()->now('suggestion', true);
                $request->session()->put('suggestion_shown', false);
                $request->session()->increment('suggestion_counter');
                $request->session()->put('transaction_counter', 0);
            }
        }
        return $next($request);
    }
}
