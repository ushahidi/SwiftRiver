$(document).ready(function() {
    
    // Popup close event
    function onPopupClose(evt) {
        // 'this' is the popup.
        selectControl.unselect(this.feature);
    }

    // Display popup when a feature is selected
    function onFeatureSelect(evt) {
        
        feature = evt.feature;

        // Only show popup for individual features and not clusters
        if(feature.attributes.count == 1) {

            // Get the droplet detail page and only display the .fullstory div's contents
            $.get(droplet_base_url + feature.cluster[0].attributes.droplet_id, function(response) {
                   var droplet_detail = $(response).find('.fullstory').html();
                                             
                   popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                                            feature.geometry.getBounds().getCenterLonLat(),
                                            new OpenLayers.Size(100,100),
                                            droplet_detail,
                                            null, true, onPopupClose);
                   feature.popup = popup;
                   popup.feature = feature;
                   map.addPopup(popup);
               });   
        }
    }

    // Remove popup when feature is deselected
    function onFeatureUnselect(evt) {
        feature = evt.feature;
        if (feature.popup) {
            popup.feature = null;
            map.removePopup(feature.popup);
            feature.popup.destroy();
            feature.popup = null;
        }
    }    
    
    // Create the map
    var map = new OpenLayers.Map('map');
    var osm = new OpenLayers.Layer.OSM( "OpenLayers OSM");
    map.addLayer(osm);
    
    //FIXME: Determine this from bounds obtained from droplet contents.
    map.setCenter(new OpenLayers.LonLat(0, 0), 2);
    
    // The marker layer
    var markerRadius = 4;
    var style = new OpenLayers.Style({
        pointRadius: "${radius}",
        label: "${label}",
        fillColor: "#ff4400",
        fillOpacity: 0.8,
        strokeColor: "#cc6633",
        strokeWidth: "${strokeWidth}",
        strokeOpacity: 0.3,
        fontColor: "#ffffff",
        fontSoze: "${fontsize}"
    }, {
        context: {
            radius: function(feature)
            {
                feature_count = feature.attributes.count;
                if (feature_count > 10000)
                {
                	return markerRadius * 17;
                }
                else if (feature_count > 5000)
                {
                	return markerRadius * 10;
                }
                else if (feature_count > 1000)
                {
                	return markerRadius * 8;
                }
                else if (feature_count > 500)
                {
                	return markerRadius * 7;
                }
                else if (feature_count > 100)
                {
                	return markerRadius * 6;
                }
                else if (feature_count > 10)
                {
                	return markerRadius * 5;
                }
                else if (feature_count >= 2)
                {
                	return markerRadius * 3;
                }
                else
                {
                	return markerRadius * 2;
                }
            },
            fontsize: function(feature) {
                feature_count = feature.attributes.count;
                if (feature_count > 1000)
                {
                	return "20px";
                }
                else if (feature_count > 500)
                {
                	return "18px";
                }
                else if (feature_count > 100)
                {
                	return "14px";
                }
                else if (feature_count > 10)
                {
                	return "12px";
                }
                else if (feature_count >= 2)
                {
                	return "10px";
                }
                else
                {
                	return "";
                }
            },
            strokeWidth: function(feature) {
                feature_count = feature.attributes.count;
                if (feature_count > 10000)
                {
                	return 45;
                }
                else if (feature_count > 5000)
                {
                	return 30;
                }
                else if (feature_count > 1000)
                {
                	return 22;
                }
                else if (feature_count > 100)
                {
                	return 15;
                }
                else if (feature_count > 10)
                {
                	return 10;
                }
                else if (feature_count >= 2)
                {
                	return 5;
                }
                else
                {
                	return 1;
                }  
            },
            label: function(feature) {
                return feature.attributes.count > 1 ? feature.attributes.count : "";
            }
        }
    });    
    
    vLayer = new OpenLayers.Layer.Vector("Droplet Markers", {
                    protocol: new OpenLayers.Protocol.HTTP({
                    url: geojson_url,
                    format: new OpenLayers.Format.GeoJSON()
                   }),
                   strategies: [
                    new OpenLayers.Strategy.Fixed(),
                    new OpenLayers.Strategy.Cluster()
                    ],
                    styleMap: new OpenLayers.StyleMap({
                        "default": style,
                        "select": style
                    })
    });
    map.addLayer(vLayer);

    // Register marker selection events
    vLayer.events.on({
        'featureselected': onFeatureSelect,
        'featureunselected': onFeatureUnselect
    });
    
    // Make features on the marker layer selectable
    selectControl = new OpenLayers.Control.SelectFeature(vLayer);
    map.addControl(selectControl);
    selectControl.activate(); 


    // Full screen map borrowed from the Ushahidi fullscreenmap plugin
    var orig_width = $("#map").width();
    var orig_height = $("#map").height();
    
    $(".fullscreenmap_click").colorbox({
    	width:"100%", 
    	height:"100%", 
    	inline:true, 
    	href:"#map",
    	// Resize Map DIV and Refresh
    	onComplete:function(){                	    
    	    $("#map").width("99%");
    		$("#map").height("99%");
    		map.setCenter(currCenter, currZoom, false, false);
    	},
    	// Return DIV to original state
    	onClosed:function(){
    	    currZoom = map.getZoom();
            currCenter = map.getCenter();
    		$("#map").width(orig_width);
    		$("#map").height(orig_height);
    		$("#map").show();
    		map.setCenter(currCenter, currZoom, false, false);
    	}
    });    

  
});

