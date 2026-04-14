import {
    Hospital,
    Pill,
    TestTube,
    FileText,
    Users,
    ShieldCheck,
    CircleAlert,
    Utensils,
} from 'lucide-react';

export const APP_CONFIG = {
    'Application Control-Client': {
        icon: ShieldCheck,
        gradient: 'from-blue-500 to-blue-600'
    },
    'IKP - Incident Reporting System': {
        icon: CircleAlert,
        gradient: 'from-orange-500 to-orange-600'
    },
    'Pharmacy Management System': {
        icon: Pill,
        gradient: 'from-emerald-500 to-emerald-600'
    },
    'SIMGIZI - Sistem Informasi Manajemen': {
        icon: Utensils,
        gradient: 'from-teal-500 to-teal-600'
    },
    'Tamasudeva - Eticom Management Unit': {
        icon: Hospital,
        gradient: 'from-purple-500 to-purple-600'
    },
    'Laboratorium Klinik': {
        icon: TestTube,
        gradient: 'from-indigo-500 to-indigo-600'
    },
    'Rekam Medis Elektronik': {
        icon: FileText,
        gradient: 'from-cyan-500 to-cyan-600'
    },
    'Sistem Antrian Pasien': {
        icon: Users,
        gradient: 'from-pink-500 to-pink-600'
    },
} as const;

export const DEFAULT_APP_CONFIG = {
    icon: Hospital,
    gradient: 'from-gray-500 to-gray-600'
};

export const DASHBOARD_TEXTS = {
    welcomePrefix: 'SELAMAT DATANG,',
    mainHeadline: 'Satu pintu untuk semua',
    mainHeadlineHighlight: 'aplikasi layanan.',
    description: 'Masuk sekali, lalu akses seluruh aplikasi operasional rumah sakit dengan aman: mutu, insiden, dokumen, hingga analitik manajemen.',
    title: 'Single Sign-On',
    subtitle: 'Portal akses terpadu Rumah Sakit Citra Husada Jember',
    noAppsMessage: 'Tidak ada aplikasi yang tersedia',
    noAppsHint: 'Hubungi administrator untuk akses aplikasi',
    footerTip: '💡 Tip: Klik pada aplikasi untuk membuka, atau akses Admin Panel untuk pengaturan tambahan',
    footerSecurity: 'Semua data terlindungi dengan enkripsi tingkat enterprise',
};

export const MODAL_TEXTS = {
    title: 'Info Akun',
    status: 'Status',
    active: 'Active',
    profiles: 'Profil Akses',
    noProfiles: 'Tidak memiliki akses profil. Hubungi administrator untuk diberikan akses.',
    system: 'System',
    adminPanel: 'Admin Panel',
    logout: 'Keluar',
};

export const ANIMATION_VARIANTS = {
    fadeIn: '0.8s ease-out forwards',
    fadeInDelay: (delay: number) => `0.8s ease-out ${delay}s forwards`,
    slideUp: (delay: number) => `0.6s ease-out ${delay}s forwards`,
    slideDown: '0.3s ease-out forwards',
    slideLeft: '0.3s ease-out forwards',
};
