<?php
include "config.php";

$sql = "SELECT * FROM reports";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
</head>
<body>

<h2>Community Reports</h2>

<a href="add_report.php">+ Add New Report</a>

<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
?>

<tr>
    <td><?php echo $row['report_id']; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['description']; ?></td>
    <td><?php echo $row['status']; ?></td>

    <td>
        <a href="edit_report.php?id=<?php echo $row['report_id']; ?>">Edit</a> |
        <a href="delete_report.php?id=<?php echo $row['report_id']; ?>">Delete</a>
    </td>
</tr>

<?php
    }
}else{
    echo "<tr><td colspan='5'>No reports found.</td></tr>";
}
?>

</table>

</body>
</html>