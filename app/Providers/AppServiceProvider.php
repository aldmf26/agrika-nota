<?php

namespace App\Providers;

use App\Models\Nota;
use App\Policies\NotaPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
        \Illuminate\Support\Facades\Blade::directive('indoDateTime', function ($expression) {
            return "<?php echo {$expression} ? \\Carbon\\Carbon::parse({$expression})->locale('id')->translatedFormat('l, d F Y, H.i') : '-'; ?>";
        });

        Gate::policy(Nota::class, NotaPolicy::class);

        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });
    }
}
