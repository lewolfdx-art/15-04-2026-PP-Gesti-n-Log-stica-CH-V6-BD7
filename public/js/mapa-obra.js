// Mapa de la obra (Leaflet + OpenStreetMap), usado en el formulario de contratos
// para marcar la ubicación y en el panel de operadores para verla.
document.addEventListener('alpine:init', () => {
    const CENTRO_HUANCAYO = [-12.0651, -75.2049];

    const pin = (color) => L.divIcon({
        className: '',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        html: `<div style="width:30px;height:30px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);background:${color};border:3px solid #fff;box-shadow:0 1px 5px #0007"></div>`,
    });

    window.Alpine.data('mapaObra', ({ lat = null, lng = null, editable = false, color = '#ea580c' }) => ({
        lat,
        lng,
        editable,
        mapa: null,
        marcador: null,
        buscandoUbicacion: false,
        errorUbicacion: null,

        init() {
            const tiene = this.tieneUbicacion();

            this.mapa = L.map(this.$refs.mapa, { scrollWheelZoom: editable })
                .setView(tiene ? [this.lat, this.lng] : CENTRO_HUANCAYO, tiene ? 17 : 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap',
            }).addTo(this.mapa);

            if (tiene) {
                this.dibujarMarcador();
            }

            if (editable) {
                this.mapa.on('click', (e) => this.marcar(e.latlng.lat, e.latlng.lng));
                this.$watch('lat', () => this.dibujarMarcador());
                this.$watch('lng', () => this.dibujarMarcador());
            }

            // El mapa puede estar en una pestaña oculta: recalcular tamaño al mostrarse
            new ResizeObserver(() => this.mapa.invalidateSize()).observe(this.$refs.mapa);
        },

        tieneUbicacion() {
            return this.lat !== null && this.lat !== '' && this.lng !== null && this.lng !== ''
                && ! isNaN(parseFloat(this.lat)) && ! isNaN(parseFloat(this.lng));
        },

        marcar(lat, lng) {
            this.lat = Number(lat.toFixed(7));
            this.lng = Number(lng.toFixed(7));
            this.dibujarMarcador();
        },

        dibujarMarcador() {
            if (! this.tieneUbicacion()) {
                if (this.marcador) {
                    this.mapa.removeLayer(this.marcador);
                    this.marcador = null;
                }
                return;
            }

            const punto = [parseFloat(this.lat), parseFloat(this.lng)];

            if (this.marcador) {
                this.marcador.setLatLng(punto);
                return;
            }

            this.marcador = L.marker(punto, { icon: pin(color), draggable: editable }).addTo(this.mapa);

            if (editable) {
                this.marcador.on('dragend', (e) => {
                    const p = e.target.getLatLng();
                    this.marcar(p.lat, p.lng);
                });
            }
        },

        usarMiUbicacion() {
            if (! navigator.geolocation) {
                this.errorUbicacion = 'Este navegador no permite obtener la ubicación.';
                return;
            }

            this.buscandoUbicacion = true;
            this.errorUbicacion = null;

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.buscandoUbicacion = false;
                    this.marcar(pos.coords.latitude, pos.coords.longitude);
                    this.mapa.setView([this.lat, this.lng], 18);
                },
                () => {
                    this.buscandoUbicacion = false;
                    this.errorUbicacion = 'No se pudo obtener tu ubicación. Revisa el permiso de ubicación del navegador.';
                },
                { enableHighAccuracy: true, timeout: 15000 },
            );
        },

        quitar() {
            this.lat = null;
            this.lng = null;
            this.dibujarMarcador();
        },
    }));
});
