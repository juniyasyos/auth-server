import React from 'react';
import {
    LogOut,
    Settings,
    User,
    X
} from 'lucide-react';
import { ssoService } from '../../services/ssoService';
import { MODAL_TEXTS } from './constants';
import type { ModalContentProps } from './types';

export default function ModalContent({
    user,
    nip,
    logout,
    onClose,
    isMobile = false,
    accessProfiles = []
}: ModalContentProps) {
    return (
        <div className="flex flex-col h-full">
            {/* Header */}
            <div className="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 className="text-base font-semibold text-gray-900">{MODAL_TEXTS.title}</h2>
                <button onClick={onClose} className="text-gray-400 hover:text-gray-600">
                    <X className="w-5 h-5" />
                </button>
            </div>

            {/* Profile */}
            <div className="px-5 py-4 flex items-center gap-3">
                <div className="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                    <User className="w-5 h-5 text-gray-600" />
                </div>
                <div>
                    <p className="font-medium text-gray-900">{user?.name || 'User'}</p>
                    <p className="text-sm text-gray-500">{nip}</p>
                </div>
            </div>

            <div className="border-t border-gray-100" />

            {/* Details */}
            <div className={`px-5 py-4 space-y-3 text-sm ${isMobile ? 'flex-1 overflow-y-auto' : ''}`}>
                <div className="flex justify-between items-center">
                    <span className="text-gray-500">{MODAL_TEXTS.status}</span>
                    <span className="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-600 font-medium">
                        {MODAL_TEXTS.active}
                    </span>
                </div>

                {/* Access Profiles Section */}
                {accessProfiles && accessProfiles.length > 0 && (
                    <div className="pt-4 border-t border-gray-100 space-y-3">
                        <h3 className="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            {MODAL_TEXTS.profiles}
                        </h3>

                        {accessProfiles.map((profile) => (
                            <div key={profile.id} className="space-y-2 pb-3 border-b border-gray-100 last:border-0">
                                <div className="flex items-start justify-between gap-2">
                                    <div className="flex-1">
                                        <p className="text-sm font-semibold text-gray-900">
                                            {profile.name}
                                        </p>
                                        {profile.description && (
                                            <p className="text-xs text-gray-500 mt-1">
                                                {profile.description}
                                            </p>
                                        )}
                                    </div>
                                    {profile.is_system && (
                                        <span className="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700 font-medium flex-shrink-0">
                                            {MODAL_TEXTS.system}
                                        </span>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {/* No access profiles message */}
                {(!accessProfiles || accessProfiles.length === 0) && (
                    <div className="pt-4 border-t border-gray-100">
                        <p className="text-xs text-gray-500 italic">
                            {MODAL_TEXTS.noProfiles}
                        </p>
                    </div>
                )}
            </div>

            {/* Actions */}
            <div className="p-4 border-t border-gray-100 space-y-2">
                <button
                    onClick={() => ssoService.redirectToAdminPanel()}
                    className="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg flex items-center justify-center gap-2"
                >
                    <Settings className="w-4 h-4" />
                    {MODAL_TEXTS.adminPanel}
                </button>

                <button
                    onClick={logout}
                    className="w-full text-sm text-red-600 hover:bg-red-50 py-2.5 rounded-lg flex items-center justify-center gap-2"
                >
                    <LogOut className="w-4 h-4" />
                    {MODAL_TEXTS.logout}
                </button>
            </div>
        </div>
    );
}
