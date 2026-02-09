// src/pages/LoginPage.tsx
import React from 'react';
import { LoginForm } from '../components/Auth/LoginForm';

export function LoginPage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
      <LoginForm />
    </div>
  );
}

export default LoginPage;
