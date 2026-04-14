import { useState, useEffect } from 'react';
import type { UserApplication } from '../../types';
import type { ApplicationWithIcon } from './types';
import { APP_CONFIG, DEFAULT_APP_CONFIG } from './constants';

interface UseApplicationsProps {
    appsFromProps: Array<{
        app_key: string;
        name: string;
        description: string;
        app_url?: string;
        enabled: boolean;
        logo_url?: string | null;
    }>;
    userApplications?: UserApplication[];
}

export function useApplications({ appsFromProps, userApplications }: UseApplicationsProps) {
    const [applications, setApplications] = useState<ApplicationWithIcon[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const processApplications = async () => {
            try {
                const appsList = appsFromProps;

                const appsWithIcons = appsList.map((app) => {
                    const config = APP_CONFIG[app.name as keyof typeof APP_CONFIG] || DEFAULT_APP_CONFIG;

                    const appStatus: 'Siap Diakses' | 'Dalam Pengembangan' = app.enabled
                        ? 'Siap Diakses'
                        : 'Dalam Pengembangan';
                    const isOnline = app.enabled;

                    const userAppData = userApplications?.find((ua) => ua.app_key === app.app_key);
                    const userRole = userAppData?.roles?.[0]?.name || undefined;

                    return {
                        id: app.app_key,
                        name: app.name,
                        description: app.description || '',
                        status: appStatus,
                        url: app.app_url || `/${app.app_key}`,
                        notifications: 0,
                        icon: config.icon,
                        gradient: config.gradient,
                        isOnline: isOnline,
                        userRole: userRole,
                    };
                });

                setApplications(appsWithIcons);
            } catch (error) {
                setApplications([]);
            } finally {
                setLoading(false);
            }
        };

        processApplications();
    }, [appsFromProps, userApplications]);

    return { applications, loading };
}
