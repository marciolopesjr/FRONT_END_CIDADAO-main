// src/components/demands/DemandCategoryGrid.jsx
import React from 'react';
import { useNavigate } from 'react-router-dom';
import { FaWater, FaLightbulb, FaTree } from 'react-icons/fa'; // Import React Icons

const DemandCategoryGrid = () => {
  const navigate = useNavigate();

  const handleCategoryClick = (category) => {
    navigate('/create-demand', { state: { category } });
  };

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <button
        className="bg-blue-500 hover:bg-blue-600 text-white font-bold py-5 px-4 rounded-lg shadow-md transition-colors duration-200 flex flex-col items-center justify-center"
        onClick={() => handleCategoryClick('Boca de lobo')}
      >
        <FaWater size={48} className="mb-2" />
        <p className="text-lg">Boca de lobo</p>
      </button>
      <button
        className="bg-blue-500 hover:bg-blue-600 text-white font-bold py-5 px-4 rounded-lg shadow-md transition-colors duration-200 flex flex-col items-center justify-center"
        onClick={() => handleCategoryClick('Iluminação')}
      >
        <FaLightbulb size={48} className="mb-2" />
        <p className="text-lg">Iluminação</p>
      </button>
      <button
        className="bg-blue-500 hover:bg-blue-600 text-white font-bold py-5 px-4 rounded-lg shadow-md transition-colors duration-200 flex flex-col items-center justify-center"
        onClick={() => handleCategoryClick('Capina')}
      >
        <FaTree size={48} className="mb-2" />
        <p className="text-lg">Capina</p>
      </button>
    </div>
  );
};

export default DemandCategoryGrid;