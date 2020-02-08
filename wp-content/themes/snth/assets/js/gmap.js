(function($) {
    $('.acf-map').each(function(){
        map = new_map( $(this) );
    });

    /**
     * Create Google Map
     * @param $el
     * @returns google.maps.Map
     */
    function new_map($el) {
        var $markers = SnthGmap.galleries;
        var grayStyles = [
            {"featureType": "all", "stylers": [{ "saturation": -100 }]},
            {"featureType": "poi", "elementType": "labels.text", "stylers": [{ "visibility": "off" }]},
            {"featureType": "poi.business", "stylers": [{"visibility": "off"}]},
            {"featureType": "road", "elementType": "labels.icon", "stylers": [{"visibility": "off"}]},
            {"featureType": "transit", "stylers": [{"visibility": "off"}]}
        ];
        var args = {
            zoom		: 16,
            center		: new google.maps.LatLng(0, 0),
            disableDefaultUI: true,
            styles: grayStyles,
            mapTypeId	: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map($el[0], args);

        map.markers = [];

        var myOptions = {
            disableAutoPan: false,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(-180, 0),
            boxStyle: {
                padding: "0px 0px 0px 0px",
                width: "240px",
                height: "40px"
            },
            infoBoxClearance: new google.maps.Size(1, 1),
            pane: "floatPane",
            enableEventPropagation: true
        };

        var ib = new InfoBox(myOptions);

        $.each($markers, function(index, marker) {
            add_marker(marker, map, ib);
        });

        center_map(map);

        return map;
    }

    /**
     * Adding Markers
     *
     * @param $marker
     * @param map
     * @param ib
     */
    function add_marker($marker, map, ib) {
        var latlng = new google.maps.LatLng($marker.location.lat, $marker.location.lng);

        var marker = new google.maps.Marker({
            position	: latlng,
            map			: map
        });

        map.markers.push(marker);

        if($marker.info) {

            google.maps.event.addListener(marker, 'click', function(e) {
                //map.clear();
                ib.setContent($marker.info);
                ib.open(map, this);
                map.panTo(ib.getPosition());
            });
        }
    }

    function center_map(map) {
        var bounds = new google.maps.LatLngBounds();

        $.each(map.markers, function(i, marker) {
            var latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
            bounds.extend( latlng );
        });

        if (map.markers.length === 1) {
            map.setCenter(bounds.getCenter());
            map.setZoom( 16 );
        } else {
            map.fitBounds(bounds);
        }
    }
})(jQuery);