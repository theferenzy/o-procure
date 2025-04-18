<?php
require_once('../includes/session_security.php');
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us</title>
    <link rel="stylesheet" href="/o-procure/assets/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/o-procure/assets/fontawesome/css/all.min.css">
</head>
<body>

<!-- Hero Banner -->
<section class="relative bg-blue-900 text-white h-80 flex items-center justify-center overflow-hidden">
    <div class="text-center z-10 animate-fade-in">
        <h1 class="text-5xl font-bold mb-2">About O-Procure</h1>
        <p class="text-xl">Bringing transparency and efficiency to procurement in Nigeria.</p>
    </div>
</section>

<!-- Mission Section -->
<section class="p-10 flex flex-col md:flex-row items-center gap-10 bg-white">
    <img src="../assets/img/mission.jpg" alt="Our Mission" class="rounded-lg w-full md:w-1/2 shadow-lg hover:scale-105 transition-transform duration-500">
    <div class="text-gray-800 md:w-1/2">
        <h2 class="text-3xl font-bold mb-4">Mission First</h2>
        <p class="text-lg leading-relaxed">
            At O-Procure, our goal is to revolutionize the procurement process in Nigeria’s oil and gas sector by introducing fair bidding, digital workflows, and access to vetted suppliers.
        </p>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-gray-100 py-12">
    <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-10 text-center">
        <div class="transition-transform hover:scale-105">
            <h3 class="text-4xl font-bold text-blue-800">250+</h3>
            <p class="text-gray-700 mt-2">Successful Bids</p>
        </div>
        <div class="transition-transform hover:scale-105 delay-100">
            <h3 class="text-4xl font-bold text-blue-800">120+</h3>
            <p class="text-gray-700 mt-2">Verified Suppliers</p>
        </div>
        <div class="transition-transform hover:scale-105 delay-200">
            <h3 class="text-4xl font-bold text-blue-800">5</h3>
            <p class="text-gray-700 mt-2">Years of Innovation</p>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="p-10 bg-white">
    <h2 class="text-3xl font-bold text-center mb-8">Our Core Team</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 text-center">
        <div class="hover:shadow-lg p-4 transition duration-300">
            <img src="../assets/img/team1.jpg" class="w-32 h-32 mx-auto rounded-full mb-4" alt="Founder">
            <h4 class="font-semibold">Oluwaferanmi Samson</h4>
            <p class="text-sm text-gray-600">Founder & CEO</p>
        </div>
        <!-- You can add more team members here -->
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>
