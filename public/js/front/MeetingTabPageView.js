define(['underscore','TabPageView','app','twigjs!front/templates/pages/meeting'],function(_,TabPageView,app,template){

    var markerIcon = {
      url: '/public/images/marker.png',
      size: new google.maps.Size(60, 80),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(15, 40),
      scaledSize: new google.maps.Size(30, 40)
    };

    var MeetingTagPageView = TabPageView.extend({
        name:'meeting',
        template:template,
        afterAppend:function(){
            var view = this,
                activity = app.getActivity(),
                activityLatLng = activity.getLatLng(),
                isMobile = app.isMobile(),
                map = new google.maps.Map(view.$('.map').get(0),{
                    disableDefaultUI:true,
                    center:activityLatLng,
                    zoom:16,
                    scrollwheel:!isMobile,
                    maxZoom:isMobile?16:false,
                    minZoom:isMobile?16:false,
                    styles:googleMapsStyles,
                }),
                circleDefaultOptions = {
                    strokeWeight: 0,
                    fillColor: '#4B519C',
                    fillOpacity: 0.05,
                    map: map,
                    center: activityLatLng,
                    radius: 100
                };

            new google.maps.Circle(_.defaults({fillOpacity: 0.3, radius: 20},circleDefaultOptions)),
            new google.maps.Circle(_.defaults({radius: 80},circleDefaultOptions)),
            new google.maps.Circle(_.defaults({},circleDefaultOptions));

            var marker = new google.maps.Marker({
                position:activityLatLng,
                map:map,
                icon:markerIcon
            });

            var infowindow = new google.maps.InfoWindow({
               content: '<img src="'+thumb+'">',
                pixelOffset: new google.maps.Size(-15, 0)
            });
            marker.addListener('click',function(){
                infowindow.open(map, marker);
            });

            //
            view.updateFromNowLabel();
        },
        updateFromNowLabel:function(){
            var view = this,
                activity = app.getActivity(),
                fromNowLabel = activity.getFromNowLabel();
            console.log('fromNowLabel',fromNowLabel);
            view.$('.delay .value').text(fromNowLabel);
        }
    });

    var googleMapsStyles = [
        {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#5d7e9e"
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#f2f2f2"
                },
                {
                    "saturation": "-100"
                },
                {
                    "lightness": "-1"
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#f9f9f9"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "poi.attraction",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "poi.attraction",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "poi.business",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "poi.business",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "poi.government",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.medical",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#e6f3d6"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "poi.school",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.sports_complex",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "saturation": "16"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "visibility": "simplified"
                },
                {
                    "color": "#f3f3f3"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text",
            "stylers": [
                {
                    "color": "#454545"
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#f3f3f3"
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#777777"
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "labels.icon",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#eaf6f8"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#e8f4f6"
                }
            ]
        }
    ];

    return MeetingTagPageView;
});