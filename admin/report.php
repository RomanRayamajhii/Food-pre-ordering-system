<?php
include "./header.php";

include "./includes/config.php"; //  database configuration

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    // Get the start and end date from the form
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Query for detailed completed orders
    $sql = "
    SELECT
        u.username,
        o.id AS order_id,
        o.total_amount AS total_price,
        o.payment_method,
        o.created_at
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.status = 'completed'
    AND DATE(o.created_at) BETWEEN '$start_date' AND '$end_date'
    ORDER BY o.created_at DESC;
    ";

    $result = $conn->query($sql);

    // Query for summary statistics (Completed vs Cancelled vs Others)
    $stats_sql = "
    SELECT 
        status, 
        COUNT(*) as count, 
        SUM(total_amount) as total_val 
    FROM orders 
    WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
    GROUP BY status";
    
    $stats_result = $conn->query($stats_sql);
    $stats = ['completed' => 0, 'cancelled' => 0, 'other' => 0, 'revenue' => 0, 'total_orders' => 0];
    while($row = $stats_result->fetch_assoc()) {
        $stats['total_orders'] += $row['count'];
        if($row['status'] == 'completed') { $stats['completed'] = $row['count']; $stats['revenue'] = $row['total_val']; }
        elseif($row['status'] == 'cancelled') { $stats['cancelled'] = $row['count']; }
        else { $stats['other'] += $row['count']; }
    }
    if (!$result) {
        die("Error executing query: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    
    <!-- jQuery UI CSS for datepicker styling -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        h2 {
            color: #333;
            text-align: center;
        }
        .container{
            margin: 20px;
        }

        form {
            background-color: #fff;
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
            display: inline-block;
        }

        input[type="text"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 200px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
        
        .btn-print {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-print:hover {
            background-color: #1976D2;
            transform: translateY(-1px);
        }

        .btn-print i {
            margin-right: 8px;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-results {
            text-align: center;
            color: red;
            font-size: 18px;
        }
        .stats-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 150px;
        }
        .stat-card h3 { margin: 0; font-size: 14px; color: #666; text-transform: uppercase; }
        .stat-card p { margin: 5px 0 0; font-size: 24px; font-weight: bold; color: #333; }
        .stat-card[stat-total-orders] { border-top: 4px solid #0059ff; }
        .stat-completed { border-top: 4px solid #4CAF50; }
        .stat-cancelled { border-top: 4px solid #f44336; }
        .stat-revenue { border-top: 4px solid #2196F3; }
        @media print {
            .header, form, button { display: none; }
            .container { margin: 0; width: 100%; }
            table { box-shadow: none; }
        }
    </style>
</head>
<body>
<div class="container">
<h2>Generate Report</h2>
<form method="POST" action="">
    <label for="start_date">Start Date:</label>
    <input type="text" id="start_date" name="start_date" placeholder="2025-01-10" required>
    
    <label for="end_date">End Date:</label>
    <input type="text" id="end_date" name="end_date" placeholder="2025-01-10"  required>
    
    <button type="submit">Generate Report</button>
    <?php if (isset($result)): ?>
        <button type="button" class="btn-print" onclick="window.print()"><span><i class="fa-solid fa-print"></i></span>Print Report</button>
    <?php endif; ?>
</form>


<?php
if (isset($result) && $result->num_rows > 0) {
    echo "<h2>Order Report from $start_date to $end_date</h2>";
    
    echo "<div class='stats-grid'>
            <div class='stat-card' stat-total-orders><h3>Total Orders</h3><p>{$stats['total_orders']}</p></div>
            <div class='stat-card stat-completed'>
                <h3>Completed Orders</h3>
                <p>{$stats['completed']}</p>
            </div>
            <div class='stat-card stat-cancelled'>
                <h3>Cancelled Orders</h3>
                <p>{$stats['cancelled']}</p>
            </div>
            <div class='stat-card stat-revenue'>
                <h3>Total Revenue</h3>
                <p>Rs.  " . number_format($stats['revenue'], 2) . "</p>
            </div>
          </div>";

    echo "<table>
            <tr>
                <th>Date</th>
                <th>Username</th>
                <th>Order ID</th>
                <th>Payment</th>
                <th>Total Price</th>
                
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $total_sales += $row['total_price'];
        echo "<tr>
                <td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>#" . htmlspecialchars($row['order_id']) . "</td>
                <td>" . ucfirst($row['payment_method']) . "</td>
                <td>Rs. " . number_format($row['total_price'], 2) . "</td>
                </tr>";
    }
    echo "<tr><td colspan='4' style='text-align:right;'> <b>Total Completed Sales</b></td>
    <td style='font-weight:bold; color: #4CAF50;'>Rs. " . number_format($stats['revenue'], 2) . "</td></tr>";
   

    echo "</table>";
} elseif (isset($result)) {
    echo "<p class='no-results'>No results found for the given date range.</p>";
}
?>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    // datepicker for both start and end date fields
    $(document).ready(function () {
        $("#start_date, #end_date").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            maxDate: 0 // Restrict selection to today and past dates only
        });
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
