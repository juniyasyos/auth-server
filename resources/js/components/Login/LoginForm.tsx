import React, { useState, FormEvent } from "react";
import { Eye, EyeOff, Lock, User, AlertCircle, ArrowRight, ShieldCheck } from "lucide-react";
import { Button, Input } from "./Button";
import "../../../css/components/login.css";

const LoginForm: React.FC = () => {
    const [nip, setNip] = useState<string>("");
    const [password, setPassword] = useState<string>("");
    const [showPassword, setShowPassword] = useState<boolean>(false);
    const [remember, setRemember] = useState<boolean>(true);
    const [errors, setErrors] = useState<{ nip?: string; password?: string }>({});
    const [authError, setAuthError] = useState<string>("");
    const [loading, setLoading] = useState<boolean>(false);
    const [shake, setShake] = useState<boolean>(false);

    const triggerShake = () => {
        setShake(true);
        window.setTimeout(() => setShake(false), 350);
    };

    const validate = () => {
        const e: { nip?: string; password?: string } = {};
        if (!nip.trim()) e.nip = "NIP is required";
        else if (nip.trim().length < 4) e.nip = "NIP must be at least 4 characters";
        if (!password) e.password = "Password is required";
        else if (password.length < 6) e.password = "Password must be at least 6 characters";
        return e;
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setAuthError("");
        const v = validate();
        setErrors(v);
        if (Object.keys(v).length > 0) {
            triggerShake();
            return;
        }
        setLoading(true);
        window.setTimeout(() => {
            setLoading(false);
            if (nip === "admin" && password === "admin123") {
                setAuthError("");
            } else {
                setAuthError("Invalid NIP or password. Please try again.");
                triggerShake();
            }
        }, 1200);
    };

    return (
        <div
            data-testid="v2-login-form-wrapper"
            className={["w-full max-w-md", shake ? "animate-shake" : ""].join(" ")}
        >
            {/* Mobile brand chip */}
            <div className="mb-8 flex items-center gap-3 lg:hidden">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 ring-1 ring-cyan-100">
                    <ShieldCheck className="h-5 w-5 text-cyan-600" />
                </div>
                <div>
                    <div className="text-sm font-semibold text-slate-900">RS Citra Husada</div>
                    <div className="text-xs text-slate-500">Integrated Hospital MS</div>
                </div>
            </div>

            <header className="mb-8">
                <span className="inline-flex items-center gap-2 rounded-full border border-cyan-100 bg-cyan-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-700 animate-fade-up">
                    <span className="h-1.5 w-1.5 rounded-full bg-cyan-500 shadow-[0_0_10px_2px_rgba(6,182,212,0.5)]" />
                    Sign in
                </span>
                <h1
                    data-testid="v2-form-title"
                    className="mt-5 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl animate-fade-up delay-100"
                >
                    Admin Login
                </h1>
                <p data-testid="v2-form-subtitle" className="mt-3 text-sm text-slate-500 animate-fade-up delay-200">
                    Secure access to system dashboard. Use your hospital credentials to
                    continue.
                </p>
            </header>

            {authError && (
                <div
                    data-testid="v2-auth-error"
                    role="alert"
                    className="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
                >
                    <AlertCircle className="mt-0.5 h-4 w-4 flex-shrink-0 text-rose-500" />
                    <span>{authError}</span>
                </div>
            )}

            <form
                data-testid="v2-login-form"
                onSubmit={handleSubmit}
                noValidate
                className="space-y-5 animate-fade-up delay-300"
            >
                <Input
                    label="NIP"
                    name="nip"
                    hint="Nomor Induk Pegawai"
                    inputMode="numeric"
                    autoComplete="username"
                    placeholder="e.g. 1987654321"
                    value={nip}
                    onChange={(e) => setNip(e.target.value)}
                    error={errors.nip}
                    leftIcon={<User className="h-5 w-5" />}
                />

                <Input
                    label="Password"
                    name="password"
                    type={showPassword ? "text" : "password"}
                    autoComplete="current-password"
                    placeholder="Enter your password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    error={errors.password}
                    leftIcon={<Lock className="h-5 w-5" />}
                    rightSlot={
                        <button
                            type="button"
                            data-testid="v2-toggle-password"
                            onClick={() => setShowPassword((s) => !s)}
                            aria-label={showPassword ? "Hide password" : "Show password"}
                            className="rounded-md p-1.5 text-slate-400 transition hover:text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400/50"
                        >
                            {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                        </button>
                    }
                />

                <div className="flex items-center justify-between pt-1">
                    <label
                        htmlFor="v2-remember"
                        className="group flex cursor-pointer items-center gap-2.5 text-sm text-slate-600 select-none"
                    >
                        <span className="relative inline-flex">
                            <input
                                id="v2-remember"
                                type="checkbox"
                                checked={remember}
                                data-testid="v2-remember-checkbox"
                                onChange={(e) => setRemember(e.target.checked)}
                                className="peer h-4 w-4 cursor-pointer appearance-none rounded border border-slate-300 bg-white transition checked:border-cyan-500 checked:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-400/40"
                            />
                            <svg
                                className="pointer-events-none absolute left-0 top-0 h-4 w-4 scale-90 text-white opacity-0 transition peer-checked:opacity-100"
                                viewBox="0 0 16 16"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2.5"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <path d="M3 8.5l3 3 7-7" />
                            </svg>
                        </span>
                        <span className="transition group-hover:text-slate-900">Remember me</span>
                    </label>
                    <a
                        href="#"
                        data-testid="v2-forgot-link"
                        className="text-sm font-medium text-cyan-600 transition hover:text-cyan-700 focus:outline-none focus:underline"
                    >
                        Forgot password?
                    </a>
                </div>

                <Button type="submit" loading={loading} data-testid="v2-signin-button">
                    <span>Sign in</span>
                    <ArrowRight className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" />
                </Button>

                <p className="pt-2 text-center text-xs text-slate-400">
                    Demo credentials —{" "}
                    <code className="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] text-slate-700">
                        admin
                    </code>{" "}
                    /{" "}
                    <code className="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] text-slate-700">
                        admin123
                    </code>
                </p>
            </form>
        </div>
    );
};

export default LoginForm;
