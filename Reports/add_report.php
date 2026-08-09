<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Location</title>
</head>

<body>

    <h1>Add Location</h1>

    <form id="reportForm">

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

const API_URL =
    'http://localhost/community_visibility_system/codeigniter/public/api/locations';

const reportForm =
    document.getElementById('reportForm');


reportForm.addEventListener('submit', async function(event) {

    event.preventDefault();

    const locationData = {

        name:
            document.getElementById('name').value,

        description:
            document.getElementById('description').value,

        latitude:
            document.getElementById('latitude').value,

        longitude:
            document.getElementById('longitude').value,

        category:
            document.getElementById('category').value,

        status:
            document.getElementById('status').value

    };


    console.log(
        'Data nga ipadala:',
        locationData
    );


    try {

        const response = await fetch(API_URL, {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },

            body: JSON.stringify(locationData)

        });


        const result =
            await response.json();


        console.log(
            'Response gikan sa API:',
            result
        );


        if (!response.ok) {

            throw new Error(
                result.message ||
                'Failed to add location.'
            );

        }


        alert(
            'Location successfully added!'
        );


        reportForm.reset();


    } catch (error) {

        console.error(
            'Error:',
            error
        );


        alert(
            'Error adding location: ' +
            error.message
        );

    }

});

</script>

</body>
</html>