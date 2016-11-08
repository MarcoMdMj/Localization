<?php

namespace MarcoMdMj\Localization\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use MarcoMdMj\Localization\Localization;

/**
 * Redirect incorrect requests to the default locale.
 *
 * @package MarcoMdMj\Localization
 */
class LocalizationRedirect
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($destination = app(Localization::class)->shouldRedirect()) {
            app('session')->reflash();

            return new RedirectResponse($destination, 302, ['Vary' => 'Accept-Language']);
        }

        return $next($request);
    }
}
