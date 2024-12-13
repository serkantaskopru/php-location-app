<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasyon Uygulaması</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"/>

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
        <div id="side-main">
            <ul class="nav nav-tabs" id="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="location-tab" data-bs-toggle="tab" data-bs-target="#location"
                            type="button" role="tab" aria-controls="location" aria-selected="true">Lokasyon
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="route-tab" data-bs-toggle="tab" data-bs-target="#route" type="button"
                            role="tab" aria-controls="profile" aria-selected="false">Rota
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="tab-content">
                <div class="tab-pane fade show active" id="location" role="tabpanel" aria-labelledby="location-tab">
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
                        <input type="text" class="form-control" id="color" name="color" required>
                    </div>

                    <div class="mt-2">
                        <button type="button" onclick="createNewLocation()" class="btn btn-primary">Lokasyon Ekle
                        </button>
                        <button type="button" onclick="fetchLocations()" class="btn btn-success">Lokasyonları Getir
                        </button>
                    </div>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered" id="locations-table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Konum</th>
                                <th scope="col">Enlem</th>
                                <th scope="col">Boylam</th>
                                <th scope="col">İşaretçi</th>
                                <th scope="col">İşlem</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="route" role="tabpanel" aria-labelledby="route-tab">

                </div>
            </div>
        </div>

        <div id="side-detail" style="display: none">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Lokasyon Yönetimi</h4>
                <button class="btn btn-sm btn-primary" onclick="closeLocationEditPage()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="form-group">
                <label for="selectedLocationName">Lokasyon Adı</label>
                <input type="text" class="form-control" id="selectedLocationName" name="name">
            </div>
            <div class="form-group">
                <label for="selectedLocationLatitude">Enlem</label>
                <input type="number" class="form-control" id="selectedLocationLatitude" name="latitude" step="any">
            </div>
            <div class="form-group">
                <label for="selectedLocationLongitude">Boylam</label>
                <input type="number" class="form-control" id="selectedLocationLongitude" name="longitude" step="any">
            </div>
            <div class="form-group">
                <label for="selectedLocationColor">Marker Rengi (Hex)</label>
                <input type="text" class="form-control" id="selectedLocationColor" name="color">
            </div>

            <div class="mt-2">
                <button type="button" class="btn btn-primary" onclick="updateSelectedLocation()">Lokasyonu Güncelle</button>
            </div>
        </div>
    </div>

    <!-- Right Panel (Map) -->
    <div class="content col-md-9">
        <div id="map"></div>
    </div>
</div>

<!-- Bootstrap JS, Popper.js ve jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>

<!-- Axios JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.8/axios.min.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- BlockUI JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>

