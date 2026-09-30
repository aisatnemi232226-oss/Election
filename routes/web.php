<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Auth\{LoginController, RegisterController, ForgotPasswordController, ResetPasswordController};
use App\Http\Controllers\Auth\RecoveryQrController;
use App\Http\Controllers\{DashboardController, ElectionsController, CandidateController, PositionController, VoterController, StudentNotificationController,
    UserController, AdminController, RoleController, ViewElectionController, VerifiedVoterController, ElectionArticleController, 
    ArticleController, SearchController, ResultsController, ReportsController, AdminRoleController, ProfileSettingsController, 
    AuditLogController, AcademicYearController, CourseController, ElectionPartyController, ElectionEventSettingsController, VoteController, RegisterElectionController, CandidateRegistrationController, CandidateApprovalController, NotificationController};
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\TicketController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

// Public image proxy route: streams files from storage/app/public when the public symlink is not serving correctly
Route::get('/img/storage/{folder}/{filename}', [\App\Http\Controllers\ImageController::class, 'storage'])
    ->where('filename', '.*')
    ->name('storage.image');

// ======================= 
// 🔐 Authentication Routes
// ======================= 
Route::controller(LoginController::class)->group(function (): void {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/login/qr', 'loginWithQr')->middleware('throttle:10,1')->name('login.qr');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'createAccount')->name('register.store');
});

Route::view('/register/options', 'voter.registration-options')->name('register.options');

Route::middleware('auth')->controller(RegisterController::class)->group(function () {
    Route::get('/register/verify', 'showVoterVerificationForm')->name('register.verify');
    Route::post('/register/verify', 'submitVoterVerification')->name('register.verify.store');
});

Route::middleware('auth')->controller(StudentNotificationController::class)->prefix('student/notifications')->name('student.notifications.')->group(function (): void {
    Route::get('/', 'index')->name('index');
    Route::post('/read', 'markRead')->name('read');
});

Route::controller(CandidateRegistrationController::class)->group(function () {
    Route::get('/candidate/register', 'show')->name('candidate.register');
    Route::post('/candidate/register', 'store')->name('candidate.register.store');
});

