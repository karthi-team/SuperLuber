var map;

function initMap() {
    map = L.map('map').setView([11.339011556072483, 77.71273207611698], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.santhila.co/">Santhila</a> contributors'
    }).addTo(map);
}

function filter_market_view(manager_id, sales_rep_id, dealer_id, market_id, shop_type_id, shop_id) {

    var routeColors = ['blue', 'green', 'red', 'purple', 'orange', 'cyan', 'magenta'];

    clearMapLayers();

    var icon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color:#c30b82;' class='marker-pin'></div><i class='fas fa-store' style='color:#000000; font-size:17px; top: 3px;'></i>",
        iconSize: [30, 42],
        iconAnchor: [15, 42]
    });

    var _token1 = $("#_token1").val();

    var sendInfo = {
        "_token": _token1,
        "action": "filter_market_view",
        "manager_id": manager_id,
        "sales_rep_id": sales_rep_id,
        "dealer_id": dealer_id,
        "market_id": market_id,
        "shop_type_id": shop_type_id,
        "shop_id": shop_id,
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(response) {
            let minLat = Infinity, maxLat = -Infinity, minLng = Infinity, maxLng = -Infinity;
            let boundsMap = new Map();
            let routeIndex = 0;

            response.forEach(item => {
                let waypoints = [];
                item.shop_list.forEach(store => {
                    if (store.latitude && store.longitude) {
                        var latitude = parseFloat(store.latitude);
                        var longitude = parseFloat(store.longitude);

                        if (latitude < minLat) minLat = latitude;
                        if (latitude > maxLat) maxLat = latitude;
                        if (longitude < minLng) minLng = longitude;
                        if (longitude > maxLng) maxLng = longitude;

                        var baseUrl = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/';
                        var imageUrl = '';

                        var imageArray = parseImageName(store.image_name);
                        if (Array.isArray(imageArray) && imageArray.length > 0) {
                            var firstImage = imageArray[0];
                            imageUrl = '<center><img id="image_preview" src="' + baseUrl + 'storage/shop_img/' + firstImage + '" style="max-width: 100px; max-height: 100px;" alt="Image Preview"></center>';
                        } else {
                            imageUrl = '<center><img id="image_preview" src="' + baseUrl + 'storage/default/default_image.png" style="max-width: 100px; max-height: 100px;" alt="Image Preview"></center>';
                        }

                        var popupContent = '<b>';
                        popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Shop Point</i><br/><br/>';
                        popupContent += '<span style="font-size: medium; color: #c30b82;">' + store.shop_name + '</span><br/>';
                        popupContent += 'Assigned Dealer: ' + store.dealer_name + '<br/>';
                        popupContent += 'Shop Type: ' + store.shops_type + '<br/>';
                        if (store.mobile_no && store.mobile_no.trim() !== '') {
                            popupContent += 'Mobile No: ' + store.mobile_no + '<br/>';
                        }
                        popupContent += 'Market Name: ' + store.area_name + '<br/>';
                        if (store.address === '' || store.address === '-') {
                            popupContent += 'Location: ' + (globalPlaceName ? globalPlaceName : 'No Location Found!') + '<br/>';
                        } else {
                            popupContent += 'Location: ' + store.address + '.<br/>';
                        }
                        popupContent += imageUrl;
                        popupContent += '</b>';

                        var marker = L.marker([latitude, longitude], {
                            icon: icon
                        }).addTo(map);

                        var circle = L.circle([latitude, longitude], {
                            color: 'red',
                            fillColor: '#ffb2c1',
                            fillOpacity: 0.5,
                            radius: 7  // 7 meters
                        }).addTo(map);

                        marker.bindPopup(popupContent);

                        waypoints.push(L.latLng(latitude, longitude));
                    }
                });

                if (waypoints.length > 1) {
                    var routeColor = routeColors[routeIndex % routeColors.length];
                    routeIndex++;

                    var routeControl = L.Routing.control({
                        waypoints: waypoints,
                        routeWhileDragging: true,
                        createMarker: function() { return null; },
                        lineOptions: {
                            styles: [{ color: routeColor, weight: 4 }],
                            clickable: false
                        }
                    }).addTo(map);

                    var routeBounds = L.latLngBounds(waypoints);
                    var popupContent = '<b>';
                    popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Market View</i><br/><br/>';
                    popupContent += '<span style="font-size: medium; color: #c30b82;">' + item.market_name + '</span><br/>';
                    popupContent += 'Assigned Sales Executive: ' + item.sales_executive_name + '<br/>';
                    popupContent += 'Assigned Dealer: ' + item.dealer_name + '<br/>';
                    popupContent += '</b>';

                    boundsMap.set(routeBounds, popupContent);
                } else if (waypoints.length === 1) {
                    var routeColor = routeColors[routeIndex % routeColors.length];
                    routeIndex++;
                    var circle = L.circle(waypoints[0], {
                        color: routeColor,
                        radius: 50  // 50 meters
                    }).addTo(map);

                    var popupContent = '<b>';
                    popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Market View</i><br/><br/>';
                    popupContent += '<span style="font-size: medium; color: #c30b82;">' + item.market_name + '</span><br/>';
                    popupContent += 'Assigned Sales Executive: ' + item.sales_executive_name + '<br/>';
                    popupContent += 'Assigned Dealer: ' + item.dealer_name + '<br/>';
                    popupContent += '</b>';

                    circle.bindPopup(popupContent);
                }
            });

            if (minLat !== Infinity && maxLat !== -Infinity && minLng !== Infinity && maxLng !== -Infinity) {
                var overallBounds = L.latLngBounds(
                    [minLat, minLng],
                    [maxLat, maxLng]
                );
                map.fitBounds(overallBounds);
            }

            map.on('click', function(e) {
                var latlng = e.latlng;
                boundsMap.forEach((popupContent, bounds) => {
                    if (bounds.contains(latlng)) {
                        L.popup()
                            .setLatLng(latlng)
                            .setContent(popupContent)
                            .openOn(map);
                    }
                });
            });
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

function clearMapLayers() {
    map.eachLayer(function (layer) {
        if (layer instanceof L.Marker || layer instanceof L.Polyline || layer instanceof L.Circle) {
            map.removeLayer(layer);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initMap();
    get_sales_executive();
});

function parseImageName(imageName) {
    try {
        return JSON.parse(imageName);
    } catch (error) {
        console.error('Error parsing image_name:', error);
        return [];
    }
}

function get_sales_executive() {
    var _token1=$("#_token1").val();
    var filter_date = $("#filter_date").val();
    var sendInfo={"_token":_token1, "action":"get_sales_executive", "filter_date":filter_date};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(data){
            $('#filter_sales_executive_id').empty();
            $('#filter_sales_executive_id').append('<option value="">Select</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#filter_sales_executive_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
            }
        },
        error: function() {
            console.log('error handling here');
        }
    });
}

function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    var R = 6371;
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c;
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI/180);
}

