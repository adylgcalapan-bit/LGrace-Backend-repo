document.addEventListener('DOMContentLoaded', () => {

    // Initialize map
    const map = L.map('map').setView([14.6789, 121.117], 13);

    // OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);


    // Marker colors based on report status
    const statusColors = {
        'Pending': '#ff9800',
        'In Progress': '#2196f3',
        'Resolved': '#28a745',
        'Rejected': '#dc3545'
    };


    // Get actual reports from CodeIgniter backend
    fetch('/api/locations')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch reports.');
            }

            return response.json();
        })

        .then(result => {

            console.log('Reports from backend:', result);

            if (!result.success) {
                console.error('Backend returned an error.');
                return;
            }

            const reports = result.data;

            if (reports.length === 0) {
                console.log('No reports found in database.');
                return;
            }


            const markers = [];


            reports.forEach(report => {

                const latitude = parseFloat(report.latitude);
                const longitude = parseFloat(report.longitude);


                // Skip reports without valid coordinates
                if (
                    isNaN(latitude) ||
                    isNaN(longitude)
                ) {
                    console.warn(
                        'Report has no valid coordinates:',
                        report
                    );

                    return;
                }


                const color =
                    statusColors[report.status] || '#6c757d';


                const marker = L.circleMarker(
                    [latitude, longitude],
                    {
                        radius: 8,
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.9
                    }
                ).addTo(map);


                marker.bindPopup(`
                    <strong>Report #${report.report_id}</strong><br>
                    Title: ${report.title}<br>
                    Status: ${report.status}<br>
                    Latitude: ${report.latitude}<br>
                    Longitude: ${report.longitude}
                `);


                markers.push(marker);
            });


            // Automatically move map to actual report markers
            if (markers.length > 0) {

                const markerGroup =
                    L.featureGroup(markers);

                map.fitBounds(
                    markerGroup.getBounds().pad(0.2)
                );
            }

        })

        .catch(error => {

            console.error(
                'Error loading reports:',
                error
            );

        });


    // Existing search field
    document
        .getElementById('searchLocation')
        ?.addEventListener('input', (e) => {

            const value =
                e.target.value.toLowerCase();

            console.log(
                'Search location:',
                value
            );

        });


    // Existing category filter
    document
        .getElementById('categoryFilter')
        ?.addEventListener('change', () => {

            console.log(
                'Category filter changed'
            );

        });


    // Existing status filter
    document
        .getElementById('statusFilter')
        ?.addEventListener('change', () => {

            console.log(
                'Status filter changed'
            );

        });

});