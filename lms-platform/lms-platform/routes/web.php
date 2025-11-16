<?php

use App\Http\Controllers\admin\InfoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\AdminController;
use App\Http\Controllers\backend\AdminCourseController;
use App\Http\Controllers\backend\AdminInstructorController;
use App\Http\Controllers\backend\AdminProfileController;
use App\Http\Controllers\backend\BackendOrderController;
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\CouponController;
use App\Http\Controllers\backend\CourseController;
use App\Http\Controllers\backend\CourseSectionController;
use App\Http\Controllers\backend\InstructorController;
use App\Http\Controllers\backend\InstructorProfileController;
use App\Http\Controllers\backend\OrderController;
use App\Http\Controllers\backend\SettingController;
use App\Http\Controllers\backend\SliderController;
use App\Http\Controllers\backend\SubcategoryController;
use App\Http\Controllers\backend\UserController;
use App\Http\Controllers\backend\UserProfileController;
use App\Http\Controllers\frontend\CartController;
use App\Http\Controllers\frontend\CheckoutController;
use App\Http\Controllers\frontend\FrontendDashboardController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\InstructorApplicationController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\backend\AdminStripeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HealthController;

// Health check endpoints (no auth required)
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
Route::get('/health/simple', [HealthController::class, 'simple'])->name('health.simple');

Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

/*
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */

/*  Google Route  */

Route::get('/auth/google', [SocialController::class, 'googleLogin'])->name('auth.google');
Route::get('/auth/google-callback', [SocialController::class, 'googleAuthentication'])->name('auth.google-callback');



/* Admin Route   */

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');


Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'destroy'])
        ->name('logout');

    /*  control Profile */

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::post('/profile/store', [AdminProfileController::class, 'store'])->name('profile.store');
    Route::get('/setting', [AdminProfileController::class, 'setting'])->name('setting');
    Route::post('/password/setting', [AdminProfileController::class, 'passwordSetting'])->name('passwordSetting');

    /*  control Category & Subcategory  */

    Route::resource('category', CategoryController::class);
    Route::resource('subcategory', SubcategoryController::class);

    /* Control Slider */
    Route::resource('slider', SliderController::class);

     /* control Course  */

    Route::resource('course', AdminCourseController::class);
    Route::post('/course-status', [AdminCourseController::class, 'courseStatus'])->name('course.status');

    /*  order controller  */
    Route::resource('order', BackendOrderController::class);

    /* Mange Info */
    Route::resource('info', InfoController::class);

    /* control instructor  */
    Route::resource('instructor', AdminInstructorController::class);
    Route::post('/update-status', [AdminInstructorController::class, 'updateStatus'])->name('instructor.status');
    Route::get('/instructor-active-list', [AdminInstructorController::class, 'instructorActive'])->name('instructor.active');

    /*  Setting Controller */
    Route::get('/mail-setting', [SettingController::class, 'mailSetting'])->name('mailSetting');
    Route::post('/mail-settings/update', [SettingController::class, 'updateMailSettings'])->name('mail.settings.update');

    Route::get('/stripe-setting', [SettingController::class, 'stripeSetting'])->name('stripeSetting');
    Route::post('/stripe-settings/update', [SettingController::class, 'updateStripeSettings'])->name('stripe.settings.update');

    Route::get('/google-setting', [SettingController::class, 'googleSetting'])->name('googleSetting ');
    Route::post('/google-settings/update', [SettingController::class, 'updateGoogleSettings'])->name('google.settings.update');



});