let globalPlaceName = ''; // Declare global variable to hold placeName

function reverseGeocode(lat, lng) {
    const fetchPromise = fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            var placeName = data.display_name;
            globalPlaceName = placeName;
            return placeName;
        });

    const timeoutPromise = new Promise((resolve, reject) => {
        setTimeout(() => {
            reject(new Error('Timeout exceeded'));
        }, 10000); // 10 seconds timeout for the catch block
    });

    return Promise.race([fetchPromise, timeoutPromise])
        .catch(error => {
            console.error('Error:', error);
            globalPlaceName = "Location Not Found";
            return "Location Not Found";
        });
}

function get_sales_ref() {
    var manager_id = $("#manager_id").val();
    var sendInfo={"action":"get_sales_ref","manager_id":manager_id};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
            $('#sales_rep_id').empty();
            $('#sales_rep_id').append('<option value="">Select Sales Executive</option>');
            for(let i1=0;i1 < data.length;i1++){
                $('#sales_rep_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
            }
        }
    });
}

function get_dealer_name() {
    var sales_ref_id = $("#sales_rep_id").val();
    var sendInfo={"action":"get_dealer_name","sales_rep_id":sales_ref_id};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
            $('#dealer_id').empty();
            $('#dealer_id').append('<option value="">Select Dealer Name</option>');
            for(let i1=0;i1 < data.length;i1++){
                $('#dealer_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
            }
        }
    });
}

function get_market_name() {
    var dealer_id = $("#dealer_id").val();
    var sendInfo = {action: "get_market_name", dealer_id: dealer_id};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#market_id').empty();
            $('#market_id').append('<option value="">Select Market Name</option>');
            for (let i1 = 0; i1 < data.area_names.length; i1++) {
                $('#market_id').append('<option value="' + data.area_names[i1]['id'] + '">' + data.area_names[i1]['area_name'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching data');
        }
    });
}

function get_shop_type() {
    $("#shop_type_id").empty();
    var sendInfo = {action: "get_shop_type"};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#shop_type_id').empty();
            $('#shop_type_id').append('<option value="">Select Shop Type</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#shop_type_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['shops_type'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching here');
        }
    });
}

function get_shop_name() {
    var market_id = $("#market_id").val();
    var shop_type_id = $("#shop_type_id").val();
    $("#shop_id").empty();
    var sendInfo = {action: "get_shop_name", market_id: market_id, shop_type_id: shop_type_id};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#shop_id').empty();
            $('#shop_id').append('<option value="">Select Shop Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#shop_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['shop_name'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching here');
        }
    });
}
