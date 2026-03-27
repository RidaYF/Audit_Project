<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Learn=>Audit : ISO 2700x | Audit Simulator</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        <div class="min-h-screen">
            <!-- Hero Section -->
            <div class="relative overflow-hidden bg-gradient-to-b from-white to-gray-50 dark:from-[#0a0a0a] dark:to-[#161615]">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
                    <div class="text-center max-w-4xl mx-auto">
                        <div class="inline-flex items-center gap-2 bg-[#fff2f2] dark:bg-[#1D0002] rounded-full px-4 py-1.5 mb-6">
                            <span class="text-[#F53003] dark:text-[#FF4433] text-sm font-medium">Interactive Learning</span>
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white mb-6">
                            Learn=>Audit : 
                            <span class="text-[#F53003] dark:text-[#FF4433]">ISO 2700x</span>
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                            Simulateur d'audit de conformité ISO 27001, 27002 et 27005. 
                            Devenez expert en sécurité des systèmes d'information.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('audit.start') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#F53003] hover:bg-black dark:bg-[#FF4433] dark:hover:bg-white text-white dark:text-black font-medium rounded-lg transition-all duration-200 shadow-sm">
                                Commencer l'audit
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                            <a href="#about" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24" id="about">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Comment ça fonctionne ?</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Un simulateur d'audit complet basé sur les normes ISO 2700x
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
                        <div class="w-12 h-12 bg-[#fff2f2] dark:bg-[#1D0002] rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Scénario Réaliste</h3>
                        <p class="text-gray-600 dark:text-gray-400">Auditez une entreprise fictive avec ses systèmes, politiques et configurations.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
                        <div class="w-12 h-12 bg-[#fff2f2] dark:bg-[#1D0002] rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Contrôles ISO 27002</h3>
                        <p class="text-gray-600 dark:text-gray-400">Évaluez une sélection de contrôles représentatifs des principales sections.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
                        <div class="w-12 h-12 bg-[#fff2f2] dark:bg-[#1D0002] rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Rapport d'Audit</h3>
                        <p class="text-gray-600 dark:text-gray-400">Générez automatiquement un rapport complet avec vos conclusions.</p>
                    </div>
                </div>
            </div>

            <!-- ISO Standards Section -->
            <div class="bg-gray-50 dark:bg-[#161615] border-t border-gray-200 dark:border-gray-800">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="text-center lg:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Normes ISO 2700x</h3>
                            <p class="text-gray-600 dark:text-gray-400">Les standards internationaux pour la sécurité de l'information</p>
                        </div>
                        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="bg-white dark:bg-[#0a0a0a] rounded-lg p-5 border border-gray-200 dark:border-gray-800">
                                <div class="text-2xl font-bold text-[#F53003] dark:text-[#FF4433] mb-2">ISO 27001</div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Système de Management de la Sécurité de l'Information (SMSI)</p>
                            </div>
                            <div class="bg-white dark:bg-[#0a0a0a] rounded-lg p-5 border border-gray-200 dark:border-gray-800">
                                <div class="text-2xl font-bold text-[#F53003] dark:text-[#FF4433] mb-2">ISO 27002</div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Code de bonnes pratiques pour la gestion de la sécurité</p>
                            </div>
                            <div class="bg-white dark:bg-[#0a0a0a] rounded-lg p-5 border border-gray-200 dark:border-gray-800">
                                <div class="text-2xl font-bold text-[#F53003] dark:text-[#FF4433] mb-2">ISO 27005</div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Gestion des risques de sécurité de l'information</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <div class="max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Prêt à commencer votre mission d'audit ?</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        Simulez un audit complet et développez vos compétences en conformité ISO 2700x
                    </p>
                    <a href="{{ route('audit.start') }}" class="inline-flex items-center justify-center px-8 py-3 bg-[#F53003] hover:bg-black dark:bg-[#FF4433] dark:hover:bg-white text-white dark:text-black font-semibold rounded-lg transition-all duration-200">
                        Démarrer l'audit
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>