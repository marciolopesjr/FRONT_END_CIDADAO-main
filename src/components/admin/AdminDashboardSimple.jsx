// src/components/admin/AdminDashboardSimple.jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Solução para o problema dos ícones do Leaflet
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

let DefaultIcon = L.icon({
  iconUrl: icon,
  shadowUrl: iconShadow,
});

L.Marker.prototype.options.icon = DefaultIcon;

const AdminDashboardSimple = () => {
  const [demands, setDemands] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const mapCenter = [-23.5, -46.6]; // Centro inicial do mapa (pode ajustar)

  useEffect(() => {
    const fetchDemands = async () => {
      setLoading(true);
      setError(null);
      try {
        const response = await axios.get('/api/get_demands.php');
        setDemands(response.data);
      } catch (err) {
        console.error("Erro ao buscar demandas:", err);
        setError('Erro ao carregar as demandas.');
      } finally {
        setLoading(false);
      }
    };

    fetchDemands();
  }, []);

  return (
    <div className="p-4 sm:p-6 md:p-8">
      <h2 className="text-2xl sm:text-3xl font-semibold mb-4 text-gray-800">Painel Administrativo - Mapa de Demandas</h2>

      {loading && <p>Carregando demandas...</p>}
      {error && <p className="text-red-500">{error}</p>}

      <div className="h-[400px] sm:h-[500px] md:h-[600px]">
        <MapContainer center={mapCenter} zoom={11} style={{ height: '100%', width: '100%' }} className="rounded-md">
          <TileLayer
            attribution='© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          />
          {demands.map(demand => (
            <Marker
              key={demand.id}
              position={[parseFloat(demand.latitude), parseFloat(demand.longitude)]}
            >
              <Popup>
                <div>
                  <h3 className="font-bold">{demand.category}</h3>
                  <p>{demand.description.substring(0, 50)}...</p>
                </div>
              </Popup>
            </Marker>
          ))}
        </MapContainer>
      </div>
    </div>
  );
};

export default AdminDashboardSimple;