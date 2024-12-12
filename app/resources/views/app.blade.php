<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasyon Uygulaması</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    <style>
        #map {
            height: 100%;
            width: 100%;
        }

        .sidebar {
            height: 100vh;
            padding-top: 20px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .container-fluid {
            display: flex;
            height: 100vh;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Left Panel (Management) -->
    <div class="sidebar col-md-3 bg-light">
        <h4>Lokasyon Yönetimi</h4>
        <div class="form-group">
            <label for="name">Lokasyon Adı</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="latitude">Enlem</label>
            <input type="number" class="form-control" id="latitude" name="latitude" step="any" required>
        </div>
        <div class="form-group">
            <label for="longitude">Boylam</label>
            <input type="number" class="form-control" id="longitude" name="longitude" step="any" required>
        </div>
        <div class="form-group">
            <label for="color">Marker Rengi (Hex)</label>
            <input type="text" class="form-control" id="color" name="color" value="#3388ff" required>
        </div>
        <button type="button" class="btn btn-primary">Lokasyon Ekle</button>
    </div>

    <!-- Right Panel (Map) -->
    <div class="content col-md-9">
        <div id="map"></div>
    </div>
</div>

<!-- Bootstrap JS, Popper.js ve jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Axios JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.8/axios.min.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Leaflet map start
    var map = L.map('map').setView([0, 0], 11);

    // OpenStreetMap Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
</script>
<script>

</script>
</body>
</html>
