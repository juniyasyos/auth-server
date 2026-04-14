import React from 'react';
import type { ApplicationWithIcon } from './types';
import type { Application } from '../../types';
import { STYLES } from './styles';

interface ApplicationCardProps {
    app: ApplicationWithIcon;
    index: number;
    onAppClick: (app: Application) => void;
}

export default function ApplicationCard({ app, index, onAppClick }: ApplicationCardProps) {
    const Icon = app.icon;

    return (
        <div style={{ opacity: 0, animation: `slideUp 0.6s ease-out ${0.1 * index}s forwards` }}>
            <button
                onClick={() => onAppClick(app)}
                disabled={!app.isOnline}
                className={STYLES.grid.card}
            >
                <div className={!app.isOnline ? STYLES.grid.cardButtonOffline : STYLES.grid.cardButton}>
                    {/* Gradient overlay on hover */}
                    <div className={STYLES.grid.cardGradient} />

                    {/* Offline overlay */}
                    {!app.isOnline && (
                        <div className={STYLES.grid.cardOfflineOverlay}>
                            <span className={STYLES.grid.cardOfflineLabel}>Offline</span>
                        </div>
                    )}

                    {/* Icon */}
                    <div className={`${STYLES.appCard.icon} bg-gradient-to-br ${app.gradient}`}>
                        <Icon className="w-6 sm:w-8 h-6 sm:h-8" />
                        <div className={`${STYLES.appCard.iconGlowBase} bg-gradient-to-br ${app.gradient}`}></div>
                    </div>

                    {/* Content */}
                    <div className={STYLES.appCard.content}>
                        <h3 className={STYLES.appCard.title}>
                            {app.name}
                        </h3>
                        <p className={STYLES.appCard.description}>
                            {app.description}
                        </p>

                        {/* Status Badge and User Role */}
                        <div className={STYLES.appCard.badgesContainer}>
                            {/* Status Badge - Simplified */}
                            {app.status && (
                                <div className={STYLES.appCard.statusBadge}>
                                    <span className={app.status === 'Siap Diakses' ? STYLES.appCard.statusBadgeReady : STYLES.appCard.statusBadgeDev}>
                                        <span className={`${STYLES.appCard.statusDot} ${app.status === 'Siap Diakses' ? STYLES.appCard.statusDotReady : STYLES.appCard.statusDotDev}`} />
                                        {app.status}
                                    </span>
                                </div>
                            )}

                            {/* User Role in Application */}
                            {app.userRole && (
                                <div className={STYLES.appCard.statusBadge}>
                                    <span className={STYLES.appCard.roleLabel}>
                                        👤 {app.userRole}
                                    </span>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Hover indicator */}
                    <div className={`${STYLES.appCard.hoverIndicator} ${!app.isOnline ? 'hidden' : ''}`}>
                        <div className={STYLES.appCard.hoverIndicatorButton}>
                            <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </button>
        </div>
    );
}
