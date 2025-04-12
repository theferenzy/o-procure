<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../pages/login.php");
    exit();
}

include '../config/database.php';
include '../includes/header.php';
?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Manage Pending Users</h1>

    <?php
    $sql = "SELECT user_id, full_name, email, role, company_name, contact_number, status FROM users WHERE status = 'Pending'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0): ?>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border p-2">Full Name</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">Role</th>
                    <th class="border p-2">Company Name</th>
                    <th class="border p-2">Contact Number</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="border p-2"><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($row['role']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($row['company_name'] ?? 'N/A'); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td class="border p-2">
                            <a href="approve_user.php?id=<?php echo $row['user_id']; ?>" class="bg-green-500 text-white p-2 rounded">Approve</a>
                            <a href="reject_user.php?id=<?php echo $row['user_id']; ?>" class="bg-red-500 text-white p-2 rounded ml-2">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending users.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>