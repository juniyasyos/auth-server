import React from "react";
import LoginForm from "./LoginForm";
import { Sidebar } from "./Sidebar";

/* ============================================================
 *  Page
 * ============================================================ */

const LoginV2: React.FC = () => {
    return (
        <div
            data-testid="login-v2-page"
            className="relative min-h-screen w-full flex bg-white text-slate-900 font-sans antialiased selection:bg-cyan-200/60 selection:text-slate-900 overflow-hidden"
        >
            {/* LEFT — Login form */}
            <section
                data-testid="v2-form-section"
                className="relative flex w-full md:w-3/5 lg:w-1/2 items-center justify-center px-6 py-12 sm:px-10 animate-fade-up bg-white"
            >
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 overflow-hidden"
                >
                    <div className="absolute -top-32 -left-24 h-80 w-80 rounded-full bg-cyan-100/60 blur-3xl animate-float-slow" />
                    <div className="absolute -bottom-32 -right-24 h-80 w-80 rounded-full bg-sky-100/60 blur-3xl animate-float-slower" />
                </div>

                <div className="relative z-10 w-full flex justify-center">
                    <LoginForm />
                </div>

                <div className="absolute bottom-6 left-0 right-0 flex justify-center px-6 text-xs text-slate-400">
                    <span>© {new Date().getFullYear()} RS Citra Husada · v2</span>
                </div>
            </section>

            {/* RIGHT — Sidebar */}
            <div className="animate-fade-in-right hidden md:flex md:w-2/5 lg:w-1/2">
                <Sidebar />
            </div>
        </div>
    );
};

export default LoginV2;