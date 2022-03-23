<?php

namespace pxlrbt\FilamentSpotlight;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;

class InjectSpotlightMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        app('view')->startPush('scripts', Blade::render("@livewire('livewire-ui-spotlight')"));

        return $next($request);
    }
}
