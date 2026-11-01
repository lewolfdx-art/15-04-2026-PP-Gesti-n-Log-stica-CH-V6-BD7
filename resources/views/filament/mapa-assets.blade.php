{{-- Leaflet y el componente Alpine del mapa de obras; se cargan antes que Alpine --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/mapa-obra.js') }}?v={{ filemtime(public_path('js/mapa-obra.js')) }}"></script>
