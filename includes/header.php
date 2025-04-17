<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<head>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/tailwindcss/tailwind.min.css">
</head>

<header class="bg-blue-900 text-white p-4 relative z-50">
    <div class="container mx-auto flex justify-between items-center">
        <img src="/o-procure/assets/o-procure.png" alt="O-Procure Logo" class="logo">

        <nav>
            <ul class="flex space-x-8 items-center relative">
                <?php if (!isset($_SESSION['role'])): ?>
                    <!-- Guest Navigation -->
                    <li><a href="/o-procure/pages/index.php" class="hover:underline">Home</a></li>
                    <li><a href="/o-procure/pages/about.php" class="hover:underline">About</a></li>
                    <li class="relative group">
                        <a href="#" class="hover:underline">Services</a>
                        <ul class="absolute hidden group-hover:block bg-white text-blue-900 shadow-md rounded-md mt-2 min-w-[220px] space-y-1 py-2 px-4 z-50">
                            <li><a href="/o-procure/pages/available_contracts.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Available Contracts</a></li>
                            <li><a href="/o-procure/pages/services/etendering.php" class="block hover:bg-blue-100 px-2 py-1 rounded">E-Tendering</a></li>
                        </ul>
                    </li>
                    <li><a href="/o-procure/pages/onboarding/prequalify.php" class="hover:underline">Onboarding</a></li>
                    <li><a href="login.php" class="hover:underline">Login</a></li>

                <?php elseif ($_SESSION['role'] === 'Admin'): ?>
                    <!-- Admin Dropdown Navigation -->
                    <li class="relative group">
                        <a href="#" class="hover:underline">Admin Panel ▾</a>
                        <ul class="absolute hidden group-hover:block bg-white text-blue-900 shadow-md rounded-md mt-2 min-w-[220px] space-y-1 py-2 px-4 z-50">
                            <li><a href="/o-procure/admin/index.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Dashboard</a></li>
                            <li><a href="/o-procure/admin/manageusers.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Manage Users</a></li>
                            <li><a href="/o-procure/admin/manage_suppliers.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Manage Suppliers</a></li>
                            <li><a href="/o-procure/admin/review_contracts.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Manage Contracts</a></li>
                            <li><a href="/o-procure/admin/managebids.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Manage Bids</a></li>
                            <li><a href="/o-procure/admin/track_workflow.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Track Workflow</a></li>
                            <li><a href="/o-procure/admin/logout.php" class="block hover:bg-blue-100 px-2 py-1 rounded text-red-600">Logout</a></li>
                        </ul>
                    </li>

                <?php elseif ($_SESSION['role'] === 'Buyer'): ?>
                    <!-- Buyer Navigation -->
                    <li><a href="/o-procure/pages/services/buyer/index.php" class="hover:underline">Home</a></li>
                    <li><a href="/o-procure/pages/services/buyer/managebids.php" class="hover:underline">Manage Bids</a></li>
                    <li><a href="/o-procure/pages/services/buyer/mybids.php" class="hover:underline">Contract Archive</a></li>
                    <li><a href="/o-procure/pages/services/buyer/profile.php" class="hover:underline">Profile</a></li>
                    <li><a href="/o-procure/pages/services/buyer/logout.php" class="hover:underline">Logout</a></li>

                <?php elseif ($_SESSION['role'] === 'Supplier'): ?>
                    <!-- Supplier Navigation -->
                    <li><a href="/o-procure/pages/onboarding/index.php" class="hover:underline">Home</a></li>
                    <li><a href="/o-procure/pages/onboarding/contractbidding.php" class="hover:underline">Available Contracts</a></li>
                    <li><a href="/o-procure/pages/onboarding/mybids.php" class="hover:underline">Bids History</a></li>
                    <li><a href="/o-procure/pages/onboarding/myprofile.php" class="hover:underline">Profile</a></li>
                    <li><a href="../../logout.php" class="hover:underline">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
