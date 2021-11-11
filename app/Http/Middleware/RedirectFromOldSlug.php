<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;

class RedirectFromOldSlug
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
        $url = parse_url($request->url());
        $tail = array_key_exists('path', $url) ? $url['path'] : '';
        $redirect = Redirect::query()
            ->where('old_slug', $tail)
            ->orderByRaw('created_at DESC, id DESC')
            ->first();
        $newSlug = null;

        while ($redirect !== null)
        {
            $tail = $redirect->new_slug;
            $newSlug = $redirect;
            $redirect = Redirect::query()
                ->where('old_slug', $tail)
                ->where('created_at', '>', $redirect->created_at)
                ->orderByRaw('created_at DESC, id DESC')
                ->first();
        }
        if ($newSlug !== null)
            return redirect($tail);

        return $next($request);
    }
}
