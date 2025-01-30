// File: /src/components/demands/CreateDemandForm.jsx
import React, { useState } from 'react';
import DemandMap from '../map/DemandMap';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';

const CreateDemandForm = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const [description, setDescription] = useState('');
  const [selectedLocation, setSelectedLocation] = useState(null);
  const category = location.state?.category;
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');

  const handleLocationSelected = (latlng) => {
    setSelectedLocation(latlng);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!selectedLocation) {
      alert('Por favor, selecione a localização no mapa.');
      return;
    }

    setLoading(true);
    setError('');
    setSuccessMessage('');

    try {
      const apiUrl = 'https://cidadao.codemakersbr.com.br/api/create_demand.php';
      const response = await axios.post(apiUrl, {
        category,
        description,
        latitude: selectedLocation.lat,
        longitude: selectedLocation.lng,
      });

      if (response.status === 201) {
        setSuccessMessage(response.data.message);
        navigate('/'); // Redireciona para a página inicial após a criação
      } else {
        setError(response.data.error || 'Erro ao criar a demanda.');
      }
    } catch (error) {
      console.error("Erro ao criar demanda:", error);
      setError('Erro ao conectar com o servidor.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-lg mx-auto p-6 bg-white rounded-md shadow-md">
      <h2 className="text-2xl font-semibold mb-4">Criar Pedido de Providência</h2>
      {category && <p className="mb-4">Categoria selecionada: <span className="font-semibold">{category}</span></p>}
      <form onSubmit={handleSubmit}>
        <div className="mb-4">
          <label htmlFor="description" className="block text-gray-700 text-sm font-bold mb-2">
            Descrição da Demanda
          </label>
          <textarea
            id="description"
            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            placeholder="Descreva o problema aqui"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            rows="4"
          />
        </div>
        <div className="mb-4">
          <label className="block text-gray-700 text-sm font-bold mb-2">
            Localização
          </label>
          <DemandMap onLocationSelected={handleLocationSelected} />
          {selectedLocation && (
            <p className="text-sm mt-2">
              Localização selecionada: Lat: {selectedLocation.lat}, Lng: {selectedLocation.lng}
            </p>
          )}
        </div>
        <div className="flex items-center justify-end">
          <button
            className={`bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}
            type="submit"
            disabled={loading}
          >
            {loading ? 'Enviando Pedido...' : 'Enviar Pedido'}
          </button>
        </div>
        {error && <p className="text-red-500 text-sm mt-2">{error}</p>}
        {successMessage && <p className="text-green-500 text-sm mt-2">{successMessage}</p>}
      </form>
    </div>
  );
};

export default CreateDemandForm;