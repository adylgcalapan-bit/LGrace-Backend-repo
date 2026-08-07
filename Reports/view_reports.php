<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Locations</title>
</head>

<body>

    <h1>Locations</h1>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Status</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>

        <tbody id="locationTable">
        </tbody>

    </table>
<script>

fetch('http://localhost/community_visibility_system/codeigniter/public/api/locations')

    .then(response => response.json())

    .then(result => {
        let table = document.getElementById('locationTable');

result.data.forEach(location => {

    table.innerHTML += `
        <tr>
            <td>${location.id}</td>
            <td>${location.name}</td>
            <td>${location.description}</td>
            <td>${location.category}</td>
            <td>${location.status}</td>
            <td>${location.latitude}</td>
            <td>${location.longitude}</td>
        </tr>
    `;

});

    })

    .catch(error => {

        console.error('Error:', error);

    });

</script>

</body>

</html>