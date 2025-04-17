<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../pages/login.php');
    exit();
}

include '../config/database.php';
include '../includes/header.php';

// Fetch all pending supplier profiles
$sql = "SELECT sp.*, u.full_name, u.email 
        FROM supplier_profiles sp 
        JOIN users u ON sp.supplier_id = u.user_id 
        WHERE sp.status = 'Pending'
        ORDER BY sp.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="container">
    <h2 class="page-title">Pending Supplier Pre-Qualification</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>TIN</th>
                    <th>Compliance</th>
                    <th>Documents</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['company_name']) ?></td>
                        <td><?= htmlspecialchars($row['tax_id']) ?></td>
                        <td><?= htmlspecialchars($row['compliance_doc']) ?></td>
                        <td>
                            <a href="../<?= $row['cac_doc'] ?>" target="_blank">CAC Doc</a><br>
                            <a href="../<?= $row['portfolio_doc'] ?>" target="_blank">Portfolio</a>
                        </td>
                        <td>
                            <a href="approve_supplier.php?id=<?= $row['id'] ?>" class="btn-approve">Approve</a>
                            <a href="reject_supplier.php?id=<?= $row['id'] ?>" class="btn-reject">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending suppliers at this time.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
