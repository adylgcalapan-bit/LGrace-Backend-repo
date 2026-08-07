<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Location</title>
</head>

<body>

    <h1>Add Location</h1>

    <form id="locationForm">

        <label>Name:</label><br>
        <input type="text" id="name" required>
        <br><br>

        <label>Description:</label><br>
        <textarea id="description" required></textarea>
        <br><br>

        <label>Latitude:</label><br>
        <input type="number" id="latitude" step="any" required>
        <br><br>

        <label>Longitude:</label><br>
        <input type="number" id="longitude" step="any" required>
        <br><br>

        <label>Category:</label><br>
        <input type="text" id="category" required>
        <br><br>

        <label>Status:</label><br>
        <select id="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <br><br>

        <button type="submit">Add Location</button>

    </form>
    <script>
       document.getElementById('locationForm').addEventListener('submit', function(event) {

    event.preventDefault();

});
  const locationData = {

    name: document.getElementById('name').value,

    description: document.getElementById('description').value,

    latitude: document.getElementById('latitude').value,

    longitude: document.getElementById('longitude').value,

    category: document.getElementById('category').value,

    status: document.getElementById('status').value

};
  
fetch('http://localhost/community_visibility_system/codeigniter/public/api/locations', {

    method: 'POST',

    headers: {
        'Content-Type': 'application/json'
    },

    body: JSON.stringify(locationData)

});
   </script>
</body>

</html>