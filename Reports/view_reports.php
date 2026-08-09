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
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="reportTableBody">
        </tbody>

    </table>

<script>

const API_URL =
    'http://localhost/community_visibility_system/codeigniter/public/api/locations';

const reportTableBody =
    document.getElementById('reportTableBody');


async function loadReports() {

    try {

        const response = await fetch(API_URL);

        const result = await response.json();

        console.log(
            'Reports gikan sa API:',
            result
        );


        if (!response.ok) {
            throw new Error(
                'Failed to load reports.'
            );
        }


        const reports = result.data || [];

        reportTableBody.innerHTML = '';


        if (reports.length === 0) {

            reportTableBody.innerHTML = `
                <tr>
                    <td colspan="7">
                        No reports found.
                    </td>
                </tr>
            `;

            return;
        }


        reports.forEach(function(report) {

            const row =
                document.createElement('tr');


            row.innerHTML = `
                <td>${report.id}</td>
                <td>${report.name}</td>
                <td>${report.description}</td>
                <td>${report.category}</td>
                <td>${report.status}</td>
                <td>${report.latitude}</td>
                <td>${report.longitude}</td>
                
14<td>
    <a href="edit_report.php?id=${report.id}">
        Edit
    </a>

    |

    <a href="delete_report.php?id=${report.id}">
        Delete
    </a>
</td>
`;


            reportTableBody.appendChild(row);

        });


    } catch (error) {

        console.error(
            'Error loading reports:',
            error
        );


        reportTableBody.innerHTML = `
            <tr>
                <td colspan="7">
                    Error loading reports.
                </td>
            </tr>
        `;

    }

}


loadReports();

</script>

</body>
</html>