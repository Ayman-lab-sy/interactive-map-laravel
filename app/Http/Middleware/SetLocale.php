<?php

namespace App\Http\Middleware;

use App\Models\ContactInfo;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use URL;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        app()->setLocale($request->segment(1));

        URL::defaults(['locale' => $request->segment(1)]);
        View::share('locale', app()->getLocale());
        $site = SiteSetting::allJson(app()->getLocale(), 'site');
        $footerC = ContactInfo::where('in_footer', 1)->where('type','<>', 'social')->get()->groupBy('type');
        $footerS = ContactInfo::where('in_footer', 1)->where('type', 'social')->get();
        View::share('sFooter', $footerS);
        View::share('cFooter', $footerC);
        View::share('site', $site);

        return $next($request);
    }
}
