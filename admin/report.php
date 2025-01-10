<?php
include "./header.php";

include "./includes/config.php"; //  database configuration

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    // Get the start and end date from the form
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // query to fetch the report data
    $sql = "
    SELECT
        u.username,
        o.id AS order_id,
        SUM(oi.quantity * oi.price) AS total_price
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.status = 'completed'
    AND o.created_at BETWEEN '$start_date' AND '$end_date'
    GROUP BY o.id
    ORDER BY o.created_at DESC;
    ";

    // Execute the query
    $result = $conn->query($sql);

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
</form>


<?php
if (isset($result) && $result->num_rows > 0) {
    $total_sales = 0;

    echo "<h2>Order Report from $start_date to $end_date</h2>";
    echo "<table>
            <tr>
                <th>Username</th>
                <th>Order ID</th>
                <th>Total Price</th>
                
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $total_sales += $row['total_price'];
        echo "<tr>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['order_id']) . "</td>
                <td>" . number_format($row['total_price'], 2) . "</td>
                </tr>";
    }
    echo "<tr><td colspan='2' style='border-right:1px solid #ddd;'> <b>Total Sales Amount</b></td>
    <td style='font-weight:bold;'>" . number_format($total_sales, 2) . "</td></tr>";
   

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
            changeYear: true
        });
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
