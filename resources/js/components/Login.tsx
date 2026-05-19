import { useState, useEffect } from 'react';
import axios from 'axios';
import LoginDefaultView from './Login/LoginDefaultView';

interface CompanyData {
  name?: string;
}

interface LoginProps {
  onLogin: (nip: string, password: string) => void;
  isLoading?: boolean;
  error?: string | null;
  devAutofill?: {
    nip?: string;
    password?: string;
  } | null;
}

export default function Login({ onLogin, isLoading = false, error, devAutofill = null }: LoginProps) {
  const [nip, setNip] = useState('');
  const [password, setPassword] = useState('');
  const [focusedInput, setFocusedInput] = useState<string | null>(null);
  const [showPassword, setShowPassword] = useState(false);
  const [showError, setShowError] = useState(false);
  const [companyName, setCompanyName] = useState<string>('');

  // Auto-fill untuk development mode
  useEffect(() => {
    if (devAutofill?.nip && devAutofill?.password) {
      setNip(devAutofill.nip);
      setPassword(devAutofill.password);
      return;
    }

    const isDev = import.meta.env.VITE_APP_ENV === 'dev';
    if (isDev) {
      const devNip = import.meta.env.VITE_DEV_NIP || '';
      const devPassword = import.meta.env.VITE_DEV_PASSWORD || '';
      setNip(devNip);
      setPassword(devPassword);
    }
  }, [devAutofill]);

  useEffect(() => {
    let isMounted = true;

    const loadCompanyData = async () => {
      try {
        const response = await axios.get<CompanyData>('/api/company');
        if (isMounted) {
          setCompanyName(response.data.name ?? '');
        }
      } catch (err) {
        console.warn('Unable to load company data', err);
      }
    };

    loadCompanyData();

    return () => {
      isMounted = false;
    };
  }, []);

  useEffect(() => {
    if (error) {
      setShowError(true);

      const timer = setTimeout(() => {
        setShowError(false);
      }, 4000);

      return () => clearTimeout(timer);
    }
  }, [error]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (nip && password && !isLoading) {
      onLogin(nip, password);
    }
  };

  return (
    <LoginDefaultView
      nip={nip}
      setNip={setNip}
      password={password}
      setPassword={setPassword}
      focusedInput={focusedInput}
      setFocusedInput={setFocusedInput}
      showPassword={showPassword}
      setShowPassword={setShowPassword}
      showError={showError}
      onCloseError={() => setShowError(false)}
      handleSubmit={handleSubmit}
      isLoading={Boolean(isLoading)}
      error={error}
      companyName={companyName}
    />
  );
}