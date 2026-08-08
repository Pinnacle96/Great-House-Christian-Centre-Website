<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token ?? \App\Core\Security::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <title><?= $title ?? 'Admin' ?> - GHCC CMS</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>.sidebar-transition { transition: all 0.3s ease; }</style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Mobile Menu Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- Desktop Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-brand-green-900 to-brand-green-800 text-white flex-shrink-0 hidden lg:flex flex-col sidebar-transition">
            <!-- Logo -->
            <div class="p-6 flex items-center justify-center border-b border-brand-green-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-brand-gold rounded-full flex items-center justify-center">
                        <i class="fas fa-church text-brand-green-900 text-sm"></i>
                    </div>
                    <span class="font-bold text-xl tracking-wider text-white">GHCC ADMIN</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto py-6">
                <nav class="px-4 space-y-1">
                    <?php 
                    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
                    $isActive = function($path) use ($currentPath) {
                        return strpos($currentPath, $path) !== false ? 'bg-brand-green-600 text-white shadow-lg' : 'text-brand-green-100 hover:bg-brand-green-700';
                    };
                    $canManageGlobalFrontend = \App\Core\BranchScope::canManageGlobalFrontend();
                    ?>
                    
                    <a href="<?= APP_URL ?>/admin" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin') ?> <?= $currentPath === '/admin' ? 'bg-brand-green-600' : '' ?>">
                        <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <?php if (in_array($_SESSION['role_id'], [1, 2, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/branches" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/branches') ?>">
                        <i class="fas fa-code-branch w-5 h-5 mr-3"></i>
                        <span class="font-medium">Branches</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/members" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/members') ?>">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        <span class="font-medium">Members</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/sermons" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/sermons') ?>">
                        <i class="fas fa-microphone-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Sermons</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 5, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/events" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/events') ?>">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Events</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 5, 6, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/registrations" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/registrations') ?>">
                        <i class="fas fa-clipboard-list w-5 h-5 mr-3"></i>
                        <span class="font-medium">Registrations</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/finance" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/finance') ?>">
                        <i class="fas fa-donate w-5 h-5 mr-3"></i>
                        <span class="font-medium">Finance</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/prayers" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/prayers') ?>">
                        <i class="fas fa-praying-hands w-5 h-5 mr-3"></i>
                        <span class="font-medium">Prayer Requests</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/communication" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/communication') ?>">
                        <i class="fas fa-comments w-5 h-5 mr-3"></i>
                        <span class="font-medium">Communication</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/contact-messages" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/contact-messages') ?>">
                        <i class="fas fa-inbox w-5 h-5 mr-3"></i>
                        <span class="font-medium">Contact Inbox</span>
                    </a>
                    
                    <?php if ($canManageGlobalFrontend): ?>
                    <div class="pt-4 pb-2 px-4 text-xs font-bold text-brand-green-300 uppercase tracking-widest">Page Content</div>
                    
                    <a href="<?= APP_URL ?>/admin/page-content?page=home" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'home' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        <span class="font-medium">Home Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=about" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'about' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-info-circle w-5 h-5 mr-3"></i>
                        <span class="font-medium">About Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/team" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/team') ?>">
                        <i class="fas fa-user-tie w-5 h-5 mr-3"></i>
                        <span class="font-medium">Team Members</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=services" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'services' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-hand-holding-heart w-5 h-5 mr-3"></i>
                        <span class="font-medium">Services Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=sermons" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'sermons' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-microphone w-5 h-5 mr-3"></i>
                        <span class="font-medium">Sermons Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=events" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'events' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-calendar-day w-5 h-5 mr-3"></i>
                        <span class="font-medium">Events Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=give" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'give' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-heart w-5 h-5 mr-3"></i>
                        <span class="font-medium">Give Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=contact" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'contact' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>">
                        <i class="fas fa-address-book w-5 h-5 mr-3"></i>
                        <span class="font-medium">Contact Page</span>
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <div class="pt-4 pb-2 px-4">
                        <span class="text-xs font-bold text-brand-green-300 uppercase tracking-widest">System Superadmin</span>
                    </div>

                    <a href="<?= APP_URL ?>/admin/users" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/users') ?>">
                        <i class="fas fa-user-shield w-5 h-5 mr-3"></i>
                        <span class="font-medium">User Management</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/settings" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/settings') ?>">
                        <i class="fas fa-cog w-5 h-5 mr-3"></i>
                        <span class="font-medium">Global Settings</span>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            
            <!-- User Section -->
            <div class="p-6 border-t border-brand-green-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-brand-gold rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-brand-green-900"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                        <p class="text-xs text-brand-green-200 truncate"><?= htmlspecialchars($_SESSION['role_name'] ?? 'Role') ?></p>
                    </div>
                </div>
                <a href="<?= APP_URL ?>/logout" class="flex items-center text-brand-green-200 hover:text-white transition-colors text-sm">
                    <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 flex h-dvh w-64 flex-col overflow-hidden bg-gradient-to-b from-brand-green-900 to-brand-green-800 text-white transform -translate-x-full lg:translate-x-0 lg:hidden sidebar-transition">
            <div class="p-6 flex items-center justify-between border-b border-brand-green-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-brand-gold rounded-full flex items-center justify-center">
                        <i class="fas fa-church text-brand-green-900 text-sm"></i>
                    </div>
                    <span class="font-bold text-xl tracking-wider text-white">GHCC ADMIN</span>
                </div>
                <button onclick="closeMobileMenu()" class="text-brand-green-200 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain py-6">
                <nav class="px-4 space-y-1">
                    <a href="<?= APP_URL ?>/admin" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <?php if (in_array($_SESSION['role_id'], [1, 2, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/branches" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/branches') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-code-branch w-5 h-5 mr-3"></i>
                        <span class="font-medium">Branches</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/members" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/members') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        <span class="font-medium">Members</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/sermons" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/sermons') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-microphone-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Sermons</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 5, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/events" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/events') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Events</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 5, 6, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/registrations" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/registrations') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-clipboard-list w-5 h-5 mr-3"></i>
                        <span class="font-medium">Registrations</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/finance" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/finance') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-donate w-5 h-5 mr-3"></i>
                        <span class="font-medium">Finance</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 7])): ?>
                    <a href="<?= APP_URL ?>/admin/prayers" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/prayers') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-praying-hands w-5 h-5 mr-3"></i>
                        <span class="font-medium">Prayer Requests</span>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/communication" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/communication') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-comments w-5 h-5 mr-3"></i>
                        <span class="font-medium">Communication</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/contact-messages" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/contact-messages') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-inbox w-5 h-5 mr-3"></i>
                        <span class="font-medium">Contact Inbox</span>
                    </a>
                    
                    <?php if ($canManageGlobalFrontend): ?>
                    <div class="pt-4 pb-2 px-4 text-xs font-bold text-brand-green-300 uppercase tracking-widest">Branding & Content</div>
                    
                    <a href="<?= APP_URL ?>/admin/page-content?page=home" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/page-content') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-edit w-5 h-5 mr-3"></i>
                        <span class="font-medium">Home Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/page-content?page=about" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= isset($_GET['page']) && $_GET['page'] === 'about' ? 'bg-brand-green-600 text-white' : 'text-brand-green-100 hover:bg-brand-green-700' ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-info-circle w-5 h-5 mr-3"></i>
                        <span class="font-medium">About Page</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/team" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/team') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-user-tie w-5 h-5 mr-3"></i>
                        <span class="font-medium">Team Members</span>
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="<?= APP_URL ?>/admin/settings" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/settings') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-cogs w-5 h-5 mr-3"></i>
                        <span class="font-medium">Global Settings</span>
                    </a>

                    <a href="<?= APP_URL ?>/admin/users" class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 <?= $isActive('/admin/users') ?>" onclick="closeMobileMenu()">
                        <i class="fas fa-users-cog w-5 h-5 mr-3"></i>
                        <span class="font-medium">User Management</span>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            
            <div class="p-6 border-t border-brand-green-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-brand-gold rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-brand-green-900"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                        <p class="text-xs text-brand-green-200 truncate"><?= htmlspecialchars($_SESSION['role_name'] ?? 'Role') ?></p>
                    </div>
                </div>
                <a href="<?= APP_URL ?>/logout" class="flex items-center text-brand-green-200 hover:text-white transition-colors text-sm" onclick="closeMobileMenu()">
                    <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-4 lg:px-6">
                <div class="flex items-center">
                    <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-600 hover:text-brand-green-600 p-2 rounded-lg transition-colors">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800 ml-2 lg:ml-4"><?= $title ?? 'Dashboard' ?></h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="p-2 text-gray-500 hover:text-brand-green-600 transition-colors">
                            <i class="fas fa-bell"></i>
                            <?php
                            // Get count of new prayer requests
                            $prayerCount = 0;
                            if (isset($_SESSION['user_id'])) {
                                $db = \App\Core\Database::getInstance()->getConnection();
                                $sql = "SELECT COUNT(*) as count FROM prayer_requests WHERE status = 'new'";
                                [$sql, $params] = \App\Core\BranchScope::appendWhere($sql);
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                $result = $stmt->fetch();
                                $prayerCount = $result['count'] ?? 0;
                            }
                            ?>
                            <?php if ($prayerCount > 0): ?>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?= $prayerCount ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['role_name'] ?? 'Role') ?></p>
                        </div>
                        <div class="w-8 h-8 bg-brand-green-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6">
