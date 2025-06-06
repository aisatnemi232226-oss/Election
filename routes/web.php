<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController, ForgotPasswordController, ResetPasswordController};
use App\Http\Controllers\{DashboardController, ElectionsController, CandidateController, PositionController, VoterController, 
                            UserController, AdminController, RoleController, ViewElectionController, VerifiedVoterController,
                            ElectionArticleController, ArticleController, SearchController};

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

// =======================
// 🔐 Authentication Routes
// =======================
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// ============================
// 🔑 Password Reset Routes
// ============================
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->name('password.update');
});

// =======================
// 🏠 Dashboard Route (Authenticated) - FIXED
// =======================
Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware('auth');

// =======================
// ⚙️ Admin Routes (Middleware Protected)
// =======================
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // ✅ Routes for Search Box
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::post('/search', [SearchController::class, 'search']);

    // ✅ Routes for Elections
    Route::resource('elections', ElectionsController::class)->except(['show']);
    Route::get('/elections/content', [ElectionsController::class, 'content'])->name('elections.content');

    // ✅ Routes for Articles (general)
    Route::resource('articles', ArticleController::class);
    Route::post('/quill-image-upload', [ArticleController::class, 'uploadQuillImage'])->name('quill.image.upload');
   
    // ✅ Routes for Election-Articles - FIXED ORGANIZATION
    Route::prefix('election-articles')->name('election-articles.')->group(function () {
        Route::get('/', [ElectionArticleController::class, 'index'])->name('index');
        Route::get('/create', [ElectionArticleController::class, 'create'])->name('create');
        Route::post('/', [ElectionArticleController::class, 'store'])->name('store');
        Route::get('/{id}', [ElectionArticleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ElectionArticleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ElectionArticleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ElectionArticleController::class, 'destroy'])->name('destroy');
        
        // Image upload routes
        Route::post('/upload-image', [ElectionArticleController::class, 'uploadImage'])->name('upload-image');
        Route::post('/featured-image/upload', [ArticleController::class, 'uploadFeaturedImage'])->name('featured-image.upload');
    });

    // ✅ Routes for Candidates (Updated Structure)
    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('/', [CandidateController::class, 'index'])->name('index');
        Route::get('/create', [CandidateController::class, 'create'])->name('create');
        Route::post('/', [CandidateController::class, 'store'])->name('store');
        Route::get('/paginateCandidates', [CandidateController::class, 'paginateCandidates'])->name('paginate');
        Route::get('/filter', [CandidateController::class, 'filter'])->name('filter');
        Route::post('/uploadQuillImage', [CandidateController::class, 'uploadQuillImage'])->name('quill.image.upload');
        Route::post('/upload-image', [CandidateController::class, 'uploadQuillImage'])->name('upload-image');
        Route::get('/{id}', [CandidateController::class, 'show'])->name('show');
        Route::get('/{id}/json', [CandidateController::class, 'showJson'])->name('showJson');
        Route::get('/{id}/edit', [CandidateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CandidateController::class, 'update'])->name('update');
        Route::delete('/{id}', [CandidateController::class, 'destroy'])->name('destroy');
    });

    // ✅ Routes for Positions
    Route::resource('position', PositionController::class)->only(['index', 'create', 'store', 'destroy', 'edit', 'update']);
    Route::get('/position/paginatePositions', [PositionController::class, 'paginatePositions'])->name('position.paginate');

    // ✅ Other Admin Resources
    Route::resource('voters', VoterController::class);
    Route::resource('users', UserController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('view-election', ViewElectionController::class);

    // ✅ Routes for Verified-Voters
    Route::get('/verified-voters', [VerifiedVoterController::class, 'index'])->name('verified-voters.index');
    Route::post('/verified-voters/import', [VerifiedVoterController::class, 'import'])->name('verified-voters.import');
    Route::get('/verified-voters/export', [VerifiedVoterController::class, 'export'])->name('verified-voters.export');
    Route::get('/verified-voters/search', [VerifiedVoterController::class, 'search'])->name('verified-voters.search');
    Route::delete('/verified-voters/{id}', [VerifiedVoterController::class, 'destroy'])->name('verified-voters.destroy');
    Route::get('/verified-voters/sample', [VerifiedVoterController::class, 'downloadSample'])->name('verified-voters.sample');
});

// =======================
// 🌍 Public Voter Pages
// =======================
Route::view('/', 'voter.home')->name('home');
Route::view('/election', 'voter.election')->name('election');
Route::view('/registerElection', 'voter.registerElection')->name('registerElection');
Route::view('/vote', 'voter.vote')->name('vote');
Route::view('/result', 'voter.result')->name('result');
Route::view('/resultTally', 'voter.resultTally')->name('resultTally');
Route::view('/contact', 'voter.contact')->name('contact');