// src/components/map/DemandMap.jsx
import React, { useState, useEffect } from 'react';
import { MapContainer, TileLayer, Marker, useMap } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Solução para o problema dos ícones do Leaflet
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

let DefaultIcon = L.icon({
    iconUrl: icon,
    shadowUrl: iconShadow
});

L.Marker.prototype.options.icon = DefaultIcon;

const DemandMap = ({ onLocationSelected }) => {
    const [position, setPosition] = useState(null);
    const [userLocation, setUserLocation] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    setUserLocation(latlng);
                    setPosition(latlng); // Define a posição inicial do marcador como a localização do usuário
                    onLocationSelected(latlng);
                    setLoading(false);
                },
                (error) => {
                    console.error("Erro ao obter a localização:", error);
                    setLoading(false);
                    // Define um centro padrão caso a geolocalização falhe
                    setUserLocation({ lat: -23.5, lng: -46.6 });
                }
            );
        } else {
            console.error("Geolocalização não suportada pelo navegador.");
            setLoading(false);
            // Define um centro padrão caso a geolocalização não seja suportada
            setUserLocation({ lat: -23.5, lng: -46.6 });
        }
    }, [onLocationSelected]);

    const mapCenter = userLocation || [-23.5, -46.6]; // Centro inicial do mapa

    function LocationMarker() {
        const map = useMap();

        useEffect(() => {
            if (userLocation) {
                map.setView(userLocation, map.getZoom());
            }
        }, [userLocation, map]);

        const mapEvents = useMap();
        mapEvents.on('click', (e) => {
            setPosition(e.latlng);
            onLocationSelected(e.latlng);
        });

        return position === null ? null : (
            <Marker position={position}></Marker>
        );
    }

    return (
        <MapContainer center={mapCenter} zoom={13} style={{ height: '300px', width: '100%' }} className="rounded-md">
            <TileLayer
                attribution='© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            {!loading && <LocationMarker />}
            {loading && <p className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white bg-opacity-70 p-4 rounded-md">Carregando sua localização...</p>}
        </MapContainer>
    );
};

export default DemandMap;