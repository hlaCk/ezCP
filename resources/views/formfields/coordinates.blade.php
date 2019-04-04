<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>
@forelse($dataTypeContent->getCoordinates() as $point)
    <input type="hidden" name="{{ $row->field }}[lat]" value="{{ $point['lat'] }}" id="lat"/>
    <input type="hidden" name="{{ $row->field }}[lng]" value="{{ $point['lng'] }}" id="lng"/>
@empty
    <input type="hidden" name="{{ $row->field }}[lat]" value="{{ config('ezcp.googlemaps.center.lat') }}" id="lat"/>
    <input type="hidden" name="{{ $row->field }}[lng]" value="{{ config('ezcp.googlemaps.center.lng') }}" id="lng"/>
@endforelse

<script type="application/javascript">
    function initMap() {
        @forelse($dataTypeContent->getCoordinates() as $point)
            var center = {lat: {{ $point['lat'] }}, lng: {{ $point['lng'] }}};
        @empty
            var center = {lat: {{ config('ezcp.googlemaps.center.lat') }}, lng: {{ config('ezcp.googlemaps.center.lng') }}};
        @endforelse
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: {{ config('ezcp.googlemaps.zoom') }},
            center: center
        });
        var markers = [];
        @forelse($dataTypeContent->getCoordinates() as $point)
            var marker = new google.maps.Marker({
                    position: {lat: {{ $point['lat'] }}, lng: {{ $point['lng'] }}},
                    map: map,
                    draggable: true
                });
            markers.push(marker);
        @empty
            var marker = new google.maps.Marker({
                    position: center,
                    map: map,
                    draggable: true
                });
        @endforelse

        google.maps.event.addListener(marker,'dragend',function(event) {
            document.getElementById('lat').value = this.position.lat();
            document.getElementById('lng').value = this.position.lng();
        });
    }
</script>
<div id="map"></div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('ezcp.googlemaps.key') }}&callback=initMap"></script>
