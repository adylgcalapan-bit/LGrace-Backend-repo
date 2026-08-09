<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Location</title>
</head>

<body>

    <h1>Delete Location</h1>

    <p>
        Are you sure you want to delete this location?
    </p>

    <button type="button" id="deleteButton">
        Yes, Delete
    </button>

    <a href="view_reports.php">
        Cancel
    </a>
<script>

const params =
    new URLSearchParams(
        window.location.search
    );

const reportId =
    params.get('id');

const API_URL =
    'http://localhost/community_visibility_system/codeigniter/public/api/locations';

const deleteButton =
    document.getElementById('deleteButton');


console.log(
    'Report ID nga i-delete:',
    reportId
);


deleteButton.addEventListener(
    'click',
    async function() {

        const confirmed =
            confirm(
                'Are you sure you want to delete this location?'
            );

        if (!confirmed) {
            return;
        }

        try {

            const response =
                await fetch(
                    API_URL + '/' + reportId,
                    {
                        method: 'DELETE',

                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

            const result =
                await response.json();

            console.log(
                'Delete response:',
                result
            );

            if (!response.ok) {

                throw new Error(
                    result.message ||
                    'Failed to delete location.'
                );

            }

            alert(
                'Location successfully deleted!'
            );

            window.location.href =
                'view_reports.php';

        } catch (error) {

            console.error(
                'Delete error:',
                error
            );

            alert(
                'Error deleting location: ' +
                error.message
            );

        }

    }
);

</script>

</body>
</html>