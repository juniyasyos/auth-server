import React, { forwardRef, InputHTMLAttributes } from "react";

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    label: string;
    hint?: string;
    error?: string;
    leftIcon?: React.ReactNode;
    rightSlot?: React.ReactNode;
}

const Input = forwardRef<HTMLInputElement, InputProps>(
    ({ label, hint, error, leftIcon, rightSlot, id, className = "", ...rest }, ref) => {
        const inputId = id ?? rest.name ?? label.toLowerCase();
        return (
            <div className="space-y-2">
                <div className="flex items-baseline justify-between">
                    <label
                        htmlFor={inputId}
                        className="text-xs font-semibold uppercase tracking-[0.12em] text-slate-600"
                    >
                        {label}
                    </label>
                    {hint && !error && (
                        <span className="text-[11px] text-slate-400">{hint}</span>
                    )}
                </div>

                <div className="relative">
                    {leftIcon && (
                        <span className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            {leftIcon}
                        </span>
                    )}
                    <input
                        id={inputId}
                        ref={ref}
                        data-testid={`v2-${inputId}-input`}
                        className={[
                            "w-full rounded-xl bg-white py-3.5 text-sm text-slate-900 placeholder:text-slate-400",
                            "border transition duration-200 outline-none shadow-sm",
                            "focus:bg-white focus:ring-2",
                            leftIcon ? "pl-11" : "pl-4",
                            rightSlot ? "pr-12" : "pr-4",
                            error
                                ? "border-rose-400 focus:border-rose-500 focus:ring-rose-400/25"
                                : "border-slate-200 focus:border-cyan-500 focus:ring-cyan-400/25 hover:border-slate-300",
                            className,
                        ].join(" ")}
                        {...rest}
                    />
                    {rightSlot && (
                        <div className="absolute right-2 top-1/2 -translate-y-1/2">
                            {rightSlot}
                        </div>
                    )}
                </div>

                {error && (
                    <p
                        data-testid={`v2-${inputId}-error`}
                        className="text-xs font-medium text-rose-500"
                    >
                        {error}
                    </p>
                )}
            </div>
        );
    }
);
Input.displayName = "Input";

export default Input;
