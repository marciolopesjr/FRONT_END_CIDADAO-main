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

// Novo componente funcional para o Popup
const DemandPopup = ({ demand, onDemandUpdated }) => {
  const [isEditing, setIsEditing] = useState(false);
  const [status, setStatus] = useState(demand.status);
  const [secretariatId, setSecretariatId] = useState(demand.secretariat_id);
  const [editError, setEditError] = useState('');
  const [editLoading, setEditLoading] = useState(false);

  const handleEditClick = () => {
    setIsEditing(true);
    setEditError(''); // Limpa qualquer erro anterior ao entrar no modo de edição
  };

  const handleCancelClick = () => {
    setIsEditing(false);
    setStatus(demand.status); // Reverte para o status original
    setSecretariatId(demand.secretariat_id); // Reverte para a secretaria original
    setEditError(''); // Limpa qualquer erro
  };

  const handleSaveClick = async () => {
    setEditLoading(true);
    setEditError('');
    try {
      const response = await axios.post('/api/update_demand.php', {
        demand_id: demand.id,
        status: status,
        secretariat_id: secretariatId === '' ? null : secretariatId // Envia null se for string vazia
      });

      if (response.status === 200) {
        setIsEditing(false);
        onDemandUpdated(demand.id, { status: status, secretariat_id: secretariatId }); // Notifica o componente pai sobre a atualização
      } else {
        setEditError(response.data.error || 'Erro ao atualizar demanda.');
      }
    } catch (error) {
      console.error("Erro ao atualizar demanda:", error);
      setEditError('Erro ao conectar com o servidor.');
    } finally {
      setEditLoading(false);
    }
  };

  return (
    <div>
      <h3 className="font-bold">{demand.category}</h3>
      <p>{demand.description.substring(0, 50)}...</p>

      {isEditing ? (
        <div>
          <div className="mb-2">
            <label className="block text-gray-700 text-sm font-bold mb-1">Status:</label>
            <input
              type="text" // Pode ser substituído por um <select> no futuro
              className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              value={status}
              onChange={(e) => setStatus(e.target.value)}
            />
          </div>
          <div className="mb-2">
            <label className="block text-gray-700 text-sm font-bold mb-1">Secretaria ID:</label>
            <input
              type="number"
              className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              value={secretariatId || ''} // Exibe string vazia se for null
              onChange={(e) => setSecretariatId(e.target.value === '' ? null : parseInt(e.target.value))} // Converte para número ou null
            />
          </div>
          {editError && <p className="text-red-500 text-xs italic mb-2">{editError}</p>}
          <div className="flex justify-end space-x-2">
            <button
              className="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline text-xs"
              type="button"
              onClick={handleCancelClick}
              disabled={editLoading}
            >
              Cancelar
            </button>
            <button
              className={`bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline text-xs ${editLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
              type="button"
              onClick={handleSaveClick}
              disabled={editLoading}
            >
              {editLoading ? 'Salvando...' : 'Salvar'}
            </button>
          </div>
        </div>
      ) : (
        <div>
          <p><strong>Status:</strong> {demand.status}</p>
          <p><strong>Secretaria ID:</strong> {demand.secretariat_id || 'Não atribuída'}</p>
          <div className="flex justify-end">
            <button
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline text-xs"
              onClick={handleEditClick}
            >
              Editar
            </button>
          </div>
        </div>
      )}
    </div>
  );
};


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

  const handleDemandUpdated = (demandId, updatedFields) => {
    // Atualiza o estado 'demands' para refletir a mudança localmente
    setDemands(demands.map(demand =>
      demand.id === demandId ? { ...demand, ...updatedFields } : demand
    ));
  };


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
                <DemandPopup demand={demand} onDemandUpdated={handleDemandUpdated} />
              </Popup>
            </Marker>
          ))}
        </MapContainer>
      </div>
    </div>
  );
};

export default AdminDashboardSimple;