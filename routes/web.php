<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminListingController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/browse', BrowseController::class)->name('browse');

Route::get('/terms', fn () => view('pages.stub', ['title' => 'Terms and Conditions', 'body' => "We're still drafting TrustHire's terms of service. Check back soon."]))->name('terms');
Route::get('/privacy', fn () => view('pages.stub', ['title' => 'Privacy Policy', 'body' => "We're still drafting TrustHire's privacy policy. Check back soon."]))->name('privacy');
Route::get('/faq', fn () => view('pages.stub', ['title' => 'FAQs', 'body' => "We're putting together answers to common questions. Check back soon."]))->name('faq');
Route::get('/pricing', fn () => view('pages.stub', ['title' => 'Pricing', 'body' => 'TrustHire is free to use during early access. No listing fees or commission yet.']))->name('pricing');
Route::get('/help', fn () => view('pages.stub', ['title' => 'Help Center', 'body' => "We're building out a full help center. In the meantime, reach us via WhatsApp or email in the footer."]))->name('help');
Route::get('/disputes', fn () => view('pages.stub', ['title' => 'Dispute Resolution', 'body' => 'Disputes between renters and lenders are currently handled manually by our team — get in touch via WhatsApp or email.']))->name('disputes');
Route::get('/trust-safety', fn () => view('pages.trust-safety'))->name('trust-safety');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send')->middleware('guest');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify')->middleware('guest');
Route::post('/register', [AuthController::class, 'registerEmail'])->name('register')->middleware('guest');
Route::post('/login/email', [AuthController::class, 'loginEmail'])->name('login.email')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'update'])->name('onboarding.update');

    Route::get('/verify', [VerifyController::class, 'show'])->name('verify.show');
    Route::post('/verify', [VerifyController::class, 'submit'])->name('verify.submit');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/listings/new', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');

    Route::get('/requests/new', [ItemRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [ItemRequestController::class, 'store'])->name('requests.store');

    Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');
    Route::post('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/messages', [BookingController::class, 'sendMessage'])->name('bookings.messages.store');
    Route::post('/bookings/{booking}/payment-method', [BookingController::class, 'setPaymentMethod'])->name('bookings.payment-method');
    Route::post('/bookings/{booking}/mark-paid', [BookingController::class, 'markPaid'])->name('bookings.mark-paid');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/review', [BookingController::class, 'submitReview'])->name('bookings.review');

    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
        Route::get('/listings/{listing}/edit', [AdminListingController::class, 'edit'])->name('listings.edit');
        Route::put('/listings/{listing}', [AdminListingController::class, 'update'])->name('listings.update');
        Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('listings.destroy');

        Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

        Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
        Route::post('/verifications/{user}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{user}/reject', [AdminVerificationController::class, 'reject'])->name('verifications.reject');

        Route::get('/reports', [ReportAdminController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/resolve', [ReportAdminController::class, 'resolve'])->name('reports.resolve');

        Route::get('/content', [AdminContentController::class, 'show'])->name('content.show');
        Route::post('/content', [AdminContentController::class, 'update'])->name('content.update');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{category}/delete', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    });
});

Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/requests/{itemRequest}', [ItemRequestController::class, 'show'])->name('requests.show');
