<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Location</title>
</head>

<body>

    <h1>Edit Location</h1>

    <form id="editForm">

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

        <button type="submit">
            Update Location
        </button>

    </form>
<script>

const params =
    new URLSearchParams(
        window.location.search
    );

const reportId =
    params.get('id');

const API_URL =
    'http://localhost/community_visibility_system/codeigniter/public/api/locations';

console.log(
    'Report ID:',
    reportId
);

async function loadReport() {

    try {

        const response =
            await fetch(
                API_URL + '/' + reportId
            );

        const result =
            await response.json();

        console.log(
            'Report gikan sa API:',
            result
        );

        if (!response.ok) {

            throw new Error(
                'Unable to load report.'
            );

        }

        const report =
            result.data;


        document.getElementById('name').value =
            report.name;

        document.getElementById('description').value =
            report.description;

        document.getElementById('latitude').value =
            report.latitude;

        document.getElementById('longitude').value =
            report.longitude;

        document.getElementById('category').value =
            report.category;

        document.getElementById('status').value =
            report.status;


    } catch (error) {

        console.error(
            'Error loading report:',
            error
        );

    }

}

const editForm =
    document.getElementById('editForm');


editForm.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();


        const updatedData = {

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
            'Updated data:',
            updatedData
        );


        try {

            const response = await fetch(
                API_URL + '/' + reportId,
                {

                    method: 'PUT',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },

                    body:
                        JSON.stringify(updatedData)

                }
            );


            const result =
                await response.json();


            console.log(
                'Update response:',
                result
            );


            if (!response.ok) {

                throw new Error(
                    result.message ||
                    'Failed to update location.'
                );

            }


            alert(
                'Location successfully updated!'
            );


            window.location.href =
                'view_reports.php';


        } catch (error) {

            console.error(
                'Update error:',
                error
            );


            alert(
                'Error updating location: ' +
                error.message
            );

        }

    }
);
loadReport(); 
</script>

</body>
</html>