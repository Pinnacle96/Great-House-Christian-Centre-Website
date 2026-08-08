<?php

use App\Controllers\Public\HomeController;
use App\Controllers\Public\GiveController;
use App\Controllers\Public\SubscriptionController;
use App\Controllers\Public\ContactController;
use App\Controllers\Public\BranchRegistrationController;
use App\Controllers\Public\BranchPublicController;
use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\MemberController;
use App\Controllers\Admin\SermonController;
use App\Controllers\Admin\EventController;
use App\Controllers\Admin\FinanceController;
use App\Controllers\Admin\PrayerController;
use App\Controllers\Admin\CommunicationController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\GroupController;
use App\Controllers\Admin\ServiceController;
use App\Controllers\Admin\PageContentController;
use App\Controllers\Admin\TeamController;
use App\Controllers\Admin\ContactMessageController;
use App\Controllers\Admin\BranchController;
use App\Controllers\Public\EventController as PublicEventController;
use App\Controllers\Member\PortalController;

// Public Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/ministries', [HomeController::class, 'ministries']); // Added Ministries Route
$router->get('/groups', [HomeController::class, 'groups']); // Added Groups Route
$router->get('/services', [HomeController::class, 'services']); // Added Services Route
$router->get('/sermons', [HomeController::class, 'sermons']);
$router->get('/events', [HomeController::class, 'events']);
$router->get('/events/([a-z0-9-]+)', [HomeController::class, 'eventDetails']);
$router->get('/events/([a-z0-9-]+)/register', [PublicEventController::class, 'registrationForm']);
$router->post('/events/([a-z0-9-]+)/register', [PublicEventController::class, 'register']);
$router->get('/branches', [BranchPublicController::class, 'index']);
$router->get('/branches/([a-z0-9-]+)', [BranchPublicController::class, 'show']);
$router->get('/branches/([a-z0-9-]+)/events', [BranchPublicController::class, 'events']);
$router->get('/branches/([a-z0-9-]+)/events/([a-z0-9-]+)', [BranchPublicController::class, 'eventDetails']);
$router->get('/branches/([a-z0-9-]+)/give', [GiveController::class, 'branch']);
$router->post('/branches/([a-z0-9-]+)/give/process', [GiveController::class, 'branchProcess']);
$router->get('/give', [GiveController::class, 'index']);
$router->post('/give/process', [GiveController::class, 'process']);
$router->get('/give/callback', [GiveController::class, 'callback']);
$router->post('/give/webhook', [GiveController::class, 'webhook']);
$router->post('/subscribe', [SubscriptionController::class, 'subscribe']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact/process', [ContactController::class, 'processContact']);
$router->post('/prayer/submit', [ContactController::class, 'submitPrayer']);
$router->get('/b/([a-f0-9]{32})/register', [BranchRegistrationController::class, 'show']);
$router->post('/b/([a-f0-9]{32})/register', [BranchRegistrationController::class, 'store']);

// Auth Routes
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Member Portal
$router->get('/member', [PortalController::class, 'index']);

use App\Controllers\Admin\RegistrationController;

// Admin Routes
$router->get('/admin', [DashboardController::class, 'index']);

// Admin - Branches
$router->get('/admin/branches', [BranchController::class, 'index']);
$router->get('/admin/branches/create', [BranchController::class, 'create']);
$router->post('/admin/branches/store', [BranchController::class, 'store']);
$router->get('/admin/branches/edit/(\d+)', [BranchController::class, 'edit']);
$router->post('/admin/branches/update/(\d+)', [BranchController::class, 'update']);
$router->post('/admin/branches/regenerate-token/(\d+)', [BranchController::class, 'regenerateToken']);
$router->post('/admin/branches/make-headquarters/(\d+)', [BranchController::class, 'makeHeadquarters']);
$router->post('/admin/branches/delete/(\d+)', [BranchController::class, 'delete']);
$router->post('/admin/branches/test-paystack/(\d+)', [BranchController::class, 'testPaystack']);
$router->post('/admin/branches/test-email/(\d+)', [BranchController::class, 'testEmail']);

// Admin - Registrations
$router->get('/admin/registrations', [RegistrationController::class, 'index']);
$router->get('/admin/registrations/create', [RegistrationController::class, 'create']); // Added
$router->post('/admin/registrations/store', [RegistrationController::class, 'store']); // Added
$router->post('/admin/registrations/check-in/(\d+)', [RegistrationController::class, 'checkIn']);
$router->get('/admin/registrations/export-pdf', [RegistrationController::class, 'exportPdf']);
$router->get('/admin/registrations/export-csv', [RegistrationController::class, 'exportCsv']);
$router->post('/admin/registrations/send-reminder', [RegistrationController::class, 'sendReminder']);

// Admin - Members
$router->get('/admin/members', [MemberController::class, 'index']);
$router->get('/admin/members/create', [MemberController::class, 'create']);
$router->post('/admin/members/store', [MemberController::class, 'store']);
$router->get('/admin/members/show/(\d+)', [MemberController::class, 'show']);
$router->get('/admin/members/edit/(\d+)', [MemberController::class, 'edit']);
$router->post('/admin/members/update/(\d+)', [MemberController::class, 'update']);
$router->post('/admin/members/addNote', [MemberController::class, 'addNote']);

// Admin - Sermons
$router->get('/admin/sermons', [SermonController::class, 'index']);
$router->get('/admin/sermons/create', [SermonController::class, 'create']);
$router->post('/admin/sermons/store', [SermonController::class, 'store']);

// Admin - Events
$router->get('/admin/events', [EventController::class, 'index']);
$router->get('/admin/events/create', [EventController::class, 'create']);
$router->post('/admin/events/store', [EventController::class, 'store']);

$router->get('/admin/events/edit/(\d+)', [EventController::class, 'edit']);
$router->post('/admin/events/update/(\d+)', [EventController::class, 'update']);
$router->post('/admin/events/delete/(\d+)', [EventController::class, 'delete']);

// Admin - Finance
$router->get('/admin/finance', [FinanceController::class, 'index']);
$router->get('/admin/finance/create', [FinanceController::class, 'create']);
$router->post('/admin/finance/store', [FinanceController::class, 'store']);

// Admin - Prayers
$router->get('/admin/prayers', [PrayerController::class, 'index']);
$router->post('/admin/prayers/mark-prayed/(\d+)', [PrayerController::class, 'markPrayed']);
$router->post('/admin/prayers/archive/(\d+)', [PrayerController::class, 'archive']);
$router->post('/admin/prayers/delete/(\d+)', [PrayerController::class, 'delete']);

// Admin - Communication
$router->get('/admin/communication', [CommunicationController::class, 'index']);
$router->post('/admin/communication/send', [CommunicationController::class, 'send']);
$router->get('/admin/contact-messages', [ContactMessageController::class, 'index']);
$router->post('/admin/contact-messages/read/(\d+)', [ContactMessageController::class, 'markRead']);
$router->post('/admin/contact-messages/archive/(\d+)', [ContactMessageController::class, 'archive']);
$router->post('/admin/contact-messages/delete/(\d+)', [ContactMessageController::class, 'delete']);

// Admin - Groups
$router->get('/admin/groups', [GroupController::class, 'index']);
$router->get('/admin/groups/create', [GroupController::class, 'create']);
$router->post('/admin/groups/store', [GroupController::class, 'store']);
$router->get('/admin/groups/show/(\d+)', [GroupController::class, 'show']);
$router->get('/admin/groups/edit/(\d+)', [GroupController::class, 'edit']);
$router->post('/admin/groups/update/(\d+)', [GroupController::class, 'update']);
$router->post('/admin/groups/addMember', [GroupController::class, 'addMember']);
$router->post('/admin/groups/removeMember', [GroupController::class, 'removeMember']);
$router->post('/admin/groups/updateRole', [GroupController::class, 'updateRole']);

// Admin - Service Planning
$router->get('/admin/services', [ServiceController::class, 'index']);
$router->get('/admin/services/create', [ServiceController::class, 'create']);
$router->post('/admin/services/store', [ServiceController::class, 'store']);
$router->get('/admin/services/show/(\d+)', [ServiceController::class, 'show']);
$router->get('/admin/services/edit/(\d+)', [ServiceController::class, 'edit']);
$router->post('/admin/services/update/(\d+)', [ServiceController::class, 'update']);
$router->post('/admin/services/addRoster', [ServiceController::class, 'addRoster']);
$router->post('/admin/services/removeRoster', [ServiceController::class, 'removeRoster']);
$router->post('/admin/services/updateStatus', [ServiceController::class, 'updateStatus']);
$router->get('/admin/services/getTeamMembers/(\d+)', [ServiceController::class, 'getTeamMembers']);

// Admin - User Management
$router->get('/admin/users', [UserController::class, 'index']);
$router->get('/admin/users/create', [UserController::class, 'create']);
$router->post('/admin/users/store', [UserController::class, 'store']);
$router->get('/admin/users/edit/(\d+)', [UserController::class, 'edit']);
$router->post('/admin/users/update/(\d+)', [UserController::class, 'update']);
$router->post('/admin/users/change-password/(\d+)', [UserController::class, 'changePassword']);
$router->post('/admin/users/delete/(\d+)', [UserController::class, 'delete']);

// Admin - Page Content & Settings
$router->get('/admin/page-content', [PageContentController::class, 'index']);
$router->post('/admin/page-content/update', [PageContentController::class, 'update']);
$router->get('/admin/settings', [PageContentController::class, 'settings']);
$router->post('/admin/settings/update', [PageContentController::class, 'updateSettings']);
$router->post('/admin/settings/test-email', [PageContentController::class, 'testEmail']);
$router->post('/admin/settings/test-paystack', [PageContentController::class, 'testPaystack']);

// Admin - Team Members
$router->get('/admin/team', [TeamController::class, 'index']);
$router->get('/admin/team/create', [TeamController::class, 'create']);
$router->post('/admin/team/store', [TeamController::class, 'store']);
$router->get('/admin/team/edit/(\d+)', [TeamController::class, 'edit']);
$router->post('/admin/team/update/(\d+)', [TeamController::class, 'update']);
$router->post('/admin/team/delete/(\d+)', [TeamController::class, 'delete']);
