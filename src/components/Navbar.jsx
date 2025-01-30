// src/components/Navbar.jsx
import React from 'react';
import { useNavigate, useLocation, Link } from 'react-router-dom';

const Navbar = ({ onLogout, isAdmin }) => {
  const navigate = useNavigate();
  const location = useLocation();

  const handleLogout = () => {
    // Chama a função de logout passada como prop
    onLogout();
    // Redireciona para a página de login
    navigate('/login');
  };

  // Função para determinar o título da página com base na rota
  const getPageTitle = () => {
    switch (location.pathname) {
      case '/':
        return 'Demandas';
      case '/create-demand':
        return 'Criar Demanda';
      case '/login':
        return 'Login';
      case '/register':
        return 'Registro';
      case '/admin':
        return 'Painel Administrativo';
      default:
        return 'Página Desconhecida';
    }
  };

  return (
    <nav className="bg-blue-500 p-4 text-white flex justify-between items-center">
      <span className="font-semibold text-xl">{getPageTitle()}</span>
      <div className="flex space-x-4 items-center">
        {isAdmin && (
          <Link
            to="/admin"
            className="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Painel Admin
          </Link>
        )}
        <button
          className="border-white border-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          onClick={handleLogout}
        >
          Logout
        </button>
      </div>
    </nav>
  );
};

export default Navbar;