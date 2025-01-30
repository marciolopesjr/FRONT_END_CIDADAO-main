// src/pages/LoginPage.jsx
import React from 'react';
import LoginForm from '../components/auth/LoginForm';

const LoginPage = ({ onLogin }) => {
  return (
    <div className="flex justify-center items-center h-screen bg-gray-100">
      <LoginForm onLogin={onLogin} />
    </div>
  );
};

export default LoginPage;