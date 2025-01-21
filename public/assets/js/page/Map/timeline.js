// Define map variables in a global scope
var map;

// Initialize map function
function initMap() {
    map = L.map('map').setView([11.339011556072483, 77.71273207611698], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.santhila.co/">Santhila</a> contributors'
    }).addTo(map);

    // shop_list();

    // setInterval(function () {
    //     shop_list();
    // }, 100 * 1000);
}

function shop_list(filter_date, filter_from_time, filter_to_time, filter_sales_executive_id, filter_sales_executive_status){

    var icon_red = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color:red;' class='marker-pin'></div><i class='fas fa-store' style='color:#000000; font-size:17px; top: 3px;'></i>",
        iconSize: [30, 42],
        iconAnchor: [15, 42]
    });

    var icon_green = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color:green;' class='marker-pin'></div><i class='fas fa-store' style='color:#000000; font-size:17px; top: 3px;'></i>",
        iconSize: [30, 42],
        iconAnchor: [15, 42]
    });

    var _token1=$("#_token1").val();
    var sendInfo={
        "_token":_token1,
        "action":"timeline_shop_list",
        "filter_date": filter_date,
        "filter_from_time": filter_from_time,
        "filter_to_time": filter_to_time,
        "filter_sales_executive_id": filter_sales_executive_id,
        "filter_sales_executive_status": filter_sales_executive_status
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(response) {
            let reverseGeocodePromises = [];
            response.forEach(store => {
                var latitude = parseFloat(store.latitude);
                var longitude = parseFloat(store.longitude);

                console.log(store.address);

                if(store.address === '' || store.address === '-'){
                reverseGeocode(latitude, longitude)
                    .then(placeName => {
                        globalPlaceName = placeName;
                    })
                    .catch(error => {
                        console.error('Error in reverseGeocode:', error);
                    });
                }

                // Get current hostname and port
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
                popupContent += 'Assigned Dealer&nbsp;&nbsp;: ' + store.dealer_name + ',<br/>';
                popupContent += 'Shop Type&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' + store.shops_type + ',<br/>';
                if (store.mobile_no && store.mobile_no.trim() !== '') {
                    popupContent += 'Mobile Number&nbsp;&nbsp;&nbsp;&nbsp;: ' + store.mobile_no + ',<br/>';
                }
                popupContent += 'Market Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' + store.area_name + ',<br/>';
                if (store.order_quantity !== 0 && store.shop_color === 'green') {
                    popupContent += 'Order Quantity&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' + store.order_quantity + ',<br/>';
                } else if (store.shop_color === 'green') {
                    popupContent += 'Order Quantity&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: No Order Found,<br/>';
                }

                if (store.address === '' || store.address === '-') {
                    reverseGeocodePromises.push(reverseGeocode(latitude, longitude)
                        .then(placeName => {
                            popupContent += 'Shop Location&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' + (globalPlaceName ? globalPlaceName : 'Waiting for location updating...') + '.<br/>';
                            popupContent += imageUrl;
                            popupContent += '</b>';

                            var marker = L.marker([latitude, longitude], {
                                icon: (store.shop_color === 'green' ? icon_green : icon_red)
                            }).addTo(map);

                            // Create red circle around the marker
                            var circle = L.circle([latitude, longitude], {
                                color: (store.shop_color === 'green' ? 'green' : 'red'),
                                fillColor: (store.shop_color === 'green' ? '#c1ffb2' : '#ffb2c1'),
                                fillOpacity: 0.5,
                                radius: 7  // 5 meters
                            }).addTo(map);

                            marker.bindPopup(popupContent);
                        })
                        .catch(error => {
                            console.error('Error in reverseGeocode:', error);
                        }));
                } else {
                    popupContent += 'Shop Location&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' + store.address + '.<br/>';
                    popupContent += imageUrl;
                    popupContent += '</b>';

                    var marker = L.marker([latitude, longitude], {
                        icon: (store.shop_color === 'green' ? icon_green : icon_red)
                    }).addTo(map);

                    // Create red circle around the marker
                    var circle = L.circle([latitude, longitude], {
                        color: (store.shop_color === 'green' ? 'green' : 'red'),
                        fillColor: (store.shop_color === 'green' ? '#c1ffb2' : '#ffb2c1'),
                        fillOpacity: 0.5,
                        radius: 7  // 5 meters
                    }).addTo(map);

                    marker.bindPopup(popupContent);
                }
            });
        },
        error: function() {
            console.log('error handing here');
        }
    });
}



