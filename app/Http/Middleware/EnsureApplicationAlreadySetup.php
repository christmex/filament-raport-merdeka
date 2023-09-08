<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationAlreadySetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if((auth()->user()->email != 'super@sekolahbasic.sch.id') && Route::current()->getName() != 'filament.admin.pages.dashboard'){
            if(!(SchoolYear::active() && SchoolTerm::active())){
                Notification::make()
                    ->warning()
                    ->title('Whopps, cant do that :(')
                    ->body('This application need to be configurate first by admin, please contact your admin')
                    ->send();
                return redirect()->route('filament.admin.pages.dashboard');
            }
        }

        return $next($request);
    }
}
