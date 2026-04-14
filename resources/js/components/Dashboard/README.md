# Dashboard Component Structure

## Overview
Dashboard component telah dipecah menjadi beberapa file yang lebih kecil dan terstruktur untuk meningkatkan maintainability dan reusability.

## File Structure

```
Dashboard/
├── Dashboard.tsx           # Main component wrapper
├── ModalContent.tsx        # Modal komponen terpisah untuk user info
├── ApplicationCard.tsx     # Reusable card komponen untuk setiap aplikasi
├── types.ts               # TypeScript interfaces dan type definitions
├── constants.ts           # Konstanta, text, dan konfigurasi aplikasi
├── styles.ts              # Tailwind CSS class collections
├── useApplications.ts     # Custom hook untuk logic pemrosesan aplikasi
├── index.ts               # Barrel export untuk public API
└── README.md              # Dokumentasi ini
```

## File Descriptions

### Dashboard.tsx
Main component yang mengkoordinasikan semua sub-components. Handles:
- User authentication state
- Modal visibility state
- Application rendering
- Layout structure

**Imports dari:**
- `./types.ts` - Type definitions
- `./constants.ts` - Text dan konfigurasi
- `./styles.ts` - CSS classes
- `./useApplications.ts` - Custom hook
- `./ModalContent.tsx` - Modal component
- `./ApplicationCard.tsx` - Card component

### ModalContent.tsx
Komponen modal untuk menampilkan informasi user dan akses profil.

**Handles:**
- User profile display
- Access profiles listing
- Admin panel navigation
- Logout functionality

**Props:**
```typescript
interface ModalContentProps {
  user: UserType;
  nip: string;
  logout: () => void;
  onClose: () => void;
  isMobile?: boolean;
  accessProfiles?: AccessProfile[];
}
```

### ApplicationCard.tsx
Reusable card komponen untuk menampilkan satu aplikasi dengan status dan role user.

**Handles:**
- Icon rendering dengan gradient
- Status badge (Siap Diakses / Dalam Pengembangan)
- User role display
- Online/offline state
- Hover animations

**Props:**
```typescript
interface ApplicationCardProps {
  app: ApplicationWithIcon;
  index: number;
  onAppClick: (app: Application) => void;
}
```

### types.ts
Centralized TypeScript type definitions dan interfaces.

**Contains:**
- `DashboardProps` - Props untuk main Dashboard component
- `ApplicationWithIcon` - Extended Application type dengan icon data
- `ModalContentProps` - Props untuk ModalContent component
- `AccessProfile` - Access profile type
- `ApplicationData` - Application data with roles
- `ApplicationRole` - Role type

### constants.ts
Semua konstanta, teks, dan konfigurasi aplikasi.

**Contains:**
- `APP_CONFIG` - Mapping aplikasi ke icon dan gradient
- `DEFAULT_APP_CONFIG` - Default icon dan gradient
- `DASHBOARD_TEXTS` - Semua text di dashboard
- `MODAL_TEXTS` - Semua text di modal
- `ANIMATION_VARIANTS` - Animation timing configurations

### styles.ts
Tailwind CSS class collections untuk konsistensi styling.

**Contains:**
- `STYLES` - Object berisi Tailwind classes terkelompok per section
  - `modal.*` - Modal styling
  - `header.*` - Header styling
  - `welcome.*` - Welcome section styling
  - `grid.*` - Grid layout styling
  - `appCard.*` - Application card styling
  - `footer.*` - Footer styling
  - `empty.*` - Empty state styling
- `KEYFRAME_STYLES` - CSS keyframe animations

### useApplications.ts
Custom hook untuk merancang dan memproses aplikasi.

**Functionality:**
- Memproses aplikasi dari props
- Menambahkan icon dan gradient berdasarkan nama
- Menghitung status (online/offline)
- Mendapatkan user role untuk setiap aplikasi
- Handles loading state

**Returns:**
```typescript
{
  applications: ApplicationWithIcon[];
  loading: boolean;
}
```

### index.ts
Barrel export untuk mempermudah importing.

**Exports:**
- Main components: `Dashboard`, `ModalContent`, `ApplicationCard`
- Custom hooks: `useApplications`
- Types, constants, dan styles

## Usage

### Import dari folder Dashboard
```typescript
// Recommended: Using barrel export
import { Dashboard, useApplications } from '@/components/Dashboard';

// Or specific imports
import Dashboard from '@/components/Dashboard/Dashboard';
import { useApplications } from '@/components/Dashboard/useApplications';
```

### Menggunakan komponen
```jsx
<Dashboard 
  user={user}
  applications={applications}
  accessProfiles={accessProfiles}
/>
```

### Menggunakan custom hook
```typescript
const { applications, loading } = useApplications({
  appsFromProps: myApps,
  userApplications: user.applications,
});
```

### Menggunakan constants
```typescript
import { DASHBOARD_TEXTS, APP_CONFIG } from '@/components/Dashboard';

console.log(DASHBOARD_TEXTS.mainHeadline); // "Satu pintu untuk semua"
const config = APP_CONFIG['Pharmacy Management System'];
```

### Menggunakan styles
```typescript
import { STYLES } from '@/components/Dashboard';

<div className={STYLES.header.container}>...</div>
```

## Benefits

✅ **Better Maintainability** - Setiap file punya single responsibility  
✅ **Reusability** - ComponentCard dapat digunakan di tempat lain  
✅ **Scalability** - Mudah untuk menambah fitur baru  
✅ **Type Safety** - Centralized type definitions  
✅ **Consistency** - Constants dan styles terpusat  
✅ **Testing** - Easier to test individual components dan hooks  
✅ **Documentation** - Clear separation of concerns  

## Adding New Features

### Menambah aplikasi baru ke APP_CONFIG
Edit `constants.ts`:
```typescript
'New App Name': {
  icon: NewIcon,
  gradient: 'from-color-500 to-color-600'
}
```

### Mengubah teks di dashboard
Edit `constants.ts` bagian `DASHBOARD_TEXTS` atau `MODAL_TEXTS`.

### Mengubah styling
Edit `styles.ts` di bagian yang sesuai.

### Menambah logic aplikasi
Update `useApplications.ts` hook.

### Menambah tipe baru
Tambahkan di `types.ts`.

## Future Improvements

- [ ] Extract animation logic ke custom hook
- [ ] Create utility functions untuk app data transformation
- [ ] Add unit tests untuk setiap component
- [ ] Create Storybook stories untuk components
- [ ] Extract color theme ke separate file
- [ ] Create reusable Badge component
- [ ] Add error boundary untuk error handling
