import { Link } from '@inertiajs/react';

export default function GuestLayout() {
    return (
        <div className="min-h-screen bg-slate-50 font-sans antialiased text-slate-800 selection:bg-blue-600 selection:text-white relative overflow-hidden">
            
            {/* Background Decorations */}
            <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 rounded-full blur-[120px] opacity-70 pointer-events-none"></div>
            <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-100 rounded-full blur-[120px] opacity-70 pointer-events-none"></div>

            {/* Header / Navigation */}
            <header className="fixed top-0 w-full bg-white/70 backdrop-blur-lg border-b border-white/20 z-50 transition-all duration-300">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex h-20 items-center justify-between">
                        {/* Logo */}
                        <div className="flex-shrink-0 transition-transform hover:scale-105">
                            <Link href="/" className="flex items-center gap-3">
                                <div className="p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                                    <img src="/images/logo.png" alt="Logo" className="h-8 w-auto" />
                                </div>
                                <span className="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-indigo-700 tracking-tight">
                                    Automated Payroll System
                                </span>
                            </Link>
                        </div>

                        {/* Right side nav */}
                        <div className="flex items-center gap-4">
                            <a
                                href={route('login')}
                                className="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-full overflow-hidden hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full sm:w-auto"
                            >
                                <span className="relative z-10 flex items-center gap-2">
                                    Sign In
                                    <svg className="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main className="relative pt-32 pb-16 flex flex-col items-center min-h-screen">
                <div className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center mt-10 md:mt-20">
                    
                    <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-sm font-medium mb-8 animate-fade-in-up">
                        <span className="flex h-2 w-2 relative">
                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span className="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        
                    </div>

                    <h1 className="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 mb-8 leading-tight">
                        Modernize Your <br className="hidden sm:block" />
                        <span className="relative whitespace-nowrap">
                            <svg className="absolute -bottom-2 w-full h-3 text-blue-500/20" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0 5 Q 50 10 100 5" stroke="currentColor" strokeWidth="8" fill="transparent" />
                            </svg>
                            <span className="relative bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                                Payroll Workflow
                            </span>
                        </span>
                    </h1>
                    
                    <p className="mt-4 text-lg md:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed mb-16">
                        Eliminate manual data entry and compliance errors. Our automated system integrates attendance, tax calculations, and direct deposits into one powerful platform.
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-6xl mx-auto">
                        <div className="bg-white p-8 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 text-left hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div className="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 className="text-xl font-bold text-slate-800 mb-3">Automated Payroll</h3>
                            <p className="text-slate-600 leading-relaxed">
                                Instantly calculate wages, taxes, and deductions with precise accuracy. Say goodbye to manual spreadsheets.
                            </p>
                        </div>

                        <div className="bg-white p-8 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 text-left hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div className="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-6">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 className="text-xl font-bold text-slate-800 mb-3">Time & Attendance</h3>
                            <p className="text-slate-600 leading-relaxed">
                                Seamlessly track employee hours, leaves, and absences with direct integration into the payroll pipeline.
                            </p>
                        </div>

                        <div className="bg-white p-8 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 text-left hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div className="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center mb-6">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 className="text-xl font-bold text-slate-800 mb-3">Comprehensive Reports</h3>
                            <p className="text-slate-600 leading-relaxed">
                                Generate detailed payslips and analytical reports to keep your business compliant and informed.
                            </p>
                        </div>
                    </div>
                    
                </div>
            </main>
        </div>
    );
}