<script>
    let markers = [];
    let selectedLocationID = 0;
    const apiURL = 'http://localhost:8012';
    const locationsTable = $('#locations-table');
    const sidebarMainContent = $('#side-main');
    const sidebarDetailContent = $('#side-detail');

    // Leaflet map start
    let map = L.map('map').setView([0, 0], 11);

    // OpenStreetMap Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    axios.defaults.headers.common['Accept'] = 'application/json';

    axios.interceptors.request.use(
        function (config) {
            blockUI();
            return config;
        },
        function (error) {
            unblockUI();
            return Promise.reject(error);
        }
    );

    axios.interceptors.response.use(
        function (response) {
            unblockUI();
            return response;
        },
        function (error) {
            unblockUI();
            return Promise.reject(error);
        }
    );

    function blockUI() {
        $.blockUI({
            message: '<h6 class="mb-0">Lütfen bekleyin...</h6>',
            css: {
                border: 'none',
                padding: 16,
                backgroundColor: '#000',
                borderRadius: '10px',
                opacity: 0.5,
                color: '#fff'
            }
        });
    }

    function unblockUI() {
        $.unblockUI();
    }

    function _clearLocationsTable() {
        locationsTable.find('tbody').remove();
    }

    function _appendBodyToLocationsTable() {
        locationsTable.append('<tbody></tbody>');
    }

    function _insertRowToLocationsTable(id, name, lat, long, marker) {
        let row = `<tr>
                    <td>${id}</td>
                    <td>${name}</td>
                    <td><small>${lat}</small></td>
                    <td><small>${long}</small></td>
                    <td><small>${marker}</small></td>
                    <td class="d-flex">
                    <button type="button" class="btn btn-sm btn-primary" onclick="selectLocationForEdit(${id})"><i class="bi bi-pen-fill"></i></button>
                    <button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteLocation(${id})"><i class="bi bi-trash3-fill"></i></button>
                    </td>
                    </tr>`
        ;
        locationsTable.find('tbody').append(row);
    }

    function _appendMarkerToMap(lat, long, color) {

        const icon = L.divIcon({
            className: "my-custom-pin",
            iconAnchor: [0, 0],
            labelAnchor: [-6, 0],
            popupAnchor: [0, -36],
            html: `<span style="background-color: ${color};
          width: 1rem;
          height: 1rem;
          display: block;
          left: -0.5rem;
          top: -0.5rem;
          position: relative;
          border-radius: 1rem 1rem 0;
          transform: rotate(45deg);
          border: 1px solid #FFFFFF" />`
        })

        const marker = L.marker([lat, long], {icon: icon}).addTo(map);
        markers.push(marker);
    }

    function _removeMarkers() {
        try {
            markers.forEach(marker => {
                map.removeLayer(marker);
            })
        } catch (exception) {
            console.error(exception);
        }
    }

    function fetchLocations() {
        _clearLocationsTable();
        _appendBodyToLocationsTable();
        _removeMarkers();

        axios.get(`${apiURL}/api/v1/locations`)
            .then(function (response) {
                console.log('Data:', response.data);

                if (response.data.status === true) {
                    map.invalidateSize();

                    const data = response.data.data;

                    data.forEach(element => {
                        _insertRowToLocationsTable(element.id, element.name, element.latitude, element.longitude, element.color);
                        _appendMarkerToMap(element.latitude, element.longitude, element.color);
                    });

                    const latitudes = data.map(el => el.latitude);
                    const longitudes = data.map(el => el.longitude);

                    const southWest = [Math.min(...latitudes), Math.min(...longitudes)];
                    const northEast = [Math.max(...latitudes), Math.max(...longitudes)];

                    map.fitBounds([southWest, northEast], {padding: [20, 20]});
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
            });

    }

    function _clearStoreInputs() {
        $('input#name').val('');
        $('input#latitude').val('');
        $('input#longitude').val('');
        $('input#color').val('');
    }

    function _clearUpdateInputs() {
        $('input#selectedLocationName').val('');
        $('input#selectedLocationLatitude').val('');
        $('input#selectedLocationLongitude').val('');
        $('input#selectedLocationColor').val('');
    }

    function createNewLocation() {
        const _name = $('input#name').val();
        const _latitude = $('input#latitude').val();
        const _longitude = $('input#longitude').val();
        const _color = $('input#color').val();

        axios.post(`${apiURL}/api/v1/locations`, {
            name: _name,
            latitude: _latitude,
            longitude: _longitude,
            color: _color,
        })
            .then(function (response) {
                console.log('Data:', response.data);
                _clearStoreInputs();
                fetchLocations();
            })
            .catch(function (error) {
                console.error('Error:', error);
            });
    }

    function updateSelectedLocation() {
        const _name = $('input#selectedLocationName').val();
        const _latitude = $('input#selectedLocationLatitude').val();
        const _longitude = $('input#selectedLocationLongitude').val();
        const _color = $('input#selectedLocationColor').val();

        axios.put(`${apiURL}/api/v1/locations/${selectedLocationID}`, {
            name: _name,
            latitude: _latitude,
            longitude: _longitude,
            color: _color,
        })
            .then(function (response) {
                console.log('Data:', response.data);
                _clearUpdateInputs();
                closeLocationEditPage();
                fetchLocations();
            })
            .catch(function (error) {
                console.error('Error:', error);
            });
    }

    function deleteLocation(id) {
        axios.delete(`${apiURL}/api/v1/locations/${id}`)
            .then(function (response) {
                console.log('Data:', response.data);

                if (response.data.status === true) {
                    fetchLocations();
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
            });
    }

    function closeLocationEditPage(){
        sidebarDetailContent.hide();
        sidebarMainContent.show();
    }

    function selectLocationForEdit(id){
        let location = null;
        selectedLocationID = id;
        sidebarMainContent.hide();
        sidebarDetailContent.show();

        axios.get(`${apiURL}/api/v1/locations/${id}`)
            .then(function (response) {
                console.log('Data:', response.data);

                if (response.data.status === true) {
                    location = response.data.data;

                    $('input#selectedLocationName').val(location.name);
                    $('input#selectedLocationLatitude').val(location.latitude);
                    $('input#selectedLocationLongitude').val(location.longitude);
                    $('input#selectedLocationColor').val(location.color);
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
            });
    }

    $(document).ready(function () {
        fetchLocations();
    });

</script>
</body>
</html>
