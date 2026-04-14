// Tailwind CSS class collections untuk konsistensi styling

export const STYLES = {
    // Modal styles
    modal: {
        backdrop: 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50',
        desktopPopup: 'fixed top-20 right-4 md:right-8 z-50 hidden md:block',
        mobilePopup: 'fixed top-0 right-0 h-full z-50 md:hidden w-96 max-w-[100vw]',
        container: 'bg-white rounded-2xl shadow-2xl w-96',
        mobileContainer: 'bg-white h-full shadow-2xl flex flex-col',
    },

    // Header styles
    header: {
        container: 'flex items-center justify-between mb-8',
        logoContainer: 'flex items-center gap-3',
        logoIcon: 'bg-gradient-to-br from-blue-500 via-cyan-500 to-teal-500 p-2.5 rounded-xl shadow-lg relative',
        logoIconInner: 'w-7 h-7 text-white',
        logoContent: 'flex flex-col',
        title: 'text-2xl font-bold bg-gradient-to-r from-blue-700 via-cyan-600 to-teal-600 bg-clip-text text-transparent',
        subtitle: 'text-sm text-gray-600',
        userButton: 'relative group',
        userButtonIcon: 'w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 active:scale-95',
        userButtonContent: 'w-6 h-6 text-white',
    },

    // Welcome section styles
    welcome: {
        container: 'mb-8',
        prefix: 'text-gray-600 text-xs md:text-sm mb-2',
        heading: 'text-2xl md:text-4xl font-bold text-gray-900 mb-3',
        description: 'text-gray-600 text-sm md:text-lg',
    },

    // Applications grid styles
    grid: {
        container: 'w-full',
        layout: 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5',
        card: 'relative cursor-pointer group w-full text-left h-full disabled:opacity-60 disabled:cursor-not-allowed',
        cardButton: 'bg-white/70 backdrop-blur-md rounded-2xl p-4 sm:p-5 md:p-6 shadow-lg hover:shadow-2xl border border-blue-100/50 transition-all duration-300 h-full relative overflow-hidden hover:scale-105 active:scale-95',
        cardButtonOffline: 'bg-white/70 backdrop-blur-md rounded-2xl p-4 sm:p-5 md:p-6 shadow-lg hover:shadow-2xl border border-blue-100/50 transition-all duration-300 h-full relative overflow-hidden opacity-75',
        cardGradient: 'absolute inset-0 bg-gradient-to-br from-blue-500/5 via-cyan-500/5 to-teal-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl',
        cardOfflineOverlay: 'absolute inset-0 bg-red-500/10 rounded-2xl z-30 flex items-center justify-center',
        cardOfflineLabel: 'text-red-700 font-semibold text-sm bg-red-100/80 px-3 py-1.5 rounded-lg backdrop-blur-sm',
    },

    // Application card content
    appCard: {
        icon: 'inline-flex p-3 sm:p-3.5 rounded-xl text-white shadow-lg mb-3 sm:mb-4 relative z-10 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300',
        iconGlowBase: 'absolute inset-0 rounded-xl blur-md opacity-50 -z-10',
        content: 'mb-4 relative z-10',
        title: 'text-sm sm:text-base md:text-lg font-bold text-gray-900 mb-2 leading-snug group-hover:text-blue-600 transition-colors line-clamp-2',
        description: 'text-xs sm:text-sm text-gray-600 line-clamp-2 mb-3',
        badgesContainer: 'space-y-2',
        statusBadge: 'flex items-center gap-2',
        statusBadgeReady: 'text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5 flex-shrink-0 bg-emerald-100 text-emerald-700 border border-emerald-200',
        statusBadgeDev: 'text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5 flex-shrink-0 bg-amber-100 text-amber-700 border border-amber-200',
        statusDot: 'w-1.5 h-1.5 rounded-full',
        statusDotReady: 'bg-emerald-500',
        statusDotDev: 'bg-amber-500 animate-pulse',
        roleLabel: 'text-xs text-blue-700 bg-blue-50/80 border border-blue-200 px-2.5 py-1 rounded-full font-medium flex-shrink-0',
        hoverIndicator: 'absolute bottom-4 right-4 opacity-0 transition-opacity duration-300 z-10 group-hover:opacity-100',
        hoverIndicatorButton: 'w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg',
    },

    // Footer styles
    footer: {
        container: 'mt-16 text-center',
        tip: 'text-sm text-gray-500 mb-2',
        security: 'text-xs text-gray-400',
    },

    // Loading/Empty states
    empty: {
        container: 'flex justify-center items-center py-12',
        spinner: 'animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500',
        emptyContainer: 'flex justify-center items-center py-12',
        emptyContent: 'text-center',
        emptyIcon: 'w-12 h-12 text-gray-400 mx-auto mb-4',
        emptyTitle: 'text-gray-600 text-lg',
        emptySubtitle: 'text-gray-400 text-sm mt-2',
    },
};

export const KEYFRAME_STYLES = `
  @keyframes fadeIn { 
    from { opacity: 0; transform: translateY(10px); } 
    to { opacity: 1; transform: translateY(0); } 
  }
  @keyframes slideUp { 
    from { opacity: 0; transform: translateY(30px); } 
    to { opacity: 1; transform: translateY(0); } 
  }
  @keyframes slideDown { 
    from { opacity: 0; transform: translateY(-20px); } 
    to { opacity: 1; transform: translateY(0); } 
  }
  @keyframes slideLeft { 
    from { transform: translateX(100%); } 
    to { transform: translateX(0); } 
  }
  .animate-slideDown { animation: slideDown 0.3s ease-out forwards; }
  .animate-slideLeft { animation: slideLeft 0.3s ease-out forwards; }
`;