// ============================
// 🔑 Password Reset Routes
// ============================
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
    Route::get('/forgot-password/qr', 'showRecoveryQrForm')->name('password.recovery-qr');
    Route::post('/forgot-password/qr', 'verifyRecoveryQr')->middleware('throttle:10,1')->name('password.recovery-qr.verify');
    Route::get('/forgot-password/verify', 'showOtpForm')->name('password.otp.form');
    Route::post('/forgot-password/verify', 'verifyOtp')->middleware('throttle:10,1')->name('password.otp.verify');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('/reset-password', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->controller(RecoveryQrController::class)->group(function () {
    Route::get('/account/qr-codes', 'show')->name('account.qr-codes');
    Route::get('/account/recovery-qr', 'show')->name('account.recovery-qr');
    Route::post('/account/recovery-qr', 'generate')->middleware('throttle:5,1')->name('account.recovery-qr.generate');
    Route::post('/account/login-qr', 'generateLogin')->middleware('throttle:5,1')->name('account.login-qr.generate');
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

    Route::get('/election-events-settings', [ElectionEventSettingsController::class, 'edit'])->name('election-events-settings.edit');
    Route::put('/election-events-settings', [ElectionEventSettingsController::class, 'update'])->name('election-events-settings.update');

    // ✅ Routes for Search Box
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::post('/search', [SearchController::class, 'search']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // ✅ Routes for Elections
    Route::resource('elections', ElectionsController::class)->except(['show']);
    Route::get('/elections/content', [ElectionsController::class, 'content'])->name('elections.content');
    Route::post('/elections/{id}/restore', [ElectionsController::class, 'restore'])->name('elections.restore');
    Route::delete('/elections/{id}/force', [ElectionsController::class, 'forceDestroy'])->name('elections.forceDestroy');

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
        // Serve article images through controller to avoid symlink access issues
        Route::get('/image/article/{filename}', [ElectionArticleController::class, 'serveArticleImage'])->name('image.article');
        // Generic storage image serving (folders limited by controller for safety)
        Route::get('/image/storage/{folder}/{filename}', [ElectionArticleController::class, 'serveStorageImage'])->name('image.storage');
    });

    // ✅ Routes for Candidates
    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('/', [CandidateController::class, 'index'])->name('index');
        Route::get('/create', [CandidateController::class, 'create'])->name('create');
        Route::post('/', [CandidateController::class, 'store'])->name('store');
        Route::post('/{id}/approve', [CandidateApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [CandidateApprovalController::class, 'reject'])->name('reject');
        Route::post('/{id}/restore', [CandidateController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [CandidateController::class, 'forceDestroy'])->name('forceDestroy');
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
        Route::post('/{id}/restore', [ElectionPartyController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [ElectionPartyController::class, 'forceDestroy'])->name('forceDestroy');
        Route::post('/bulk-delete', [ElectionPartyController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{id}/data', [ElectionPartyController::class, 'getPartyData'])->name('data');
    });

    // Replace this line:
// Route::resource('positions', PositionController::class)->only(['index', 'create', 'store', 'destroy', 'edit', 'update']);

// With these individual routes:
Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
Route::delete('/positions/{id}', [PositionController::class, 'destroy'])->name('positions.destroy');
Route::post('/positions/{id}/restore', [PositionController::class, 'restore'])->name('positions.restore');
Route::delete('/positions/{id}/force', [PositionController::class, 'forceDestroy'])->name('positions.forceDestroy');
Route::get('/positions/paginatePositions', [PositionController::class, 'paginatePositions'])->name('positions.paginate');

    // ✅ Other Admin Resources
    Route::resource('voters', VoterController::class);
    Route::post('voters/import', [VoterController::class, 'import'])->name('voters.import');
    Route::get('voters/sample', [VoterController::class, 'downloadSample'])->name('voters.sample');
    Route::post('voters/{id}/votes/reset-all', [VoterController::class, 'resetVotesAll'])->name('voters.votes.resetAll');
    Route::post('voters/{id}/votes/reset-position', [VoterController::class, 'resetVotesPosition'])->name('voters.votes.resetPosition');
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
        Route::post('/{id}/restore', [CourseController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [CourseController::class, 'forceDestroy'])->name('forceDestroy');
        Route::post('/bulk-delete', [CourseController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{id}/data', [CourseController::class, 'getCourseData'])->name('data');
    });

    // ✅ Routes for Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportsController::class, 'store'])->name('reports.store');
    Route::put('/reports/{id}', [ReportsController::class, 'update'])->name('reports.update');
    Route::delete('/reports', [ReportsController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');

    // ✅ Routes for Results
    Route::get('/results', [ResultsController::class, 'index'])->name('results.index');

    // ✅ Routes for Verified-Voters
    Route::get('/verified-voters', [VerifiedVoterController::class, 'index'])->name('verified-voters.index');
    Route::post('/verified-voters', [VerifiedVoterController::class, 'store'])->name('verified-voters.store');
    Route::post('/verified-voters/{user}/reject', [VerifiedVoterController::class, 'rejectVerification'])->name('verified-voters.reject');
    Route::post('/verified-voters/import', [VerifiedVoterController::class, 'import'])->name('verified-voters.import');
    Route::get('/verified-voters/export', [VerifiedVoterController::class, 'export'])->name('verified-voters.export');
    Route::get('/verified-voters/search', [VerifiedVoterController::class, 'search'])->name('verified-voters.search');
    Route::delete('/verified-voters/{id}', [VerifiedVoterController::class, 'destroy'])->name('verified-voters.destroy');
    Route::get('/verified-voters/sample', [VerifiedVoterController::class, 'downloadSample'])->name('verified-voters.sample');

    // ✅ Routes for Chatbot CMS
    Route::get('/chatbot', [\App\Http\Controllers\AdminChatbotController::class, 'index'])->name('chatbot.index');
    Route::put('/chatbot/settings', [\App\Http\Controllers\AdminChatbotController::class, 'updateSettings'])->name('chatbot.settings.update');
    Route::post('/chatbot/intents', [\App\Http\Controllers\AdminChatbotController::class, 'storeIntent'])->name('chatbot.intents.store');
    Route::put('/chatbot/intents/{id}', [\App\Http\Controllers\AdminChatbotController::class, 'updateIntent'])->name('chatbot.intents.update');
    Route::delete('/chatbot/intents/{id}', [\App\Http\Controllers\AdminChatbotController::class, 'destroyIntent'])->name('chatbot.intents.destroy');

    // ✅ Routes for Tickets
    Route::get('/tickets', [\App\Http\Controllers\AdminTicketController::class, 'index'])->name('tickets.index');
    Route::put('/tickets/{id}', [\App\Http\Controllers\AdminTicketController::class, 'update'])->name('tickets.update');
});

// =======================
// 🎫 User Tickets (Authenticated)
// =======================
Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
});

// =======================
// 🌍 Public Voter Pages
// =======================
Route::get('/', function () {
    $elections = \App\Models\Election::query()
        ->orderByDesc('created_at')
        ->get();

    $candidates = \App\Models\Candidate::query()
        ->with(['course', 'party', 'position'])
        ->where('status', 'Active')
        ->orderByDesc('created_at')
        ->get();

    $eventSettings = null;
    if (Schema::hasTable('election_event_settings')) {
        $eventSettings = \App\Models\ElectionEventSetting::query()->first();
    }

    return view('voter.home', compact('elections', 'candidates', 'eventSettings'));
})->name('home');

Route::post('/chatbot', [ChatbotController::class, 'respond'])->name('chatbot.respond');

Route::get('/election-details', function () {
    $elections = \App\Models\Election::query()
        ->orderByDesc('created_at')
        ->get();

    $positionOrder = [
        'President',
        'Presidential',
        'Vice President',
        'Vice-President',
        'Vice',
        'Secretary',
        'Treasurer',
        'Auditor',
        'Sgt. Arms',
        'Sergeant at Arms',
        'B-O-D',
        'BOD',
    ];

    $positions = \App\Models\Position::query()
        ->whereIn('id', function ($q) {
            $q->select('position_id')
                ->from('candidate')
                ->where('status', 'Active')
                ->whereNotNull('position_id')
                ->groupBy('position_id');
        })
        ->orderByRaw(
            'CASE ' . implode(' ', array_map(fn ($i) => 'WHEN name = ? THEN ' . ($i + 1), array_keys($positionOrder))) . ' ELSE 999 END',
            $positionOrder
        )
        ->orderBy('name')
        ->get();

    $selectedPositionId = request()->query('position');
    $selectedPartyId = request()->query('party');
    $selectedCourseId = request()->query('course');
    $searchQuery = request()->query('q');

    $candidatesQuery = \App\Models\Candidate::query()
        ->with(['course', 'party', 'position'])
        ->where('status', 'Active')
        ->when($selectedPositionId, function ($q) use ($selectedPositionId) {
            $q->where('position_id', $selectedPositionId);
        })
        ->when($selectedPartyId, function ($q) use ($selectedPartyId) {
            $q->where('electionparty_id', $selectedPartyId);
        })
        ->when($selectedCourseId, function ($q) use ($selectedCourseId) {
            $q->where('course_id', $selectedCourseId);
        })
        ->when($searchQuery, function ($q) use ($searchQuery) {
            $q->where('name', 'like', '%' . $searchQuery . '%');
        })
        ->orderByDesc('created_at');

    $candidates = $candidatesQuery->get();

    $parties = \App\Models\ElectionParty::query()
        ->whereIn('id', function ($q) {
            $q->select('electionparty_id')
                ->from('candidate')
                ->where('status', 'Active')
                ->whereNotNull('electionparty_id')
                ->groupBy('electionparty_id');
        })
        ->orderBy('name')
        ->get();

    $courses = \App\Models\Course::query()
        ->whereIn('id', function ($q) {
            $q->select('course_id')
                ->from('candidate')
                ->where('status', 'Active')
                ->whereNotNull('course_id')
                ->groupBy('course_id');
        })
        ->orderBy('name')
        ->get();

    $userVotesByPosition = [];
    if (auth()->check() && Schema::hasTable('position_votes')) {
        $userVotesByPosition = \App\Models\PositionVote::query()
            ->where('user_id', auth()->id())
            ->pluck('candidate_id', 'position_id')
            ->toArray();
    }

    return view('voter.election-details', compact(
        'elections',
        'candidates',
        'positions',
        'parties',
        'courses',
        'selectedPositionId',
        'selectedPartyId',
        'selectedCourseId',
        'searchQuery',
        'userVotesByPosition'
    ));
})->name('election.details');

Route::post('/vote/cast', [VoteController::class, 'cast'])
    ->middleware('auth')
    ->name('vote.cast');

Route::get('/candidate/{candidate}', function (\App\Models\Candidate $candidate) {
    $candidate->load(['course', 'party', 'position']);

    if (($candidate->status ?? null) !== 'Active') {
        abort(404);
    }

    return view('voter.candidate-profile', compact('candidate'));
})->name('candidate.profile');

Route::view('/election', 'voter.election')->name('election');
Route::get('/registerElection', [RegisterElectionController::class, 'show'])->name('registerElection');
Route::post('/registerElection', [RegisterElectionController::class, 'store'])->name('registerElection.store');

Route::get('/party/{party}/roster', function (\App\Models\ElectionParty $party) {
    $positionOrder = [
        'President',
        'Presidential',
        'Vice President',
        'Vice-President',
        'Vice',
        'Secretary',
        'Treasurer',
        'Auditor',
        'Sgt. Arms',
        'Sergeant at Arms',
        'B-O-D',
        'BOD',
    ];

    $candidates = \App\Models\Candidate::query()
        ->with(['course', 'position'])
        ->where('status', 'Active')
        ->where('electionparty_id', $party->id)
        ->get();

    $positions = $candidates
        ->pluck('position')
        ->filter()
        ->unique('id')
        ->values();

    $positions = $positions->sort(function ($a, $b) use ($positionOrder) {
        $aName = $a->name ?? '';
        $bName = $b->name ?? '';

        $aIndex = array_search($aName, $positionOrder, true);
        $bIndex = array_search($bName, $positionOrder, true);

        $aRank = ($aIndex === false) ? 999 : ($aIndex + 1);
        $bRank = ($bIndex === false) ? 999 : ($bIndex + 1);

        if ($aRank !== $bRank) {
            return $aRank <=> $bRank;
        }

        return strcasecmp($aName, $bName);
    })->values();

    $candidatesByPositionId = $candidates->groupBy(function ($c) {
        return $c->position_id ?: 0;
    });

    return view('voter.party-roster', compact('party', 'positions', 'candidatesByPositionId'));
})->name('party.roster');

Route::get('/vote', fn () => redirect()->route('election.details'))->name('vote');
Route::get('/result', function () {
    $positionOrder = [
        'President',
        'Presidential',
        'Vice President',
        'Vice-President',
        'Vice',
        'Secretary',
        'Treasurer',
        'Auditor',
        'Sgt. Arms',
        'Sergeant at Arms',
        'B-O-D',
        'BOD',
    ];

    $positions = \App\Models\Position::query()
        ->orderByRaw(
            'CASE ' . implode(' ', array_map(fn ($i) => 'WHEN name = ? THEN ' . ($i + 1), array_keys($positionOrder))) . ' ELSE 999 END',
            $positionOrder
        )
        ->orderBy('name')
        ->get();

    $pollCards = [
        'president' => null,
        'vice_president' => null,
        'overall' => null,
    ];

    if (
        \Illuminate\Support\Facades\Schema::hasTable('position_votes')
        && \Illuminate\Support\Facades\Schema::hasTable('candidate')
        && \Illuminate\Support\Facades\Schema::hasTable('positions')
    ) {
        $votesByCandidateId = \Illuminate\Support\Facades\DB::table('position_votes')
            ->select('candidate_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as votes'))
            ->groupBy('candidate_id');

        $baseCandidates = \Illuminate\Support\Facades\DB::table('candidate')
            ->joinSub($votesByCandidateId, 'vc', function ($join) {
                $join->on('candidate.id', '=', 'vc.candidate_id');
            })
            ->leftJoin('positions', 'positions.id', '=', 'candidate.position_id')
            ->select(
                'candidate.id',
                'candidate.name',
                'candidate.profile_img',
                'candidate.banner_img',
                'candidate.position_id',
                'positions.name as position_name',
                'vc.votes'
            );

        $pollCards['overall'] = $baseCandidates
            ->orderByDesc('vc.votes')
            ->orderBy('candidate.name')
            ->first();

        $pollCards['president'] = (clone $baseCandidates)
            ->whereIn('positions.name', ['President', 'Presidential'])
            ->orderByDesc('vc.votes')
            ->orderBy('candidate.name')
            ->first();

        $pollCards['vice_president'] = (clone $baseCandidates)
            ->whereIn('positions.name', ['Vice President', 'Vice-President', 'Vice'])
            ->orderByDesc('vc.votes')
            ->orderBy('candidate.name')
            ->first();

        $makeCandidateImageUrl = function ($candidate) {
            if (!$candidate) return null;

            $default = asset('images/default-banner.png');

            $bannerRelative = !empty($candidate->banner_img)
                ? (\Illuminate\Support\Str::contains($candidate->banner_img, '/') ? $candidate->banner_img : ('banner/' . $candidate->banner_img))
                : null;
            $profileRelative = !empty($candidate->profile_img)
                ? (\Illuminate\Support\Str::contains($candidate->profile_img, '/') ? $candidate->profile_img : ('profile-picture/' . $candidate->profile_img))
                : null;

            $bannerExists = $bannerRelative
                ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative)
                : false;
            if ($bannerExists) {
                return route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative]);
            }

            $profileExists = $profileRelative
                ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
                : false;
            if ($profileExists) {
                return route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative]);
            }

            return $default;
        };

        foreach (['overall', 'president', 'vice_president'] as $key) {
            if ($pollCards[$key]) {
                $pollCards[$key]->image_url = $makeCandidateImageUrl($pollCards[$key]);
            }
        }
    }

    return view('voter.result', compact('positions', 'pollCards'));
})->name('result');
Route::get('/resultTally', function (\Illuminate\Http\Request $request) {
    $positionOrder = [
        'President',
        'Presidential',
        'Vice President',
        'Vice-President',
        'Vice',
        'Secretary',
        'Treasurer',
        'Auditor',
        'Sgt. Arms',
        'Sergeant at Arms',
        'B-O-D',
        'BOD',
    ];

    $positions = \App\Models\Position::query()
        ->orderByRaw(
            'CASE ' . implode(' ', array_map(fn ($i) => 'WHEN name = ? THEN ' . ($i + 1), array_keys($positionOrder))) . ' ELSE 999 END',
            $positionOrder
        )
        ->orderBy('name')
        ->get();

    $selectedPositionId = $request->query('position');
    $selectedPosition = null;
    if ($selectedPositionId) {
        $selectedPosition = $positions->firstWhere('id', (int) $selectedPositionId);
        if (!$selectedPosition) {
            $selectedPosition = \App\Models\Position::query()->where('id', $selectedPositionId)->first();
        }
    }

    $votesByCandidateId = [];
    $votesTotalByPositionId = [];

    if (\Illuminate\Support\Facades\Schema::hasTable('position_votes')) {
        $votesByCandidateId = \Illuminate\Support\Facades\DB::table('position_votes')
            ->select('candidate_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as votes_count'))
            ->groupBy('candidate_id')
            ->pluck('votes_count', 'candidate_id')
            ->toArray();

        $votesTotalByPositionId = \Illuminate\Support\Facades\DB::table('position_votes')
            ->select('position_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as votes_total'))
            ->groupBy('position_id')
            ->pluck('votes_total', 'position_id')
            ->toArray();
    }

    $candidatesQuery = \App\Models\Candidate::query()
        ->with(['course', 'party', 'position'])
        ->where('status', 'Active')
        ->when($selectedPositionId, function ($q) use ($selectedPositionId) {
            $q->where('position_id', $selectedPositionId);
        })
        ->orderBy('name');

    $candidates = $candidatesQuery->get()->map(function ($c) use ($votesByCandidateId) {
        $c->votes_count = (int) ($votesByCandidateId[$c->id] ?? 0);
        return $c;
    })->sortByDesc(function ($c) {
        return (int) ($c->votes_count ?? 0);
    })->values();

    $totalVotes = 0;
    if ($selectedPositionId) {
        $totalVotes = (int) ($votesTotalByPositionId[(int) $selectedPositionId] ?? 0);
    } elseif (!empty($votesTotalByPositionId)) {
        $totalVotes = array_sum($votesTotalByPositionId);
    }

    $candidatesByPositionId = $candidates->groupBy(function ($c) {
        return $c->position_id ?: 0;
    });

    $courseStats = collect();
    $yearLevelTurnout = collect();
    $genderStats = null;

    $partyTallyLabels = [];
    $partyTallyData = [];

    if (
        \Illuminate\Support\Facades\Schema::hasTable('position_votes')
        && \Illuminate\Support\Facades\Schema::hasTable('candidate')
        && \Illuminate\Support\Facades\Schema::hasTable('electionparty')
    ) {
        $partyVotesQuery = \Illuminate\Support\Facades\DB::table('position_votes')
            ->join('candidate', 'candidate.id', '=', 'position_votes.candidate_id')
            ->join('electionparty', 'electionparty.id', '=', 'candidate.electionparty_id')
            ->select('electionparty.name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as votes'))
            ->when($selectedPositionId, function ($q) use ($selectedPositionId) {
                $q->where('position_votes.position_id', $selectedPositionId);
            })
            ->groupBy('electionparty.name')
            ->orderByDesc('votes');

        $partyRows = $partyVotesQuery->get();
        $partyTallyLabels = $partyRows->pluck('name')->map(fn ($v) => (string) $v)->values()->toArray();
        $partyTallyData = $partyRows->pluck('votes')->map(fn ($v) => (int) $v)->values()->toArray();
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('verified_voters')) {
        $registeredByCourseId = \Illuminate\Support\Facades\DB::table('verified_voters')
            ->select('course_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as registered_count'))
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->pluck('registered_count', 'course_id');

        $enrolledByCourseId = \Illuminate\Support\Facades\DB::table('verified_voters')
            ->select('course_id', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT users_id) as enrolled_count'))
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->pluck('enrolled_count', 'course_id');

        $votedByCourseId = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('position_votes')) {
            $votedByCourseId = \Illuminate\Support\Facades\DB::table('position_votes')
                ->join('verified_voters', 'verified_voters.users_id', '=', 'position_votes.user_id')
                ->whereNotNull('verified_voters.course_id')
                ->select('verified_voters.course_id', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT position_votes.user_id) as voted_count'))
                ->groupBy('verified_voters.course_id')
                ->pluck('voted_count', 'verified_voters.course_id');
        }

        $courseNamesById = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('courses')) {
            $courseNamesById = \Illuminate\Support\Facades\DB::table('courses')
                ->pluck('name', 'id');
        }

        $courseIds = $registeredByCourseId->keys();
        $courseStats = $courseIds->map(function ($courseId) use ($registeredByCourseId, $enrolledByCourseId, $votedByCourseId, $courseNamesById) {
            return [
                'course_id' => (int) $courseId,
                'course_name' => (string) ($courseNamesById[$courseId] ?? ('Course #' . $courseId)),
                'enrolled' => (int) ($enrolledByCourseId[$courseId] ?? 0),
                'registered' => (int) ($registeredByCourseId[$courseId] ?? 0),
                'voted' => (int) ($votedByCourseId[$courseId] ?? 0),
            ];
        })->sortByDesc('registered')->values();

        if (\Illuminate\Support\Facades\Schema::hasTable('yearlevel')) {
            $registeredByYearId = \Illuminate\Support\Facades\DB::table('verified_voters')
                ->select('yrlvl_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as registered_count'))
                ->whereNotNull('yrlvl_id')
                ->groupBy('yrlvl_id')
                ->pluck('registered_count', 'yrlvl_id');

            $votedByYearId = collect();
            if (\Illuminate\Support\Facades\Schema::hasTable('position_votes')) {
                $votedByYearId = \Illuminate\Support\Facades\DB::table('position_votes')
                    ->join('verified_voters', 'verified_voters.users_id', '=', 'position_votes.user_id')
                    ->whereNotNull('verified_voters.yrlvl_id')
                    ->select('verified_voters.yrlvl_id', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT position_votes.user_id) as voted_count'))
                    ->groupBy('verified_voters.yrlvl_id')
                    ->pluck('voted_count', 'verified_voters.yrlvl_id');
            }

            $yearNamesById = \Illuminate\Support\Facades\DB::table('yearlevel')->pluck('name', 'id');
            $yearIds = $registeredByYearId->keys();
            $yearLevelTurnout = $yearIds->map(function ($yearId) use ($registeredByYearId, $votedByYearId, $yearNamesById) {
                $registered = (int) ($registeredByYearId[$yearId] ?? 0);
                $voted = (int) ($votedByYearId[$yearId] ?? 0);
                $pct = $registered > 0 ? round(($voted / $registered) * 100, 2) : 0;
                return [
                    'year_id' => (int) $yearId,
                    'year_name' => (string) ($yearNamesById[$yearId] ?? ('Year #' . $yearId)),
                    'registered' => $registered,
                    'voted' => $voted,
                    'pct' => $pct,
                ];
            })->sortByDesc('registered')->values();
        }
    }

    return view('voter.resultTally', compact(
        'positions',
        'selectedPositionId',
        'selectedPosition',
        'candidatesByPositionId',
        'totalVotes',
        'votesTotalByPositionId',
        'courseStats',
        'yearLevelTurnout',
        'genderStats',
        'partyTallyLabels',
        'partyTallyData'
    ));
})->name('resultTally');
Route::view('/contact', 'voter.contact')->name('contact');

Route::get('/article/{id}/json', [ArticleController::class, 'publicJson'])->name('article.json');