// File: /src/App.jsx
import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Routes, Navigate } from 'react-router-dom';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import CreateDemandPage from './pages/CreateDemandPage';
import DemandCategoryGrid from './components/demands/DemandCategoryGrid';
import Navbar from './components/Navbar';
import AdminDashboardSimple from './components/admin/AdminDashboardSimple';

function App() {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [user, setUser] = useState(null);
  const [isAdmin, setIsAdmin] = useState(true); // TODO: Implementar lógica real de verificação de admin

  const handleLogin = (userData) => {
    setIsLoggedIn(true);
    setUser(userData);
    // TODO: Implementar lógica real para verificar se o usuário é admin e atualizar setIsAdmin
  };

  const handleLogout = () => {
    setIsLoggedIn(false);
    setUser(null);
  };

  return (
    <Router>
      {isLoggedIn && <Navbar onLogout={handleLogout} isAdmin={isAdmin} />}
      <div className="container mx-auto mt-8">
        <Routes>
          <Route path="/login" element={!isLoggedIn ? <LoginPage onLogin={handleLogin} /> : <Navigate to="/" />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/create-demand" element={isLoggedIn ? <CreateDemandPage /> : <Navigate to="/login" />} />
          <Route path="/" element={isLoggedIn ? <DemandCategoryGrid /> : <Navigate to="/login" />} />
          <Route path="/admin" element={isAdmin ? <AdminDashboardSimple /> : <Navigate to="/login" />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;