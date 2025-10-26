<?php

// --- CONTROLLER IMPORTS (All controllers) ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController;
use App\Http\Controllers\Admin\JobPostingPageController;
use App\Http\Controllers\Admin\AirlinePageController;
use App\Http\Controllers\Admin\AirportPageController;
use App\Http\Controllers\Admin\FlightPageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TouristVisaPageController;
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

// Travel Insurance Public Index Page
Route::get('/travel-insurance', function () {
    $countries = Country::orderBy('name')->select('id', 'name', 'code')->get();
    return Inertia::render('Public/TravelInsurance/Index', [
        'countries' => $countries
    ]);
})->name('public.travel-insurance.index');

// Travel Insurance Purchase Form Route (Requires Auth)
Route::get('/travel-insurance/purchase', function (Request $request) {
    // Basic validation of query parameters passed from Index page
    $validatedData = $request->validate([
        'quote_reference' => 'required|string',
        'package_id' => 'required',
        'package_name' => 'required|string',
        'destination_country_id' => 'required|exists:countries,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'num_travelers' => 'required|integer|min:1',
        'total_premium' => 'required|numeric',
        'currency' => 'required|string',
    ]);

    $countries = Country::orderBy('name')->select('id', 'name', 'code')->get();

    // Pass validated data and countries to the Vue component
    return Inertia::render('Public/TravelInsurance/PurchaseForm', array_merge($validatedData, [
        'countries' => $countries,
    ]));
})->middleware(['auth', 'verified'])->name('public.travel-insurance.purchase');


// --- PAYMENT GATEWAY BACKEND CALLBACK ROUTES (SSLCOMMERZ) ---
Route::prefix('payment')->name('payment.')->group(function() {
    // Example Routes (May remove later)
    Route::get('/example1', [PaymentController::class, 'exampleEasyCheckout'])->name('example1');
    Route::get('/example2', [PaymentController::class, 'exampleHostedCheckout'])->name('example2');

    // Core Payment Initiation
    Route::post('/pay', [PaymentController::class, 'index'])->name('pay');
    Route::post('/pay-via-ajax', [PaymentController::class, 'payViaAjax'])->name('pay-ajax');

    // Callbacks from SSLCommerz (POST requests from their server)
    Route::post('/success', [PaymentController::class, 'success'])->name('success');
    Route::post('/fail', [PaymentController::class, 'fail'])->name('fail');
    Route::post('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('/ipn', [PaymentController::class, 'ipn'])->name('ipn'); // Instant Payment Notification
});
// --- END PAYMENT GATEWAY BACKEND ROUTES ---

// *** NEW: PAYMENT GATEWAY FRONTEND REDIRECT ROUTES (MERGED) ***
// These handle the GET requests when the user is redirected back from SSLCommerz
Route::get('/payment/success', fn (Request $request) => Inertia::render('Public/PaymentStatus/Success', ['query' => $request->query()]))->name('payment.frontend.success');
Route::get('/payment/fail', fn (Request $request) => Inertia::render('Public/PaymentStatus/Fail', ['query' => $request->query()]))->name('payment.frontend.fail');
Route::get('/payment/cancel', fn (Request $request) => Inertia::render('Public/PaymentStatus/Cancel', ['query' => $request->query()]))->name('payment.frontend.cancel');
// *** END FRONTEND REDIRECT ROUTES ***


// --- USER DASHBOARD & PROFILE ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');
});

// --- ADMIN PANEL ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => Redirect::route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource controllers
        Route::resource('universities', UniversityPageController::class)->except(['show']);
        Route::resource('courses', CoursePageController::class)->except(['show']);
        Route::resource('job-categories', JobCategoryPageController::class)->except(['show']);
        Route::resource('job-postings', JobPostingPageController::class)->except(['show']);
        Route::resource('airlines', AirlinePageController::class)->except(['show']);
        Route::resource('airports', AirportPageController::class)->except(['show']);
        Route::resource('flights', FlightPageController::class)->except(['show']);
        Route::resource('tourist-visas', TouristVisaPageController::class)->only(['index', 'show']);
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';