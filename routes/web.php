<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController, ForgotPasswordController, ResetPasswordController};
use App\Http\Controllers\{DashboardController, ElectionsController, CandidateController, PositionController, VoterController, 
    UserController, AdminController, RoleController, ViewElectionController, VerifiedVoterController, ElectionArticleController, 
    ArticleController, SearchController, ResultsController, ReportsController, AdminRoleController, ProfileSettingsController, 
    AuditLogController, AcademicYearController, CourseController, ElectionPartyController};
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

// ======================= 
// 🔐 Authentication Routes
// ======================= 
Route::controller(LoginController::class)->group(function (): void {
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

    // ✅ Routes for Articles 
    Route::resource('articles', ArticleController::class);
    Route::post('/quill-image-upload', [ArticleController::class, 'uploadQuillImage'])->name('quill.image.upload');

    // ✅ Routes for Election-Articles
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

    // ✅ Routes for Candidates
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

    // ✅ Routes for Election Parties
    Route::prefix('election-parties')->name('election-parties.')->group(function () {
        Route::get('/', [ElectionPartyController::class, 'index'])->name('index');
        Route::get('/create', [ElectionPartyController::class, 'create'])->name('create');
        Route::post('/', [ElectionPartyController::class, 'store'])->name('store');
        Route::get('/{id}', [ElectionPartyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ElectionPartyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ElectionPartyController::class, 'update'])->name('update');
        Route::delete('/{id}', [ElectionPartyController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ElectionPartyController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{id}/data', [ElectionPartyController::class, 'getPartyData'])->name('data');
    });

    // ✅ Routes for Positions
    Route::resource('positions', PositionController::class)->only(['index', 'create', 'store', 'destroy', 'edit', 'update']);
    Route::get('/positions/paginatePositions', [PositionController::class, 'paginatePositions'])->name('positions.paginate');
    Route::post('/positions/{id}/restore', [PositionController::class, 'restore'])->name('positions.restore');
    Route::delete('/positions/{id}/force-delete', [PositionController::class, 'forceDelete'])->name('positions.force-delete');

    // ✅ Other Admin Resources
    Route::resource('voters', VoterController::class);
    Route::resource('users', UserController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('view-election', ViewElectionController::class);

    // ✅ Routes for Admin Roles Management
    Route::prefix('admin-roles')->name('admin-roles.')->group(function () {
        Route::get('/', [AdminRoleController::class, 'index'])->name('index');
        Route::get('/create', [AdminRoleController::class, 'create'])->name('create');
        Route::post('/', [AdminRoleController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminRoleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminRoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminRoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminRoleController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/data', [AdminRoleController::class, 'getRoleData'])->name('data');
    });

    // ✅ Routes for Profile Settings
    Route::prefix('profile-settings')->name('profile-settings.')->group(function () {
        Route::get('/', [ProfileSettingsController::class, 'index'])->name('index');
        Route::put('/update', [ProfileSettingsController::class, 'update'])->name('update');
        Route::delete('/remove-picture', [ProfileSettingsController::class, 'removeProfilePicture'])->name('remove-picture');
    });

    // ✅ Routes for Audit Logs
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
    });

  // ✅ Routes for Academic Year Management
    Route::prefix('academic-year')->name('academic-year.')->group(function () {
        Route::get('/', [AcademicYearController::class, 'index'])->name('index');
        Route::post('/', [AcademicYearController::class, 'store'])->name('store');
        Route::put('/{id}', [AcademicYearController::class, 'update'])->name('update');
        Route::delete('/{id}', [AcademicYearController::class, 'destroy'])->name('destroy');
        Route::post('/toggle-status', [AcademicYearController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-delete', [AcademicYearController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // ✅ Routes for Courses Management
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/create', [CourseController::class, 'create'])->name('create');
        Route::post('/', [CourseController::class, 'store'])->name('store');
        Route::get('/{id}', [CourseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CourseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CourseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CourseController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [CourseController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{id}/data', [CourseController::class, 'getCourseData'])->name('data');
    });

    // ✅ Routes for Results
    Route::get('/results', [ResultsController::class, 'index'])->name('results.index');

    // ✅ Routes for Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportsController::class, 'store'])->name('reports.store');
    Route::put('/reports/{id}', [ReportsController::class, 'update'])->name('reports.update');
    Route::delete('/reports', [ReportsController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');

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