/*  Instructor Route  */
Route::get('/instructor/login', [InstructorController::class, 'login'])->name('instructor.login');
Route::get('/instructor/register', [InstructorController::class, 'register'])->name('instructor.register');
Route::middleware(['auth', 'verified', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/notifications', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'notifications'])->name('dashboard.notifications');
    Route::get('/dashboard/calendar', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'calendar'])->name('dashboard.calendar');
    Route::get('/dashboard/messages', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'messages'])->name('dashboard.messages');

    // Course Management
    Route::get('/courses', [\App\Http\Controllers\backend\InstructorCourseController::class, 'index'])->name('courses');
    Route::get('/courses/create', [\App\Http\Controllers\backend\InstructorCourseController::class, 'create'])->name('courses.create');
    Route::get('/courses/drafts', [\App\Http\Controllers\backend\InstructorCourseController::class, 'drafts'])->name('courses.drafts');
    Route::get('/courses/published', [\App\Http\Controllers\backend\InstructorCourseController::class, 'published'])->name('courses.published');
    Route::get('/courses/pricing', [\App\Http\Controllers\backend\InstructorCourseController::class, 'pricing'])->name('courses.pricing');
    Route::get('/courses/preview', [\App\Http\Controllers\backend\InstructorCourseController::class, 'preview'])->name('courses.preview');
    Route::get('/courses/feedback', [\App\Http\Controllers\backend\InstructorCourseController::class, 'feedback'])->name('courses.feedback');

    // Student Management
    Route::get('/students', [\App\Http\Controllers\backend\InstructorStudentController::class, 'index'])->name('students');
    Route::get('/students/progress', [\App\Http\Controllers\backend\InstructorStudentController::class, 'progress'])->name('students.progress');
    Route::get('/students/certificates', [\App\Http\Controllers\backend\InstructorStudentController::class, 'certificates'])->name('students.certificates');

    // Live Session
    Route::get('/live/schedule', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'liveSchedule'])->name('live.schedule');

    // Analytics & Reports
    Route::get('/analytics/performance', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'analyticsPerformance'])->name('analytics.performance');
    Route::get('/analytics/earnings', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'analyticsEarnings'])->name('analytics.earnings');
    Route::get('/analytics/visits', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'analyticsVisits'])->name('analytics.visits');
    Route::get('/analytics/engagement', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'analyticsEngagement'])->name('analytics.engagement');
    
    // Export Routes
    Route::get('/analytics/earnings/export/excel', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'exportEarningsExcel'])->name('analytics.earnings.excel');
    Route::get('/analytics/visits/export/excel', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'exportVisitsExcel'])->name('analytics.visits.excel');
    Route::get('/analytics/earnings/export/pdf', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'exportEarningsPdf'])->name('analytics.earnings.pdf');
    
    // Email Report Routes
    Route::post('/analytics/email/send', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'sendEmailReport'])->name('analytics.email.send');
    Route::post('/analytics/email/schedule', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'scheduleEmailReports'])->name('analytics.email.schedule');
    
    // Email Management Routes
    Route::post('/email/welcome', [\App\Http\Controllers\EmailController::class, 'sendWelcomeEmail'])->name('email.welcome');
    Route::post('/email/course-enrollment', [\App\Http\Controllers\EmailController::class, 'sendCourseEnrollmentEmail'])->name('email.course-enrollment');
    Route::post('/email/bulk', [\App\Http\Controllers\EmailController::class, 'sendBulkEmail'])->name('email.bulk');
    Route::post('/email/instructor', [\App\Http\Controllers\EmailController::class, 'sendInstructorNotification'])->name('email.instructor');
    Route::post('/email/system', [\App\Http\Controllers\EmailController::class, 'sendSystemNotification'])->name('email.system');
    Route::get('/email/templates', [\App\Http\Controllers\EmailController::class, 'getEmailTemplates'])->name('email.templates');
    Route::post('/email/test', [\App\Http\Controllers\EmailController::class, 'testEmail'])->name('email.test');
    
    // 2FA Routes
    Route::get('/2fa/setup', [\App\Http\Controllers\TwoFactorAuthController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [\App\Http\Controllers\TwoFactorAuthController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [\App\Http\Controllers\TwoFactorAuthController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/verify', [\App\Http\Controllers\TwoFactorAuthController::class, 'showVerification'])->name('2fa.verify');
    Route::post('/2fa/verify', [\App\Http\Controllers\TwoFactorAuthController::class, 'verify'])->name('2fa.verify.post');
    Route::post('/2fa/backup-codes/generate', [\App\Http\Controllers\TwoFactorAuthController::class, 'generateBackupCodes'])->name('2fa.backup.generate');
    Route::get('/2fa/backup-codes', [\App\Http\Controllers\TwoFactorAuthController::class, 'showBackupCodes'])->name('2fa.backup.show');
    
    // Language Routes
    Route::get('/language/{code}', [\App\Http\Controllers\LanguageController::class, 'change'])->name('language.change');
    Route::get('/language', [\App\Http\Controllers\LanguageController::class, 'index'])->name('language.index');
    
    // Widget Routes
    Route::get('/widgets', [\App\Http\Controllers\WidgetController::class, 'index'])->name('widgets.index');
    Route::get('/widgets/manage', [\App\Http\Controllers\WidgetController::class, 'manage'])->name('widgets.manage');
    Route::post('/widgets', [\App\Http\Controllers\WidgetController::class, 'store'])->name('widgets.store');
    Route::put('/widgets/{widget}', [\App\Http\Controllers\WidgetController::class, 'update'])->name('widgets.update');
    Route::delete('/widgets/{widget}', [\App\Http\Controllers\WidgetController::class, 'destroy'])->name('widgets.destroy');
    Route::post('/widgets/positions', [\App\Http\Controllers\WidgetController::class, 'updatePositions'])->name('widgets.positions');
    Route::post('/widgets/{widget}/toggle-collapsed', [\App\Http\Controllers\WidgetController::class, 'toggleCollapsed'])->name('widgets.toggle-collapsed');
    Route::get('/widgets/{widget}/data', [\App\Http\Controllers\WidgetController::class, 'getData'])->name('widgets.data');
    
    // Theme Routes
    Route::post('/theme/{theme}', [\App\Http\Controllers\ThemeController::class, 'setTheme'])->name('theme.set');
    Route::get('/theme', [\App\Http\Controllers\ThemeController::class, 'getCurrentTheme'])->name('theme.current');
    
    // AI Learning Assistant Routes
    Route::get('/ai/recommendations', [\App\Http\Controllers\AILearningAssistantController::class, 'getRecommendations'])->name('ai.recommendations');
    Route::get('/ai/study-plan/{course}', [\App\Http\Controllers\AILearningAssistantController::class, 'getStudyPlan'])->name('ai.study-plan');
    Route::get('/ai/quiz-help/{attempt}/{question}', [\App\Http\Controllers\AILearningAssistantController::class, 'getQuizHelp'])->name('ai.quiz-help');
    Route::get('/ai/learning-path', [\App\Http\Controllers\AILearningAssistantController::class, 'getLearningPath'])->name('ai.learning-path');
    Route::post('/ai/feedback', [\App\Http\Controllers\AILearningAssistantController::class, 'getFeedback'])->name('ai.feedback');
    
    // API Documentation Routes
    Route::get('/api-docs', [\App\Http\Controllers\ApiDocumentationController::class, 'index'])->name('api.docs');
    Route::get('/api-docs/playground', [\App\Http\Controllers\ApiDocumentationController::class, 'playground'])->name('api.playground');
    Route::get('/api-docs/openapi', [\App\Http\Controllers\ApiDocumentationController::class, 'openapi'])->name('api.openapi');
    
    // Advanced Reporting Routes
    Route::get('/reports/dashboard', [\App\Http\Controllers\AdvancedReportingController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('/reports/instructor/{instructor}', [\App\Http\Controllers\AdvancedReportingController::class, 'instructor'])->name('reports.instructor');
    Route::get('/reports/course/{course}', [\App\Http\Controllers\AdvancedReportingController::class, 'course'])->name('reports.course');
    Route::get('/reports/learning', [\App\Http\Controllers\AdvancedReportingController::class, 'learning'])->name('reports.learning');
    Route::get('/reports/financial', [\App\Http\Controllers\AdvancedReportingController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/export/{type}', [\App\Http\Controllers\AdvancedReportingController::class, 'export'])->name('reports.export');
    
    // Gamification Routes
    Route::get('/badges', [\App\Http\Controllers\BadgeController::class, 'index'])->name('badges.index');
    Route::get('/badges/{badge}', [\App\Http\Controllers\BadgeController::class, 'show'])->name('badges.show');
    Route::get('/leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/{leaderboard}', [\App\Http\Controllers\LeaderboardController::class, 'show'])->name('leaderboard.show');
    Route::post('/badges/{badge}/claim', [\App\Http\Controllers\BadgeController::class, 'claim'])->name('badges.claim');
    
    // Language Management Routes
    Route::get('/admin/languages', [\App\Http\Controllers\LanguageController::class, 'adminIndex'])->name('admin.languages.index');
    Route::post('/admin/languages/translations', [\App\Http\Controllers\LanguageController::class, 'updateTranslations'])->name('admin.languages.translations.update');
    Route::post('/admin/languages/import', [\App\Http\Controllers\LanguageController::class, 'importTranslations'])->name('admin.languages.import');
    Route::get('/admin/languages/export', [\App\Http\Controllers\LanguageController::class, 'exportTranslations'])->name('admin.languages.export');
    Route::get('/admin/languages/missing', [\App\Http\Controllers\LanguageController::class, 'getMissingTranslations'])->name('admin.languages.missing');
    Route::post('/admin/languages/auto-translate', [\App\Http\Controllers\LanguageController::class, 'autoTranslate'])->name('admin.languages.auto-translate');
    
    // Security Management Routes
    Route::get('/admin/security', [\App\Http\Controllers\SecurityController::class, 'index'])->name('admin.security.index');
    Route::post('/admin/security/whitelist', [\App\Http\Controllers\SecurityController::class, 'addToWhitelist'])->name('admin.security.whitelist.add');
    Route::delete('/admin/security/whitelist/{ip}', [\App\Http\Controllers\SecurityController::class, 'removeFromWhitelist'])->name('admin.security.whitelist.remove');
    Route::post('/admin/security/blacklist', [\App\Http\Controllers\SecurityController::class, 'addToBlacklist'])->name('admin.security.blacklist.add');
    Route::delete('/admin/security/blacklist/{ip}', [\App\Http\Controllers\SecurityController::class, 'removeFromBlacklist'])->name('admin.security.blacklist.remove');
    Route::get('/admin/security/logs', [\App\Http\Controllers\SecurityController::class, 'logs'])->name('admin.security.logs');
    Route::get('/admin/security/stats', [\App\Http\Controllers\SecurityController::class, 'stats'])->name('admin.security.stats');
    Route::post('/admin/security/clean-logs', [\App\Http\Controllers\SecurityController::class, 'cleanLogs'])->name('admin.security.clean-logs');
    Route::get('/admin/security/export-logs', [\App\Http\Controllers\SecurityController::class, 'exportLogs'])->name('admin.security.export-logs');
    
    // Push Notification Routes
    Route::post('/notifications/fcm-token', [\App\Http\Controllers\PushNotificationController::class, 'updateToken'])->name('notifications.fcm-token');
    Route::delete('/notifications/fcm-token', [\App\Http\Controllers\PushNotificationController::class, 'removeToken'])->name('notifications.fcm-token.remove');
    Route::post('/notifications/subscribe/{topic}', [\App\Http\Controllers\PushNotificationController::class, 'subscribeToTopic'])->name('notifications.subscribe');
    Route::post('/notifications/unsubscribe/{topic}', [\App\Http\Controllers\PushNotificationController::class, 'unsubscribeFromTopic'])->name('notifications.unsubscribe');
    Route::post('/admin/notifications/send', [\App\Http\Controllers\PushNotificationController::class, 'sendNotification'])->name('admin.notifications.send');
    Route::post('/admin/notifications/test', [\App\Http\Controllers\PushNotificationController::class, 'testNotification'])->name('admin.notifications.test');
    Route::get('/admin/notifications/stats', [\App\Http\Controllers\PushNotificationController::class, 'stats'])->name('admin.notifications.stats');
    
    // Webhook Routes
    Route::get('/admin/webhooks', [\App\Http\Controllers\WebhookController::class, 'index'])->name('admin.webhooks.index');
    Route::post('/admin/webhooks', [\App\Http\Controllers\WebhookController::class, 'store'])->name('admin.webhooks.store');
    Route::get('/admin/webhooks/{webhook}', [\App\Http\Controllers\WebhookController::class, 'show'])->name('admin.webhooks.show');
    Route::put('/admin/webhooks/{webhook}', [\App\Http\Controllers\WebhookController::class, 'update'])->name('admin.webhooks.update');
    Route::delete('/admin/webhooks/{webhook}', [\App\Http\Controllers\WebhookController::class, 'destroy'])->name('admin.webhooks.destroy');
    Route::post('/admin/webhooks/{webhook}/test', [\App\Http\Controllers\WebhookController::class, 'test'])->name('admin.webhooks.test');
    Route::post('/admin/webhooks/retry-failed', [\App\Http\Controllers\WebhookController::class, 'retryFailed'])->name('admin.webhooks.retry-failed');
    Route::get('/admin/webhooks/logs', [\App\Http\Controllers\WebhookController::class, 'logs'])->name('admin.webhooks.logs');
    Route::get('/admin/webhooks/stats', [\App\Http\Controllers\WebhookController::class, 'stats'])->name('admin.webhooks.stats');
    
    // Dashboard Widget Routes
    Route::get('/dashboard/widget/data', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'getWidgetData'])->name('dashboard.widget.data');
    Route::post('/dashboard/widget/layout', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'saveWidgetLayout'])->name('dashboard.widget.layout');
    Route::post('/dashboard/widget/add', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'addWidget'])->name('dashboard.widget.add');
    Route::delete('/dashboard/widget/remove', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'removeWidget'])->name('dashboard.widget.remove');
    Route::post('/dashboard/widget/toggle', [\App\Http\Controllers\backend\InstructorDashboardController::class, 'toggleWidget'])->name('dashboard.widget.toggle');

    // Earnings
    Route::get('/earnings', [\App\Http\Controllers\backend\InstructorEarningsController::class, 'index'])->name('earnings');
    Route::get('/earnings/payments', [\App\Http\Controllers\backend\InstructorEarningsController::class, 'payments'])->name('earnings.payments');
    Route::get('/earnings/coupons', [\App\Http\Controllers\backend\InstructorEarningsController::class, 'coupons'])->name('earnings.coupons');

    // Settings
    Route::get('/settings/profile', [\App\Http\Controllers\backend\InstructorSettingsController::class, 'profile'])->name('settings.profile');
    Route::get('/settings/account', [\App\Http\Controllers\backend\InstructorSettingsController::class, 'account'])->name('settings.account');
    Route::get('/settings/language', [\App\Http\Controllers\backend\InstructorSettingsController::class, 'language'])->name('settings.language');
    Route::get('/settings/documents', [\App\Http\Controllers\backend\InstructorSettingsController::class, 'documents'])->name('settings.documents');

    // Help & Support
    Route::get('/help/faq', [\App\Http\Controllers\backend\InstructorHelpController::class, 'faq'])->name('help.faq');
    Route::get('/help/ticket', [\App\Http\Controllers\backend\InstructorHelpController::class, 'ticket'])->name('help.ticket');
    Route::get('/help/community', [\App\Http\Controllers\backend\InstructorHelpController::class, 'community'])->name('help.community');

    Route::post('/logout', [InstructorController::class, 'destroy'])
        ->name('logout');

    Route::get('/profile', [InstructorProfileController::class, 'index'])->name('profile');
    Route::post('/profile/store', [InstructorProfileController::class, 'store'])->name('profile.store');
    Route::get('/setting', [InstructorProfileController::class, 'setting'])->name('setting');
    Route::post('/password/setting', [InstructorProfileController::class, 'passwordSetting'])->name('passwordSetting');

    Route::resource('course', CourseController::class);
    Route::get('/get-subcategories/{categoryId}', [CategoryController::class, 'getSubcategories']);

    Route::resource('course-section', CourseSectionController::class);

    Route::resource('lecture', LectureController::class);

    Route::resource('coupon', CouponController::class);
});

// Quiz Routes
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::resource('quizzes', \App\Http\Controllers\backend\InstructorQuizController::class);
    Route::post('quizzes/{quiz}/toggle-status', [\App\Http\Controllers\backend\InstructorQuizController::class, 'toggleStatus'])->name('quizzes.toggle-status');
    Route::get('quizzes/{quiz}/statistics', [\App\Http\Controllers\backend\InstructorQuizController::class, 'statistics'])->name('quizzes.statistics');
    Route::get('quizzes/{quiz}/export-results', [\App\Http\Controllers\backend\InstructorQuizController::class, 'exportResults'])->name('quizzes.export-results');
    
    // Quiz Questions
    Route::get('quizzes/{quiz}/questions/create', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'create'])->name('quizzes.questions.create');
    Route::post('quizzes/{quiz}/questions', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'store'])->name('quizzes.questions.store');
    Route::get('quizzes/{quiz}/questions/{question}/edit', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'edit'])->name('quizzes.questions.edit');
    Route::put('quizzes/{quiz}/questions/{question}', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'update'])->name('quizzes.questions.update');
    Route::delete('quizzes/{quiz}/questions/{question}', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    Route::post('quizzes/{quiz}/questions/{question}/toggle-status', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'toggleStatus'])->name('quizzes.questions.toggle-status');
    Route::post('quizzes/{quiz}/questions/reorder', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'reorder'])->name('quizzes.questions.reorder');
    Route::post('quizzes/{quiz}/questions/bulk-import', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'bulkImport'])->name('quizzes.questions.bulk-import');
    Route::get('quizzes/{quiz}/questions/export', [\App\Http\Controllers\backend\InstructorQuizQuestionController::class, 'export'])->name('quizzes.questions.export');
});

