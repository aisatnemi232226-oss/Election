<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController, ForgotPasswordController, ResetPasswordController};
use App\Http\Controllers\{DashboardController, ElectionsController, CandidateController, PositionController, VoterController, 
                            UserController, AdminController, RoleController, ViewElectionController, VerifiedVoterController,
                            ElectionArticleController};

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
// 🏠 Dashboard Route (Authenticated)
// =======================
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// =======================
// ⚙️ Admin Routes (Middleware Protected)
// =======================
Route::middleware('auth')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {

        // ✅ Routes for Elections
        Route::resource('elections', ElectionsController::class)->except(['show']);
        Route::get('/elections/content', [ElectionsController::class, 'content'])->name('elections.content');

        // ✅ Routes for Candidates
        Route::resource('candidates', CandidateController::class)->only(['index', 'create', 'store', 'destroy', 'edit', 'update']);
        Route::get('/candidates/filter', [CandidateController::class, 'filter'])->name('candidates.filter');
        Route::get('candidates/new', [CandidateController::class, 'newCandidate'])->name('candidates.new');
        Route::get('candidates/edit/{id}', [CandidateController::class, 'editCandidate'])->name('candidates.edit');
        Route::post('/candidates/uploadQuillImage', [CandidateController::class, 'uploadQuillImage'])->name('quill.image.upload');

        // ✅ Routes for Articles
        Route::resource('election-articles', ElectionArticleController::class);
        Route::post('/articles/uploadQuillImage', [ElectionArticleController::class, 'uploadQuillImage'])->name('articles.quill.image.upload');
        
        Route::resource('positions', PositionController::class);
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
    
        // With other admin routes
        Route::get('/verified-voters/sample', [VerifiedVoterController::class, 'downloadSample'])->name('verified-voters.sample');
    });
});

// =======================
// 🌍 Public Voter Pages
// =======================
Route::view('/', 'voter.home')->name('home');
Route::view('/election', 'voter.election');
Route::view('/registerElection', 'voter.registerElection');
Route::view('/vote', 'voter.vote');
Route::view('/result', 'voter.result');
Route::view('/resultTally', 'voter.resultTally');
Route::view('/contact', 'voter.contact');
Route::view('/new-candidate', 'admin.new-candidate');