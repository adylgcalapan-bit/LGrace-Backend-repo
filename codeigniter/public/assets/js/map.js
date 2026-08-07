document.addEventListener('DOMContentLoaded', () => {
    const map = L.map('map').setView([14.6789, 121.117], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const sampleReports = [
        { id: 'R-1001', category: 'Waste Management', status: 'Pending', location: 'Purok 1', lat: 14.6789, lng: 121.117 },
        { id: 'R-1002', category: 'Infrastructure', status: 'In Progress', location: 'Purok 3', lat: 14.6850, lng: 121.126 },
        { id: 'R-1003', category: 'Public Safety', status: 'Resolved', location: 'Purok 5', lat: 14.6720, lng: 121.110 },
        { id: 'R-1004', category: 'Utilities', status: 'Rejected', location: 'Purok 2', lat: 14.6900, lng: 121.120 }
    ];

    const statusColors = {
        Pending: '#ff9800',
        'In Progress': '#2196f3',
        Resolved: '#28a745',
        Rejected: '#dc3545'
    };

    sampleReports.forEach(report => {
        const marker = L.circleMarker([report.lat, report.lng], {
            radius: 8,
            color: statusColors[report.status],
            fillColor: statusColors[report.status],
            fillOpacity: 0.9
        }).addTo(map);

        marker.bindPopup(`
            <strong>${report.id}</strong><br>
            Category: ${report.category}<br>
            Status: ${report.status}<br>
            Location: ${report.location}<br>
            <button class="btn btn-sm btn-success mt-2">View Report</button>
        `);
    });

    document.getElementById('searchLocation')?.addEventListener('input', (e) => {
        const value = e.target.value.toLowerCase();
        console.log('Search location:', value);
    });

    document.getElementById('categoryFilter')?.addEventListener('change', () => {
        console.log('Category filter changed');
    });

    document.getElementById('statusFilter')?.addEventListener('change', () => {
        console.log('Status filter changed');
    });
});
