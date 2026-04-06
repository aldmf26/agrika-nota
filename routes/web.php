<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;
use Illuminate\Support\Facades\Auth;

/**
 * WELCOME - Public route
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * PUBLIC NOTA VIEW - View nota detail without login
 */
Route::get('/v/{nomor_nota}', [\App\Http\Controllers\PublicNotaController::class, 'show'])
    ->name('nota.public_view');

/**
 * DOCUMENTATION ROUTES - Public routes for documentation
 */
Route::get('/docs', function () {
    return view('docs.index');
})->name('docs.index');

Route::get('/docs/user-guide', function () {
    return view('docs.user-guide');
})->name('docs.user-guide');

Route::get('/docs/workflow', function () {
    return view('docs.workflow');
})->name('docs.workflow');

/**
 * AUTH ROUTES - Manual Login/Register
 */
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
})->middleware('guest');

/**
 * REGISTER - Disabled publicly (Now handled by Super Admin) 
 */
// Route::get('/register', function () {
//     return view('auth.register');
// })->name('register')->middleware('guest');
// 
// Route::post('/register', function (\Illuminate\Http\Request $request) {
//     // Implementation moved to Admin/UserController
// })->middleware('guest');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

/**
 * AUTHENTICATED ROUTES - Protected by 'auth' middleware
 */
Route::middleware(['auth', 'verified'])->group(function () {

    /**
     * DASHBOARD - Home page for logged in users
     */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /**
     * NOTA ROUTES - CRUD dan approval
     */
    Route::group(['prefix' => 'nota', 'as' => 'nota.'], function () {

        // List semua nota (index)
        Route::get('/', [NotaController::class, 'index'])
            ->name('index');

        // Form input nota baru
        Route::get('/create', [NotaController::class, 'create'])
            ->name('create');

        // Store nota ke database
        Route::post('/', [NotaController::class, 'store'])
            ->name('store');

        // Show detail nota
        Route::get('/{nota}', [NotaController::class, 'show'])
            ->name('show');
            
        // Print nota
        Route::get('/{nota}/print', [NotaController::class, 'print'])
            ->name('print');

        // Edit nota (pending atau rejected)
        Route::get('/{nota}/edit', [NotaController::class, 'edit'])
            ->name('edit');

        // Update nota
        Route::put('/{nota}', [NotaController::class, 'update'])
            ->name('update');

        // Submit nota dari draft ke pending (deprecated - not used anymore)
        Route::post('/{nota}/submit', [NotaController::class, 'submit'])
            ->name('submit');

        // Approve nota (approver only)
        Route::post('/{nota}/approve', [NotaController::class, 'approve'])
            ->name('approve');

        // Reject nota (approver only)
        Route::post('/{nota}/reject', [NotaController::class, 'reject'])
            ->name('reject');

        // Void nota (approver/super_admin only)
        Route::post('/{nota}/void', [NotaController::class, 'void'])
            ->name('void');

        // Delete nota - soft delete (draft only)
        Route::delete('/{nota}', [NotaController::class, 'destroy'])
            ->name('destroy');

        // Restore deleted nota (within 3 months)
        Route::post('/{id}/restore', [NotaController::class, 'restore'])
            ->name('restore');
    });

    /**
     * MANAGEMENT ROUTES - Super admin only
     */
    Route::middleware(['role:super_admin'])->group(function () {

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
            // Divisi management
            Route::resource('divisi', \App\Http\Controllers\Admin\DivisiController::class);
            
            // User management
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
            
            // System operations
            Route::post('/system/reset', [\App\Http\Controllers\Admin\SystemController::class, 'reset'])
                ->name('system.reset');
            
            // Reports routes
            Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
                ->name('reports.index');
            Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])
                ->name('reports.export');
        });
    });
});
