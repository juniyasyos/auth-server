import React, { ButtonHTMLAttributes } from "react";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    loading?: boolean;
    variant?: "primary" | "ghost";
}

const Button: React.FC<ButtonProps> = ({
    children,
    loading = false,
    variant = "primary",
    className = "",
    disabled,
    ...rest
}) => {
    const base =
        "group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl px-5 py-3.5 text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70";

    const variants: Record<string, string> = {
        primary:
            "bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400 text-white shadow-[0_10px_30px_-10px_rgba(34,211,238,0.55)] hover:shadow-[0_20px_45px_-12px_rgba(34,211,238,0.8)] hover:scale-[1.01] hover:brightness-110 focus:ring-cyan-500",
        ghost:
            "bg-slate-50 text-slate-700 ring-1 ring-slate-200 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-300",
    };

    return (
        <button
            disabled={disabled || loading}
            className={[base, variants[variant], className].join(" ")}
            {...rest}
        >
            {variant === "primary" && (
                <span
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent transition-transform duration-700 group-hover:translate-x-full"
                />
            )}
            {loading ? (
                <>
                    <svg
                        className="h-4 w-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <circle
                            className="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            strokeWidth="4"
                        />
                        <path
                            className="opacity-90"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        />
                    </svg>
                    <span>Signing in…</span>
                </>
            ) : (
                children
            )}
        </button>
    );
};

export default Button;