Route::middleware(['auth', 'role:user'])->prefix('student')->name('student.')->group(function () {
    Route::get('quizzes', [\App\Http\Controllers\frontend\StudentQuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [\App\Http\Controllers\frontend\StudentQuizController::class, 'show'])->name('quizzes.show');
    Route::get('quizzes/{quiz}/start', [\App\Http\Controllers\frontend\StudentQuizController::class, 'start'])->name('quizzes.start');
    Route::post('quizzes/attempts/{attempt}/submit', [\App\Http\Controllers\frontend\StudentQuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('quizzes/attempts/{attempt}/result', [\App\Http\Controllers\frontend\StudentQuizController::class, 'result'])->name('quizzes.result');
    Route::get('quizzes/attempts/{attempt}/review', [\App\Http\Controllers\frontend\StudentQuizController::class, 'review'])->name('quizzes.review');
    Route::get('quizzes/attempts/{attempt}/resume', [\App\Http\Controllers\frontend\StudentQuizController::class, 'resume'])->name('quizzes.resume');
    Route::post('quizzes/attempts/{attempt}/abandon', [\App\Http\Controllers\frontend\StudentQuizController::class, 'abandon'])->name('quizzes.abandon');
    Route::get('quizzes/history', [\App\Http\Controllers\frontend\StudentQuizController::class, 'history'])->name('quizzes.history');
    Route::get('quizzes/statistics', [\App\Http\Controllers\frontend\StudentQuizController::class, 'statistics'])->name('quizzes.statistics');
    Route::get('courses/{course}/quiz-progress', [\App\Http\Controllers\frontend\StudentQuizController::class, 'courseProgress'])->name('quizzes.course-progress');
});


//user Route

Route::middleware(['auth', 'verified', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [UserController::class, 'destroy'])
        ->name('logout');

    //Profile

    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::post('/profile/store', [UserProfileController::class, 'store'])->name('profile.store');
    Route::get('/setting', [UserProfileController::class, 'setting'])->name('setting');
    Route::post('/password/setting', [UserProfileController::class, 'passwordSetting'])->name('passwordSetting');

    /* Wishlist controller */
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');
    Route::get('wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');
    Route::post('wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::post('wishlist/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
});


//Frontend Route

Route::middleware(['track.analytics'])->group(function () {
    Route::get('/', [FrontendDashboardController::class, 'home'])->name('frontend.home');
    Route::get('/course-details/{slug}', [FrontendDashboardController::class, 'view'])->name('course-details');
});

// Stripe Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.create-payment-intent');
    Route::post('/stripe/confirm-payment', [StripeController::class, 'confirmPayment'])->name('stripe.confirm-payment');
    Route::post('/stripe/create-subscription', [StripeController::class, 'createSubscription'])->name('stripe.create-subscription');
    Route::post('/stripe/cancel-subscription', [StripeController::class, 'cancelSubscription'])->name('stripe.cancel-subscription');
    Route::get('/stripe/subscription', [StripeController::class, 'getSubscription'])->name('stripe.get-subscription');
    Route::get('/stripe/subscription-plans', [StripeController::class, 'getSubscriptionPlans'])->name('stripe.get-subscription-plans');
    Route::get('/stripe/payment-methods', [StripeController::class, 'getPaymentMethods'])->name('stripe.get-payment-methods');
    Route::post('/stripe/add-payment-method', [StripeController::class, 'addPaymentMethod'])->name('stripe.add-payment-method');
    Route::delete('/stripe/remove-payment-method', [StripeController::class, 'removePaymentMethod'])->name('stripe.remove-payment-method');
});

// Stripe Webhook (no auth required)
Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook'])->name('stripe.webhook');

/* Public Wishlist Routes */
Route::middleware(['auth'])->group(function () {
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');
});

/* Cart Controller */
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart/all', [CartController::class, 'cartAll']);
Route::get('/fetch/cart', [CartController::class, 'fetchCart']);
Route::post('/remove/cart', [CartController::class, 'removeCart']);


/*  Checkout */
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
/* Coupon Apply    */
Route::post('/apply-coupon', [CouponController::class, 'applyCoupon']);


/* Auth Protected Route */

Route::middleware('auth')->group(function () {

    /* Order  */
    Route::post('/order', [OrderController::class, 'order'])->name('order');
    Route::get('/payment-success', [OrderController::class, 'success'])->name('success');
    Route::get('/payment-cancel', [OrderController::class, 'cancel'])->name('cancel');
    //Route::resource('rating', RatingController::class);
});

// Search Routes
Route::get('/search', [SearchController::class, 'index'])->name('frontend.search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/search/subcategories', [SearchController::class, 'getSubcategories'])->name('search.subcategories');
Route::get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');

// Frontend extra routes for navbar
Route::get('/courses', function () {
    return view('frontend.pages.course-list'); // Eğer yoksa, bir blade dosyası oluşturulabilir
})->name('frontend.courses');

Route::get('/blog', function () {
    return view('frontend.pages.blog'); // Eğer yoksa, bir blade dosyası oluşturulabilir
})->name('frontend.blog');

Route::get('/about', function () {
    return view('frontend.pages.about'); // Eğer yoksa, bir blade dosyası oluşturulabilir
})->name('frontend.about');

Route::get('/contact', function () {
    return view('frontend.pages.contact'); // Eğer yoksa, bir blade dosyası oluşturulabilir
})->name('frontend.contact');

Route::get('/become-instructor', function () {
    return view('frontend.pages.become-instructor'); // Eğer yoksa, bir blade dosyası oluşturulabilir
})->name('frontend.becomeInstructor');

Route::post('/become-instructor', [InstructorApplicationController::class, 'store'])->name('frontend.becomeInstructor.store');

Route::post('/contact', [ContactMessageController::class, 'store'])->name('frontend.contact.store');

Route::get('/pricing', [SubscriptionPlanController::class, 'index'])->name('pricing');

Route::post('/subscribe/{plan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'subscribe'])->middleware('auth')->name('subscribe');

require __DIR__ . '/auth.php';

// Admin Stripe Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/stripe/subscriptions', [AdminStripeController::class, 'subscriptions'])->name('stripe.subscriptions');
    Route::get('/stripe/payments', [AdminStripeController::class, 'payments'])->name('stripe.payments');
    Route::get('/stripe/statistics', [AdminStripeController::class, 'statistics'])->name('stripe.statistics');
    Route::get('/stripe/user/{userId}/subscription', [AdminStripeController::class, 'userSubscription'])->name('stripe.user.subscription');
    Route::post('/stripe/subscription/{subscriptionId}/status', [AdminStripeController::class, 'updateSubscriptionStatus'])->name('stripe.subscription.status');
});
