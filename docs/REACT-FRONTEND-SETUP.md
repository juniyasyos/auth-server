# React Frontend Setup Guide

Panduan lengkap untuk setup React frontend yang berjalan di port 3100 dan berkomunikasi dengan Laravel API.

## Struktur Folder React Frontend

```
frontend/
├── public/
├── src/
│   ├── components/
│   │   ├── Auth/
│   │   │   ├── LoginForm.tsx
│   │   │   ├── RegisterForm.tsx
│   │   │   └── ProtectedRoute.tsx
│   │   ├── Dashboard/
│   │   └── Common/
│   ├── pages/
│   │   ├── LoginPage.tsx
│   │   ├── RegisterPage.tsx
│   │   ├── DashboardPage.tsx
│   │   ├── ProfilePage.tsx
│   │   └── NotFoundPage.tsx
│   ├── services/
│   │   ├── api.ts
│   │   ├── authService.ts
│   │   └── userService.ts
│   ├── hooks/
│   │   ├── useAuth.ts
│   │   └── useApi.ts
│   ├── context/
│   │   └── AuthContext.tsx
│   ├── types/
│   │   └── index.ts
│   ├── App.tsx
│   ├── main.tsx
│   └── index.css
├── .env.local
├── .env.example
├── package.json
├── tsconfig.json
├── vite.config.ts
└── README.md
```

## 1. Setup Awal

### 1.1 Buat direktori frontend
```bash
cd /home/juni/projects
mkdir laravel-iam-frontend
cd laravel-iam-frontend
```

### 1.2 Inisialisasi project dengan Vite
```bash
npm create vite@latest . -- --template react-ts
```

### 1.3 Install dependencies
```bash
npm install
npm install axios react-router-dom zustand
npm install -D tailwindcss postcss autoprefixer
npm install -D @types/react @types/react-dom
```

### 1.4 Setup Tailwind CSS
```bash
npx tailwindcss init -p
```

Edit `tailwind.config.js`:
```javascript
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

## 2. Konfigurasi Environment

Buat file `.env.local`:
```
VITE_API_URL=http://localhost:8000
VITE_APP_NAME=Laravel IAM
```

Opsi untuk production:
```
VITE_API_URL=https://api.example.com
```

## 3. Setup API Service

Lihat contoh file di bawah:
- [src/services/api.ts](../frontend-examples/api.ts)
- [src/services/authService.ts](../frontend-examples/authService.ts)
- [src/context/AuthContext.tsx](../frontend-examples/AuthContext.tsx)
- [src/hooks/useAuth.ts](../frontend-examples/useAuth.ts)

## 4. Jalankan Development Server

```bash
# Dari direktori frontend
npm run dev

# Application akan berjalan di: http://localhost:5173
# Tetapi dikonfigurasi Vite untuk gunakan port 3100
```

Jika ingin gunakan port 3100 langsung, edit `vite.config.ts`:
```typescript
export default {
  server: {
    port: 3100,
    host: true,
  }
}
```

## 5. Build untuk Production

```bash
npm run build

# Output akan ada di folder dist/
```

## 6. Integrasi Dengan Laravel

### 6.1 Flow Login

1. User akses `http://localhost:8000` atau `http://localhost`
2. RedirectToFrontend middleware mendeteksi port 3100 accessible
3. Request di-redirect ke `http://localhost:3100`
4. React app di-render, user bisa login
5. Login endpoint: `POST /api/auth/login`
   - Body: `{ "email": "user@example.com", "password": "password" }`
   - Response: `{ "user": {...}, "access_token": "...", "token_type": "Bearer" }`
6. Token disimpan di localStorage atau secure cookie
7. Setiap API request mengirim token di header: `Authorization: Bearer {token}`

### 6.2 CORS Sudah Dikonfigurasi

CORS configuration sudah ditambahkan di Laravel:
- Origin yang diizinkan: `http://localhost:3100`
- Supported methods: Semua (GET, POST, PUT, DELETE, PATCH)
- Credentials: Diizinkan

### 6.3 API Endpoints yang Tersedia

#### Authentication
- `POST /api/auth/login` - Login user
- `POST /api/auth/register` - Register user baru
- `GET /api/auth/me` (protected) - Get user info
- `POST /api/auth/logout` (protected) - Logout
- `POST /api/auth/refresh` (protected) - Refresh token

#### OAuth2/SSO (Existing)
- `POST /api/sso/token` - Token exchange
- `POST /api/sso/token/refresh` - Refresh token
- `GET /api/sso/userinfo` - Get user info
- `POST /api/sso/introspect` - Introspect token

### 6.4 Error Handling

Respon API menggunakan standard HTTP status codes:
- `200` - OK
- `201` - Created
- `400` - Bad Request (validation error)
- `401` - Unauthorized (no token or invalid token)
- `403` - Forbidden
- `404` - Not Found
- `500` - Server Error

Example error response:
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

## 7. Testing API Dengan cURL

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

### Get User Info (dengan token)
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer {token}"
```

## 8. Dokumentasi Lengkap API

Lihat [API-RESPONSE-FORMAT.md](../docs/API-RESPONSE-FORMAT.md) untuk format response lengkap.
Lihat [API-USAGE-EXAMPLES.md](../docs/API-USAGE-EXAMPLES.md) untuk contoh penggunaan.

## 9. Troubleshooting

### Port 3100 tidak accessible
- Jika frontend tidak berjalan, Laravel akan tetap render halaman default
- Cek apakah React development server sudah berjalan: `npm run dev`
- Cek port apakah sudah in-use: `lsof -i :3100`

### CORS Error
- Pastikan `FRONTEND_URL` di `.env` Laravel sudah benar
- Cek console browser untuk error message lengkap
- Pastikan token dikirim dengan format header yang benar: `Authorization: Bearer {token}`

### Login gagal
- Pastikan user sudah register atau seeders sudah jalan
- Cek email dan password yang benar
- Lihat Laravel logs untuk detail error: `php artisan pail`

## 10. Environment Variables Lengkap

### Laravel (.env)
```
FRONTEND_PORT=3100
FRONTEND_HOST=localhost
FRONTEND_URL=http://localhost:3100
FRONTEND_HOST_PATTERN=/localhost/
```

### React (.env.local)
```
VITE_API_URL=http://localhost:8000
VITE_APP_NAME=Laravel IAM
```

## Next Steps

1. Setup React project dengan panduan di atas
2. Implement login/register pages
3. Setup protected routes
4. Implement dashboard
5. Integrate dengan existing IAM features (roles, permissions)
6. Setup state management (Zustand/Redux)
7. Error handling & loading states
8. Testing setup (Vitest, React Testing Library)
