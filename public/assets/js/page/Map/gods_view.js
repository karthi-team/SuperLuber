//Map
var map;
var allMarkers = [];
var displayedMarkers = [];
var allTherapists = [];

function initMap() {
    var defaultCenter = { lat: 11.339011556072483, lng: 77.71273207611698 };
    var defaultZoom = 12;

    map = L.map('map').setView(defaultCenter, defaultZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.santhila.co/">Santhila</a> contributors'
    }).addTo(map);

    // Initial data load
    gods_view_shop_list('', '', '', '', '');

    filter_godsview('', '', '', '', '');

    // setInterval(function () {
    //     gods_view_shop_list();
    // }, 100 * 1000);

    setInterval(function () {
        gods_view_markers();
    }, 5 * 1000); // 5 seconds
}

function gods_view_shop_list(manager_id,sales_rep_id, dealer_id, market_id, shop_id) {

    clearMapLayers();

    var _token1=$("#_token1").val();

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

    var sendInfo={"_token":_token1, "action":"gods_view_shop_list", "manager_id":manager_id, "sales_rep_id":sales_rep_id, "dealer_id":dealer_id, "market_id":market_id, "shop_id":shop_id};

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

function filter_godsview(manager_id,sales_rep_id, dealer_id, market_id, shop_id) {

    gods_view_shop_list(manager_id,sales_rep_id, dealer_id, market_id, shop_id);

    if(manager_id != '' || sales_rep_id != ''){
        var _token1=$("#_token1").val();
        var sendInfo={"_token":_token1, "action":"gods_view_dealer"};
        $.ajax({
            type: "GET",
            url: $("#CUR_ACTION").val(),
            data: sendInfo,
            success: function(response){

                displayedMarkers.forEach(function (marker) {
                    map.removeLayer(marker);
                });
                // Array to store all promises for reverse geocoding
                let reverseGeocodePromises = [];

                // Array to store all markers
                let allMarkers = [];

                response.forEach(function (sales_executive) {
                    var lat = parseFloat(sales_executive.latitude);
                    var lng = parseFloat(sales_executive.langititude);

                    // Get current hostname and port
                    var baseUrl = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/';

                    var imageUrl = sales_executive.image_name !== '' ? baseUrl + 'storage/barang/' + sales_executive.image_name : baseUrl + 'storage/default/default_image.png';
                    var time = convertTimeTo12HourFormat(sales_executive.time);

                    // Start building the popup content synchronously
                    var popupContent = '<b>';
                    popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Live Point</i><br/><br/>';
                    popupContent += '<span style="font-size: medium; color: blue;">' + sales_executive.sales_ref_name + '</span><br/>';
                    popupContent += 'Mobile No: ' + sales_executive.mobile_no + ',<br/>';
                    popupContent += 'Time: ' + time + ',<br/>';
                    if (sales_executive.current_status == 'Login') {
                        popupContent += 'Status: <span style="color: red;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Login Active') {
                        popupContent += 'Status: <span style="color: blue;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Market Start' || sales_executive.current_status == 'Market End') {
                        popupContent += 'Status: <span style="color: green;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Market Active') {
                        popupContent += 'Status: <span style="color: #7700ff;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Logout') {
                        popupContent += 'Status: <span style="color: red;">' + sales_executive.current_status + '</span>,<br/>';
                    }
                    popupContent += 'Live Location: Waiting for location updating...<br/>';
                    popupContent += '<center><img id="image_preview" src="' + imageUrl + '" style="max-width: 100px; max-height: 100px;" alt="Image Preview"></center>';

                    // Push reverse geocode promise to the array
                    reverseGeocodePromises.push(reverseGeocode(lat, lng)
                        .then(placeName => {
                            popupContent = popupContent.replace('Live Location: Waiting for location updating...', 'Live Location: ' + (placeName ? placeName : 'Waiting for location updating...'));

                            var icon = L.divIcon({
                                className: 'custom-div-icon',
                                html: "<div style='background-color:#0000ff;' class='marker-pin-sales-executive'></div><i class='fas fa-solid fa-user' style='color:#000000; font-size:17px; top: 3px;'></i>",
                                iconSize: [30, 42],
                                iconAnchor: [15, 42]
                            });

                            var marker = L.marker([lat, lng], {
                                SalesExecutiveId: sales_executive.id,
                                icon: icon
                            });

                            marker.bindPopup(popupContent);
                            allMarkers.push(marker); // Add marker to allMarkers array
                            return marker;
                        })
                        .catch(error => {
                            console.error('Error in reverseGeocode:', error);
                        }));
                });

                // Wait for all reverse geocode promises to resolve
                Promise.all(reverseGeocodePromises)
                    .then(() => {
                        // All markers and popups are now created

                        var selectedSalesExecutiveId = document.getElementById('sales_rep_id').value;

                        // Filter displayedMarkers based on selectedSalesExecutiveId
                        var displayedMarkers = allMarkers.filter(function (marker) {
                            var SalesExecutiveId = marker.options.SalesExecutiveId;
                            return selectedSalesExecutiveId === "" || SalesExecutiveId == selectedSalesExecutiveId;
                        });

                        // Remove all previously displayed markers from the map
                        displayedMarkers.forEach(function (marker) {
                            map.removeLayer(marker);
                        });

                        // Add filtered markers to the map
                        displayedMarkers.forEach(function (marker) {
                            marker.addTo(map);
                        });

                        // Create a feature group from displayed markers
                        var group = new L.featureGroup(displayedMarkers);

                        // Fit the map bounds to the feature group
                        try {
                            map.fitBounds(group.getBounds());
                        } catch (error) {
                            console.log('No Sales Executive Data Found');
                        }
                    })
                    .catch(error => {
                        console.error('Error creating markers and popups:', error);
                    });
            },
            error: function() {
                console.log('error handing here');
            }
        });
    } else {
        var _token1=$("#_token1").val();
        var sendInfo={"_token":_token1, "action":"gods_view_dealer"};
        $.ajax({
            type: "GET",
            url: $("#CUR_ACTION").val(),
            data: sendInfo,
            success: function(response){

                displayedMarkers.forEach(function (marker) {
                    map.removeLayer(marker);
                });
                // Array to store all promises for reverse geocoding
                let reverseGeocodePromises = [];

                // Array to store all markers
                let allMarkers = [];

                response.forEach(function (sales_executive) {
                    var lat = parseFloat(sales_executive.latitude);
                    var lng = parseFloat(sales_executive.langititude);

                    // Get current hostname and port
                    var baseUrl = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/';

                    var imageUrl = sales_executive.image_name !== '' ? baseUrl + 'storage/barang/' + sales_executive.image_name : baseUrl + 'storage/default/default_image.png';
                    var time = convertTimeTo12HourFormat(sales_executive.time);

                    // Start building the popup content synchronously
                    var popupContent = '<b>';
                    popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Live Point</i><br/><br/>';
                    popupContent += '<span style="font-size: medium; color: blue;">' + sales_executive.sales_ref_name + '</span><br/>';
                    popupContent += 'Mobile No: ' + sales_executive.mobile_no + ',<br/>';
                    popupContent += 'Time: ' + time + ',<br/>';
                    if (sales_executive.current_status == 'Login') {
                        popupContent += 'Status: <span style="color: red;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Login Active') {
                        popupContent += 'Status: <span style="color: blue;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Market Start' || sales_executive.current_status == 'Market End') {
                        popupContent += 'Status: <span style="color: green;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Market Active') {
                        popupContent += 'Status: <span style="color: #7700ff;">' + sales_executive.current_status + '</span>,<br/>';
                    } else if (sales_executive.current_status == 'Logout') {
                        popupContent += 'Status: <span style="color: red;">' + sales_executive.current_status + '</span>,<br/>';
                    }
                    popupContent += 'Live Location: Waiting for location updating...<br/>';
                    popupContent += '<center><img id="image_preview" src="' + imageUrl + '" style="max-width: 100px; max-height: 100px;" alt="Image Preview"></center>';

                    // Push reverse geocode promise to the array
                    reverseGeocodePromises.push(reverseGeocode(lat, lng)
                        .then(placeName => {
                            popupContent = popupContent.replace('Live Location: Waiting for location updating...', 'Live Location: ' + (placeName ? placeName : 'Waiting for location updating...'));

                            var icon = L.divIcon({
                                className: 'custom-div-icon',
                                html: "<div style='background-color:#0000ff;' class='marker-pin-sales-executive'></div><i class='fas fa-solid fa-user' style='color:#000000; font-size:17px; top: 3px;'></i>",
                                iconSize: [30, 42],
                                iconAnchor: [15, 42]
                            });

                            var marker = L.marker([lat, lng], {
                                SalesExecutiveId: sales_executive.id,
                                icon: icon
                            });

                            marker.bindPopup(popupContent);
                            allMarkers.push(marker); // Add marker to allMarkers array
                            return marker;
                        })
                        .catch(error => {
                            console.error('Error in reverseGeocode:', error);
                        }));
                });

                // Wait for all reverse geocode promises to resolve
                Promise.all(reverseGeocodePromises)
                    .then(() => {
                        // All markers and popups are now created

                        var selectedSalesExecutiveId = document.getElementById('sales_rep_id').value;

                        // Filter displayedMarkers based on selectedSalesExecutiveId
                        var displayedMarkers = allMarkers.filter(function (marker) {
                            var SalesExecutiveId = marker.options.SalesExecutiveId;
                            return selectedSalesExecutiveId === "" || SalesExecutiveId == selectedSalesExecutiveId;
                        });

                        // Remove all previously displayed markers from the map
                        displayedMarkers.forEach(function (marker) {
                            map.removeLayer(marker);
                        });

                        // Add filtered markers to the map
                        displayedMarkers.forEach(function (marker) {
                            marker.addTo(map);
                        });

                        // Create a feature group from displayed markers
                        var group = new L.featureGroup(displayedMarkers);

                        // Fit the map bounds to the feature group
                        try {
                            map.fitBounds(group.getBounds());
                        } catch (error) {
                            console.log('No Sales Executive Data Found');
                        }
                    })
                    .catch(error => {
                        console.error('Error creating markers and popups:', error);
                    });

            },
            error: function() {
                console.log('error handing here');
            }
        });
    }
}

function gods_view_markers() {
    var _token1=$("#_token1").val();
    var sendInfo={"_token":_token1, "action":"gods_view_dealer"};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(response){

            displayedMarkers.forEach(function (marker) {
                map.removeLayer(marker);
            });
            // Array to store all promises for reverse geocoding
            let reverseGeocodePromises = [];

            // Array to store all markers
            let allMarkers = [];

            response.forEach(function (sales_executive) {
                var lat = parseFloat(sales_executive.latitude);
                var lng = parseFloat(sales_executive.langititude);

                // Get current hostname and port
                var baseUrl = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/';

                var imageUrl = sales_executive.image_name !== '' ? baseUrl + 'storage/barang/' + sales_executive.image_name : baseUrl + 'storage/default/default_image.png';
                var time = convertTimeTo12HourFormat(sales_executive.time);

                // Start building the popup content synchronously
                var popupContent = '<b>';
                popupContent += '<i class="fas fa-map-marker-alt" style="color: red;"> Live Point</i><br/><br/>';
                popupContent += '<span style="font-size: medium; color: blue;">' + sales_executive.sales_ref_name + '</span><br/>';
                popupContent += 'Mobile No: ' + sales_executive.mobile_no + ',<br/>';
                popupContent += 'Time: ' + time + ',<br/>';
                if (sales_executive.current_status == 'Login') {
                    popupContent += 'Status: <span style="color: red;">' + sales_executive.current_status + '</span>,<br/>';
                } else if (sales_executive.current_status == 'Login Active') {
                    popupContent += 'Status: <span style="color: blue;">' + sales_executive.current_status + '</span>,<br/>';
                } else if (sales_executive.current_status == 'Market Start' || sales_executive.current_status == 'Market End') {
                    popupContent += 'Status: <span style="color: green;">' + sales_executive.current_status + '</span>,<br/>';
                } else if (sales_executive.current_status == 'Market Active') {
                    popupContent += 'Status: <span style="color: #7700ff;">' + sales_executive.current_status + '</span>,<br/>';
                } else if (sales_executive.current_status == 'Logout') {
                    popupContent += 'Status: <span style="color: red;">' + sales_executive.current_status + '</span>,<br/>';
                }
                popupContent += 'Live Location: Waiting for location updating...<br/>';
                popupContent += '<center><img id="image_preview" src="' + imageUrl + '" style="max-width: 100px; max-height: 100px;" alt="Image Preview"></center>';

                // Push reverse geocode promise to the array
                reverseGeocodePromises.push(reverseGeocode(lat, lng)
                    .then(placeName => {
                        popupContent = popupContent.replace('Live Location: Waiting for location updating...', 'Live Location: ' + (placeName ? placeName : 'Waiting for location updating...'));

                        var icon = L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div style='background-color:#0000ff;' class='marker-pin-sales-executive'></div><i class='fas fa-solid fa-user' style='color:#000000; font-size:17px; top: 3px;'></i>",
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        });

                        var marker = L.marker([lat, lng], {
                            SalesExecutiveId: sales_executive.id,
                            icon: icon
                        });

                        marker.bindPopup(popupContent);
                        allMarkers.push(marker); // Add marker to allMarkers array
                        return marker;
                    })
                    .catch(error => {
                        console.error('Error in reverseGeocode:', error);
                    }));
            });

            // Wait for all reverse geocode promises to resolve
            Promise.all(reverseGeocodePromises)
                .then(() => {
                    // All markers and popups are now created

                    var selectedSalesExecutiveId = document.getElementById('sales_rep_id').value;

                    // Filter displayedMarkers based on selectedSalesExecutiveId
                    var displayedMarkers = allMarkers.filter(function (marker) {
                        var SalesExecutiveId = marker.options.SalesExecutiveId;
                        return selectedSalesExecutiveId === "" || SalesExecutiveId == selectedSalesExecutiveId;
                    });

                    // Remove all previously displayed markers from the map
                    displayedMarkers.forEach(function (marker) {
                        map.removeLayer(marker);
                    });

                    // Add filtered markers to the map
                    displayedMarkers.forEach(function (marker) {
                        marker.addTo(map);
                    });
                })
                .catch(error => {
                    console.error('Error creating markers and popups:', error);
                });
        },
        error: function() {
            console.log('error handing here');
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


function parseImageName(imageName) {
    try {
        return JSON.parse(imageName);
    } catch (error) {
        console.error('Error parsing image_name:', error);
        return [];
    }
}

function convertTimeTo12HourFormat(time24) {
    var [hours, minutes] = time24.split(':');
    var period = (hours >= 12) ? 'PM' : 'AM';
    var hours12 = hours % 12;
    hours12 = (hours12 === 0) ? 12 : hours12;
    var time12 = hours12 + ':' + minutes + ' ' + period;
    return time12;
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

function get_shop_name() {
    var market_id = $("#market_id").val();
    $("#shop_id").empty();
    var sendInfo = {action: "get_shop_name", market_id: market_id};
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

document.addEventListener('DOMContentLoaded', function () {
    initMap();
    var sales_rep_id = document.getElementById('sales_rep_id');
    sales_rep_id.addEventListener('change', function () {
        showMarkers();
    });
});
