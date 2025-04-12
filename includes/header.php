<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<head>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time() ?>">
</head>

<header class="bg-blue-900 text-white p-4 relative z-50">
    <div class="container mx-auto flex justify-between items-center">
        <img src="../assets/o-procure.png" alt="O-Procure Logo" class="logo">

        <nav>
            <ul class="flex space-x-8 items-center relative">
                <?php if (!isset($_SESSION['role'])): ?>
                    <!-- Public Nav (Guest) -->
                    <li><a href="index.php" class="hover:underline">Home</a></li>
                    <li><a href="about.php" class="hover:underline">About</a></li>

                    <!-- Services Dropdown -->
                    <li class="relative group">
                        <a href="#" class="hover:underline">Services</a>
                        <ul class="absolute hidden group-hover:block bg-white text-blue-900 shadow-md rounded-md mt-2 min-w-[220px] space-y-1 py-2 px-4 z-50">
                            <li><a href="services/buyer/available_contracts.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Available Contracts</a></li>
                            <li class="text-gray-500 italic px-2 py-1 rounded cursor-not-allowed" title="Login required">Place a Bid (Members only)</li>
                            <li><a href="services/etendering.php" class="block hover:bg-blue-100 px-2 py-1 rounded">E-Tendering</a></li>
                            <li><a href="services/others.php" class="block hover:bg-blue-100 px-2 py-1 rounded">Other Services</a></li>
                        </ul>
                    </li>

                    <li><a href="register.php" class="hover:underline">Onboarding</a></li>
                    <li><a href="login.php" class="hover:underline">Login</a></li>

                <?php elseif ($_SESSION['role'] === 'Admin'): ?>
                    <!-- Admin-specific Nav -->
                    <li><a href="../admin/index.php" class="hover:underline">Home</a></li>
                    <li><a href="../admin/manageusers.php" class="hover:underline">Manage Users</a></li>
                    <li><a href="../admin/managebids.php" class="hover:underline">Manage Bids</a></li>
                    <li><a href="../admin/logout.php" class="hover:underline">Logout</a></li>

                <?php elseif ($_SESSION['role'] === 'Buyer'): ?>
                    <!-- Buyer-specific Nav -->
                    <li><a href="pages/available_contracts.php" class="hover:underline">Home</a></li>
                    <li><a href="buyer/myorders.php" class="hover:underline">My Orders</a></li>
                    <li><a href="buyer/profile.php" class="hover:underline">Profile</a></li>
                    <li><a href="buyer/logout.php" class="hover:underline">Logout</a></li>

                <?php elseif ($_SESSION['role'] === 'Supplier'): ?>
                    <!-- Supplier-specific Nav -->
                    <li><a href="index.php" class="hover:underline">Home</a></li>
                    <li><a href="mysupplies.php" class="hover:underline">My Supplies</a></li>
                    <li><a href="profile.php" class="hover:underline">Profile</a></li>
                    <li><a href="../logout.php" class="hover:underline">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
