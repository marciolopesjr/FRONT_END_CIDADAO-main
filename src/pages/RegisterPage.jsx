// src/pages/RegisterPage.jsx
import React from 'react';
import RegisterForm from '../components/auth/registerForm';

const RegisterPage = () => {
  return (
    <div className="flex justify-center items-center h-screen bg-gray-100">
      <RegisterForm />
    </div>
  );
};

export default RegisterPage;