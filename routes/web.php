<?php

// --- CONTROLLER IMPORTS (All controllers) ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\WorkVisaApplicationPageController as ProfileWorkVisaPageController;
// *** ADDED: Profile Student Visa Controller ***
use App\Http\Controllers\Profile\StudentVisaApplicationPageController as ProfileStudentVisaPageController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController;
use App\Http\Controllers\Admin\JobPostingPageController;
use App\Http\Controllers\Admin\AirlinePageController;
use App\Http\Controllers\Admin\AirportPageController;
use App\Http\Controllers\Admin\FlightPageController;
use App\Http\Controllers\Admin\UserPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TouristVisaPageController;
use App\Http\Controllers\Admin\WorkVisaApplicationPageController as AdminWorkVisaPageController;
// *** ADDED: Admin Student Visa Controller ***
use App\Http\Controllers\Admin\StudentVisaApplicationPageController as AdminStudentVisaPageController;
use App\Http\Controllers\PaymentController;

// --- FACADES & MODELS ---
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Country;
use Illuminate\Http\Request;

// --- PUBLIC PAGES ---
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/find-universities', [PublicPageController::class, 'universities'])->name('public.universities');
Route::get('/university/{university}', [PublicPageController::class, 'universityDetail'])->name('public.universities.show');
Route::get('/find-courses', [PublicPageController::class, 'courses'])->name('public.courses');
Route::get('/course/{course}', [PublicPageController::class, 'courseDetail'])->name('public.courses.show');
Route::get('/find-jobs', [PublicPageController::class, 'jobs'])->name('public.jobs');
Route::get('/job/{jobPosting}', [PublicPageController::class, 'jobDetail'])->name('public.job.detail');
Route::get('/travel-insurance', function () { /* ... */ })->name('public.travel-insurance.index');
Route::get('/travel-insurance/purchase', function (Request $request) { /* ... */ })->middleware(['auth', 'verified'])->name('public.travel-insurance.purchase');

// --- PAYMENT GATEWAY BACKEND CALLBACK ROUTES (SSLCOMMERZ) ---
Route::post('/sslcommerz/success', [PaymentController::class, 'paymentSuccess'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail', [PaymentController::class, 'paymentFail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel', [PaymentController::class, 'paymentCancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [PaymentController::class, 'paymentIpn'])->name('sslcommerz.ipn');

// --- PAYMENT GATEWAY FRONTEND REDIRECT ROUTES ---
Route::get('/payment/success', fn (Request $request) => Inertia::render('Public/PaymentStatus/Success', ['query' => $request->query()]))->name('payment.frontend.success');
Route::get('/payment/fail', fn (Request $request) => Inertia::render('Public/PaymentStatus/Fail', ['query' => $request->query()]))->name('payment.frontend.fail');
Route::get('/payment/cancel', fn (Request $request) => Inertia::render('Public/PaymentStatus/Cancel', ['query' => $request->query()]))->name('payment.frontend.cancel');


// --- USER DASHBOARD & PROFILE ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    // Work Visa Application Routes for Profile
    Route::prefix('work-visa')->name('profile.work-visa.')->group(function () {
        Route::get('/', [ProfileWorkVisaPageController::class, 'index'])->name('index');
        Route::get('/apply', [ProfileWorkVisaPageController::class, 'create'])->name('create');
        Route::get('/{workVisaApplication}', [ProfileWorkVisaPageController::class, 'show'])->name('show');
    });

    // *** ADDED: Student Visa Application Routes for Profile ***
    Route::prefix('student-visa')->name('profile.student-visa.')->group(function () {
        Route::get('/', [ProfileStudentVisaPageController::class, 'index'])->name('index');
        Route::get('/apply', [ProfileStudentVisaPageController::class, 'create'])->name('create');
        Route::get('/{studentVisaApplication}', [ProfileStudentVisaPageController::class, 'show'])->name('show');
    });
    // *** END Student Visa Routes ***
});

// --- ADMIN PANEL ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => Redirect::route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource Controllers
        Route::resource('universities', UniversityPageController::class)->except(['show']);
        Route::resource('courses', CoursePageController::class)->except(['show']);
        Route::resource('job-categories', JobCategoryPageController::class)->except(['show']);
        Route::resource('job-postings', JobPostingPageController::class)->except(['show']);
        Route::resource('airlines', AirlinePageController::class)->except(['show']);
        Route::resource('airports', AirportPageController::class)->except(['show']);
        Route::resource('flights', FlightPageController::class)->except(['show']);
        Route::resource('tourist-visas', TouristVisaPageController::class)->only(['index', 'show']);
        Route::resource('users', UserPageController::class)->only(['index', 'create', 'edit']);
        Route::resource('work-visa-applications', AdminWorkVisaPageController::class)->only(['index', 'show']);

        // *** ADDED: Student Visa Application Routes for Admin ***
        Route::resource('student-visa-applications', AdminStudentVisaPageController::class)->only(['index', 'show']);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Inside Route::middleware(['auth', 'verified'])->group(...)
        Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');
    });

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';
