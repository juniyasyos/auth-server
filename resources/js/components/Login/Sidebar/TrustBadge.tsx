import React from "react";
import { CheckCircle2 } from "lucide-react";

interface TrustBadgeProps {
    title: string;
    subtitle: string;
    pulse?: boolean;
}

const TrustBadge: React.FC<TrustBadgeProps> = ({ title, subtitle, pulse }) => (
    <div className="flex items-center gap-3 rounded-xl border border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-md shadow-sm">
        <div className="relative flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-cyan-50 ring-1 ring-cyan-100">
            <CheckCircle2 className="h-4 w-4 text-cyan-600" />
            {pulse && (
                <span className="absolute -right-0.5 -top-0.5 flex h-2.5 w-2.5">
                    <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400/70" />
                    <span className="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500" />
                </span>
            )}
        </div>
        <div className="min-w-0">
            <p className="truncate text-sm font-semibold text-slate-900">{title}</p>
            <p className="truncate text-[11px] text-slate-500">{subtitle}</p>
        </div>
    </div>
);

export default TrustBadge;