function filter_timeline(filter_date, filter_from_time, filter_to_time, filter_sales_executive_id, filter_sales_executive_status) {
    var _token1 = $("#_token1").val();
    var sendInfo = {
        "_token": _token1,
        "action": "filter_timeline",
        "filter_date": filter_date,
        "filter_from_time": filter_from_time,
        "filter_to_time": filter_to_time,
        "filter_sales_executive_id": filter_sales_executive_id,
        "filter_sales_executive_status": filter_sales_executive_status
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            // Define custom icons
            var customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#0000ff;' class='marker-pin-sales-executive'></div><i class='fas fa-solid fa-user' style='color:#000000; font-size:17px; top: 3px;'></i>",
                iconSize: [30, 42],
                iconAnchor: [15, 42]
            });

            if (!map) {
                console.error('Map object is not initialized');
                return;
            }

            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker || layer instanceof L.Polyline) {
                    map.removeLayer(layer);
                }
            });

            var baseUrl = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/';

            if (data && data.length > 0) {
                var userLatitude = parseFloat(data[0].latitude);
                var userLongitude = parseFloat(data[0].langititude);

                let reverseGeocodePromises = [];
                let polylinePoints = [];

                data.forEach(function (sales_executive, index) {
                    var sales_ref_name = sales_executive.sales_ref_name;
                    var time = sales_executive.time;
                    var latitude = parseFloat(sales_executive.latitude);
                    var longitude = parseFloat(sales_executive.langititude);
                    var current_status = sales_executive.current_status;
                    var imgUrl = sales_executive.image_name !== '' ? baseUrl + 'storage/barang/' + sales_executive.image_name : baseUrl + 'storage/default/default_image.png';

                    var distance = getDistanceFromLatLonInKm(userLatitude, userLongitude, latitude, longitude);

                    var popupContent = '<b>';
                    if (current_status == 'Login') {
                        popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Login Point</i><br/><br/>';
                    } else if (current_status == 'Login Active') {
                        popupContent += '<i class="fas fa-map-marker-alt" style="color: blue;"> Login Active Point</i><br/><br/>';
                    } else if (current_status == 'Market Start' || current_status == 'Market End') {
                        popupContent += '<i class="fas fa-map-marker-alt" style="color: green;"> Market Point</i><br/><br/>';
                    } else if (current_status == 'Market Active') {
                        popupContent += '<i class="fas fa-map-marker-alt" style="color: #7700ff;"> Market Active Point</i><br/><br/>';
                    } else if (current_status == 'Logout') {
                        popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Logout Point</i><br/><br/>';
                    }
                    popupContent += '<span style="font-size: medium; color: blue;">' + sales_ref_name + '</span><br/>';
                    popupContent += 'Time: ' + time + '<br/>';
                    popupContent += 'Distance: ' + distance.toFixed(2) + ' km<br/>';

                    reverseGeocodePromises.push(reverseGeocode(latitude, longitude)
                        .then(placeName => {
                            popupContent += 'Location: ' + (placeName ? placeName : 'Waiting for location updating...') + '.<br/><br/>';

                            if (imgUrl !== 'no_image/default_image.png') {
                                popupContent += '<center><img src="' + imgUrl + '" alt="Image" style="max-width: 100px; max-height: 100px;"></center>';
                            }

                            var marker = L.marker([latitude, longitude], { icon: customIcon })
                                .addTo(map)
                                .bindPopup(popupContent);

                            polylinePoints.push([latitude, longitude]);
                        })
                        .catch(error => {
                            console.error('Error in reverseGeocode:', error);
                        }));
                });

                Promise.all(reverseGeocodePromises).then(() => {
                    var polyline = L.polyline(polylinePoints, { color: 'blue' }).addTo(map);
                }).catch(error => {
                    console.error('Error creating markers and popups:', error);
                });
                map.setView([userLatitude, userLongitude], 13);
                shop_list(filter_date, filter_from_time, filter_to_time, filter_sales_executive_id, filter_sales_executive_status);
            } else {
                alert("No Sales Executive Location Found");
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
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

// function reverseGeocode(lat, lng) {
//     return fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error('Network response was not ok');
//             }
//             return response.json();
//         })
//         .then(data => {
//             var placeName = data.display_name;
//             globalPlaceName = placeName;
//             return placeName;
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             globalPlaceName = "Location Not Found";
//             return "Location Not Found";
//         });
// }

// function reverseGeocode(lat, lng) {
//   return fetch(`https://us1.locationiq.com/v1/reverse.php?key=pk.9a2add07f899576f4418807905ec7dca&format=json&lat=${lat}&lon=${lng}`)
//       .then(response => {
//           if (!response.ok) {
//               throw new Error('Network response was not ok');
//           }
//           return response.json();
//       })
//       .then(data => {
//           var placeName = data.display_name;
//           globalPlaceName = placeName;
//           return placeName;
//       })
//       .catch(error => {
//           console.error('Error:', error);
//           globalPlaceName = "Location Not Found";
//           return "Location Not Found";
//       });
// }
