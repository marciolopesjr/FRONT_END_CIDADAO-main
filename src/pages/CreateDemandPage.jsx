// src/pages/CreateDemandPage.jsx
import React from 'react';
import CreateDemandForm from '../components/demands/CreateDemandForm';

const CreateDemandPage = () => {
  return (
    <div className="py-12 bg-gray-100">
      <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <CreateDemandForm />
      </div>
    </div>
  );
};

export default CreateDemandPage;