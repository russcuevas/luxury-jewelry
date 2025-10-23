<?php
session_start();
include '../connection.php'; // Adjust path as needed

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$filterDate = $_GET['date'] ?? date('Y-m-d');

// Fetch daily sales
$sql = "SELECT * FROM sales WHERE DATE(sale_date) = ? ORDER BY sale_date ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([$filterDate]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalSalesAmount = 0;
$totalTransactions = count($sales);
foreach ($sales as $sale) {
    $totalSalesAmount += $sale['grand_total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Daily Sales Report - <?= date("F j, Y", strtotime($filterDate)) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #000;
        }
        h1, h2 {
            text-align: center;
            color: #752738;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #752738;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #752738;
            color: #fff;
        }
        tfoot td {
            font-weight: bold;
            font-size: 16px;
        }
        .summary {
            margin-top: 20px;
            font-size: 16px;
        }
        @media print {
            button#printBtn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h1>LUXURY JEWELRY</h1>
    <h2>Daily Sales Report</h2>
    <p style="text-align: center; font-size: 16px;">
        Date: <?= date("F j, Y", strtotime($filterDate)) ?><br>
        Total Transactions: <?= $totalTransactions ?><br>
        Total Sales Amount: ₱<?= number_format($totalSalesAmount, 2) ?>
    </p>

    <?php if (empty($sales)) : ?>
        <p style="text-align:center; margin-top: 40px;">No sales found for this date.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Time</th>
                    <th>Cashier ID</th>
                    <th>Payment Method</th>
                    <th>Total (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) : ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['id']) ?></td>
                        <td><?= date("g:i A", strtotime($sale['sale_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['cashier_id']) ?></td>
                        <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                        <td style="text-align:right;"><?= number_format($sale['grand_total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;">TOTAL SALES:</td>
                    <td style="text-align:right;">₱<?= number_format($totalSalesAmount, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div style="text-align:center; margin-top: 30px;">
        <button id="printBtn" onclick="window.print();" style="padding: 10px 20px; font-size: 16px; background-color: #752738; color: #fff; border: none; cursor: pointer;">
            Print Report
        </button>
    </div>
</body>

</html>
