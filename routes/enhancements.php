<?php

// ============================================
// NEW PLATFORM ENHANCEMENTS ROUTES
// Added: November 27, 2025
// ============================================

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\DirectoryController as AdminDirectoryController;
use App\Http\Controllers\User\AppointmentController as UserAppointmentController;
use App\Http\Controllers\User\SupportTicketController as UserSupportTicketController;
use App\Http\Controllers\PublicDirectoryController;

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Events (Public)
Route::get('/events', [EventController::class, 'publicIndex'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'publicShow'])->name('events.show');

// FAQ (Public)
Route::get('/faq', [FaqController::class, 'publicIndex'])->name('faq.index');
Route::get('/faq/{category}', [FaqController::class, 'publicCategory'])->name('faq.category');
Route::post('/faq/{faq}/helpful', [FaqController::class, 'markHelpful'])->name('faq.helpful');

// Dynamic Pages (Public)
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::get('/terms', [PageController::class, 'terms'])->name('page.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('page.privacy');
Route::get('/refund-policy', [PageController::class, 'refund'])->name('page.refund');

// Public Directory
Route::prefix('directory')->name('directory.')->group(function () {
    Route::get('/', [PublicDirectoryController::class, 'index'])->name('index');
    Route::get('/search', [PublicDirectoryController::class, 'search'])->name('search');
    Route::get('/{category}', [PublicDirectoryController::class, 'category'])->name('category');
    Route::get('/{category}/{slug}', [PublicDirectoryController::class, 'show'])->name('show');
});

// ============================================
// USER AUTHENTICATED ROUTES
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Appointments (User)
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [UserAppointmentController::class, 'index'])->name('index');
        Route::get('/create', [UserAppointmentController::class, 'create'])->name('create');
        Route::post('/', [UserAppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [UserAppointmentController::class, 'show'])->name('show');
        Route::post('/{appointment}/cancel', [UserAppointmentController::class, 'cancel'])->name('cancel');
        Route::get('/{appointment}/reschedule', [UserAppointmentController::class, 'reschedule'])->name('reschedule');
        Route::put('/{appointment}/reschedule', [UserAppointmentController::class, 'updateReschedule'])->name('update-reschedule');
    });
    
    // Support Tickets (User)
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [UserSupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [UserSupportTicketController::class, 'create'])->name('create');
        Route::post('/', [UserSupportTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [UserSupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [UserSupportTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/close', [UserSupportTicketController::class, 'close'])->name('close');
        Route::post('/{ticket}/rate', [UserSupportTicketController::class, 'rate'])->name('rate');
    });
    
    // Event Registration (User)
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
});

// ============================================
// ADMIN ROUTES
// ============================================

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Events Management
    Route::resource('events', EventController::class)->except(['show']);
    Route::post('events/{event}/toggle-featured', [EventController::class, 'toggleFeatured'])->name('events.toggle-featured');
    Route::post('events/{event}/toggle-published', [EventController::class, 'togglePublished'])->name('events.toggle-published');
    Route::get('events/{event}/registrations', [EventController::class, 'registrations'])->name('events.registrations');
    
    // Appointments Management
    Route::resource('appointments', AdminAppointmentController::class)->except(['create', 'store']);
    Route::post('appointments/{appointment}/confirm', [AdminAppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/complete', [AdminAppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('appointments/{appointment}/assign', [AdminAppointmentController::class, 'assign'])->name('appointments.assign');
    Route::get('appointments/calendar', [AdminAppointmentController::class, 'calendar'])->name('appointments.calendar');
    
    // Support Tickets Management
    Route::resource('support-tickets', AdminSupportTicketController::class)->except(['create', 'store', 'destroy']);
    Route::post('support-tickets/{ticket}/reply', [AdminSupportTicketController::class, 'reply'])->name('support-tickets.reply');
    Route::post('support-tickets/{ticket}/assign', [AdminSupportTicketController::class, 'assign'])->name('support-tickets.assign');
    Route::post('support-tickets/{ticket}/resolve', [AdminSupportTicketController::class, 'resolve'])->name('support-tickets.resolve');
    Route::post('support-tickets/{ticket}/close', [AdminSupportTicketController::class, 'close'])->name('support-tickets.close');
    Route::get('support-tickets/stats', [AdminSupportTicketController::class, 'stats'])->name('support-tickets.stats');
    
    // FAQ Categories
    Route::resource('faq-categories', FaqCategoryController::class)->except(['show']);
    Route::post('faq-categories/{category}/reorder', [FaqCategoryController::class, 'reorder'])->name('faq-categories.reorder');
    
    // FAQs
    Route::resource('faqs', FaqController::class)->except(['show']);
    Route::post('faqs/{faq}/toggle-featured', [FaqController::class, 'toggleFeatured'])->name('faqs.toggle-featured');
    Route::post('faqs/{faq}/toggle-published', [FaqController::class, 'togglePublished'])->name('faqs.toggle-published');
    Route::post('faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
    
    // CMS Pages
    Route::resource('pages', PageController::class);
    Route::post('pages/{page}/toggle-published', [PageController::class, 'togglePublished'])->name('pages.toggle-published');
    Route::post('pages/{page}/toggle-footer', [PageController::class, 'toggleFooter'])->name('pages.toggle-footer');
    
    // Partners
    Route::resource('partners', PartnerController::class)->except(['show']);
    Route::post('partners/{partner}/toggle-featured', [PartnerController::class, 'toggleFeatured'])->name('partners.toggle-featured');
    Route::post('partners/{partner}/toggle-active', [PartnerController::class, 'toggleActive'])->name('partners.toggle-active');
    Route::post('partners/reorder', [PartnerController::class, 'reorder'])->name('partners.reorder');
    
    // Directory Categories
    Route::resource('directory-categories', \App\Http\Controllers\Admin\DirectoryCategoryController::class)->except(['show']);
    
    // Directories
    Route::resource('directories', AdminDirectoryController::class);
    Route::post('directories/{directory}/toggle-featured', [AdminDirectoryController::class, 'toggleFeatured'])->name('directories.toggle-featured');
    Route::post('directories/{directory}/toggle-published', [AdminDirectoryController::class, 'togglePublished'])->name('directories.toggle-published');
    Route::post('directories/{directory}/verify', [AdminDirectoryController::class, 'verify'])->name('directories.verify');
});

// ============================================
// API ROUTES (Optional)
// ============================================

// Available time slots for appointments
Route::get('/api/appointments/available-slots', [UserAppointmentController::class, 'availableSlots'])
    ->middleware('auth')
    ->name('api.appointments.available-slots');

// FAQ Search API
Route::get('/api/faq/search', [FaqController::class, 'search'])->name('api.faq.search');

// Directory Search API
Route::get('/api/directory/search', [PublicDirectoryController::class, 'apiSearch'])->name('api.directory.search');
