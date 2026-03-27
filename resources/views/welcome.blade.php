<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ open: false }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon icon-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('assets/images/favicon/site.webmanifest') }}" />

    <title>Learn=>Audit : ISO 2700x | Simulateur d'audit de conformité</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-white text-gray-600">
    <!-- Navbar style Block original -->
    <nav class="bg-white border-b border-gray-200 fixed top-0 w-full z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-xl font-semibold text-gray-900 navbar-brand">
                    <img src="{{ asset('assets/images/logo/logo.svg') }}" alt="Learn=>Audit" class="logo" onerror="this.src='https://via.placeholder.com/120x32?text=Learn%3D%3EAudit'">
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-6">
                    <a href="#home" class="text-gray-700 hover:text-purple-500 scroll-smooth">Accueil</a>
                    <a href="#features" class="text-gray-700 hover:text-purple-500 scroll-smooth">Fonctionnalités</a>
                    <a href="#standards" class="text-gray-700 hover:text-purple-500 scroll-smooth">Normes ISO</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-purple-500 scroll-smooth">Comment ça marche</a>
                </div>

                <!-- Right side buttons -->
                <div class="hidden lg:flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-white">Tableau de bord</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-white">Déconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('signin') }}" class="btn btn-outline-white">Connexion</a>
                        <a href="{{ route('signup') }}" class="btn btn-primary">Commencer</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-700 focus:outline-none" @click="open = !open">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-cloak class="md:hidden bg-white border-t border-gray-200">
            <div class="space-y-1 px-4 py-3">
                <a href="#home" class="block text-gray-700 hover:text-purple-500 py-2 scroll-smooth" @click="open = false">Accueil</a>
                <a href="#features" class="block text-gray-700 hover:text-purple-500 py-2 scroll-smooth" @click="open = false">Fonctionnalités</a>
                <a href="#standards" class="block text-gray-700 hover:text-purple-500 py-2 scroll-smooth" @click="open = false">Normes ISO</a>
                <a href="#how-it-works" class="block text-gray-700 hover:text-purple-500 py-2 scroll-smooth" @click="open = false">Comment ça marche</a>
                <a href="#testimonials" class="block text-gray-700 hover:text-purple-500 py-2 scroll-smooth" @click="open = false">Témoignages</a>
                @guest
                    <div class="pt-4 space-y-2">
                        <a href="{{ route('signin') }}" class="block btn btn-outline-white text-center">Connexion</a>
                        <a href="{{ route('signup') }}" class="block btn btn-primary text-center">Commencer</a>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <main class="pt-16">
        <!-- Hero Section style Block original -->
        <section class="lg:py-24 py-12" id="home">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 grid-cols-1 gap-8 items-center">
                    <div class="lg:col-start-1 lg:col-end-6 col-span-12 text-center sm:text-left">
                        <span class="badge bg-white border border-purple-500 rounded-full text-purple-500 px-4">
                            🎓 Nouveau : Simulateur d'audit interactif
                        </span>

                        <div class="my-6">
                            <h1 class="lg:text-[3.4rem] text-3xl lg:leading-14 mb-4 font-bold text-gray-900">
                                Maîtrisez l'audit de conformité<br>
                                <span class="text-purple-500">ISO 2700x</span>
                            </h1>
                            <p class="text-lg text-gray-600">
                                Simulez un audit complet, évaluez les contrôles de sécurité et générez des rapports de conformité. 
                                Développez vos compétences en sécurité des systèmes d'information.
                            </p>
                            <div class="my-6 gap-3 flex justify-center sm:justify-start">
                                <a href="#" class="btn btn-primary">Commencer l'audit</a>
                                <a href="#how-it-works" class="btn btn-outline-white scroll-smooth">En savoir plus</a>
                            </div>
                        </div>
                        <div class="flex md:flex-row flex-col items-center gap-6">
                            <div class="flex items-center md:justify-center gap-3">
                                <span class="h-9 w-9 border border-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="ti ti-certificate text-purple-500 text-xl"></i>
                                </span>
                                <span class="text-gray-600">Certification ISO 27001</span>
                            </div>
                            <div class="flex items-center md:justify-center gap-3">
                                <span class="h-9 w-9 border border-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="ti ti-shield-check text-purple-500 text-xl"></i>
                                </span>
                                <span class="text-gray-600">Audit de conformité</span>
                            </div>
                            <div class="flex items-center md:justify-center gap-3">
                                <span class="h-9 w-9 border border-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="ti ti-file-report text-purple-500 text-xl"></i>
                                </span>
                                <span class="text-gray-600">Rapports détaillés</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-start-7 lg:col-end-14 col-span-12">
                        <div class="relative">
                            <div class="bg-gray-100 md:p-4 p-2 rounded-xl border border-gray-200 scene" data-relative-input="true">
                                <div data-depth="0.09">
                                    <figure>
                                        <img src="{{ asset('assets/images/app-screen-1.jpg') }}" alt="Audit Dashboard" class="w-full rounded-xl shadow border border-gray-200" onerror="this.src='https://via.placeholder.com/600x400?text=ISO+2700x+Audit+Simulator'"/>
                                    </figure>
                                </div>
                            </div>
                            <div class="absolute top-50 -mt-10 left-0 -ms-4 hidden lg:block">
                                <div class="badge bg-purple-500 text-white rounded-full">Auditeur</div>
                                <div class="absolute top-0 end-0 -mt-3 -mr-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.3722 2.21902C22.4794 2.32632 22.5515 2.46361 22.5791 2.61278C22.6067 2.76194 22.5884 2.91597 22.5267 3.05452L14.0412 22.1465C13.9819 22.28 13.8849 22.3933 13.7621 22.4725C13.6393 22.5517 13.4961 22.5933 13.35 22.5921C13.2039 22.591 13.0613 22.5473 12.9397 22.4662C12.8182 22.3852 12.7229 22.2704 12.6657 22.136L9.60416 14.987L2.45366 11.924C2.31974 11.8664 2.20552 11.771 2.12495 11.6496C2.04439 11.5281 2.00097 11.3857 2.00002 11.24C1.99906 11.0942 2.04061 10.9513 2.11958 10.8288C2.19854 10.7062 2.31151 10.6094 2.44466 10.55L21.5367 2.06452C21.675 2.00309 21.8287 1.98497 21.9776 2.01255C22.1265 2.04012 22.265 2.1121 22.3722 2.21902Z" fill="#8B3DFF"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-50 mb-n10 left-0 hidden lg:block">
                                <div class="badge bg-green-500 text-white rounded-full">Conformité ISO</div>
                                <div class="absolute top-0 end-0 -mt-3 -mr-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.3722 2.21902C22.4794 2.32632 22.5515 2.46361 22.5791 2.61278C22.6067 2.76194 22.5884 2.91597 22.5267 3.05452L14.0412 22.1465C13.9819 22.28 13.8849 22.3933 13.7621 22.4725C13.6393 22.5517 13.4961 22.5932 13.35 22.5921C13.2039 22.591 13.0613 22.5473 12.9397 22.4662C12.8182 22.3852 12.7229 22.2704 12.6657 22.136L9.60416 14.987L2.45366 11.924C2.31974 11.8664 2.20552 11.771 2.12495 11.6496C2.04439 11.5281 2.00097 11.3857 2.00002 11.24C1.99906 11.0942 2.04061 10.9513 2.11958 10.8288C2.19854 10.7062 2.31151 10.6094 2.44466 10.55L21.5367 2.06452C21.675 2.00309 21.8287 1.98497 21.9776 2.01255C22.1265 2.04012 22.265 2.1121 22.3722 2.21902Z" fill="#198754"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 me-n8 end-0 hidden lg:block">
                                <div class="badge bg-red-500 text-white rounded-full">Audit Qualité</div>
                                <div class="absolute top-0 left-0 -mt-3 -ml-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.21946 2.21902C2.11223 2.32632 2.04007 2.46361 2.01248 2.61278C1.9849 2.76194 2.0032 2.91597 2.06496 3.05452L10.5505 22.1465C10.6097 22.28 10.7067 22.3933 10.8295 22.4725C10.9523 22.5517 11.0956 22.5933 11.2416 22.5921C11.3877 22.591 11.5303 22.5473 11.6519 22.4662C11.7735 22.3852 11.8687 22.2704 11.926 22.136L14.9875 14.987L22.138 11.924C22.2719 11.8664 22.3861 11.771 22.4667 11.6496C22.5472 11.5281 22.5906 11.3857 22.5916 11.24C22.5926 11.0942 22.551 10.9513 22.472 10.8288C22.3931 10.7062 22.2801 10.6094 22.147 10.55L3.05496 2.06452C2.91659 2.00309 2.76287 1.98497 2.61402 2.01255C2.46516 2.04012 2.32664 2.1121 2.21946 2.21902Z" fill="#DC3545"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute top-0 mt-10 end-0 hidden lg:block">
                                <div class="badge bg-purple-500 text-white rounded-full">Expert ISO</div>
                                <div class="absolute top-0 left-0 -mt-3 -ml-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.21946 2.21902C2.11223 2.32632 2.04007 2.46361 2.01248 2.61278C1.9849 2.76194 2.0032 2.91597 2.06496 3.05452L10.5505 22.1465C10.6097 22.28 10.7067 22.3933 10.8295 22.4725C10.9523 22.5517 11.0956 22.5933 11.2416 22.5921C11.3877 22.591 11.5303 22.5473 11.6519 22.4662C11.7735 22.3852 11.8687 22.2704 11.926 22.136L14.9875 14.987L22.138 11.924C22.2719 11.8664 22.3861 11.771 22.4667 11.6496C22.5472 11.5281 22.5906 11.3857 22.5916 11.24C22.5926 11.0942 22.551 10.9513 22.472 10.8288C22.3931 10.7062 22.2801 10.6094 22.147 10.55L3.05496 2.06452C2.91659 2.00309 2.76287 1.98497 2.61402 2.01255C2.46516 2.04012 2.32664 2.1121 2.21946 2.21902Z" fill="#8B3DFF"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trusted Section style Block -->
        <div class="lg:py-16 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8 lg:mb-10">
                    <small class="uppercase tracking-wider font-semibold text-gray-600">
                        Utilisé par
                        <span class="font-bold text-gray-900">5000+</span>
                        futurs auditeurs et professionnels de la sécurité
                    </small>
                </div>
            </div>
        </div>

        <!-- Features Section style Block -->
        <section class="lg:py-20 py-5" id="features">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-12">
                    <div class="lg:col-span-8 lg:col-start-3 col-span-12">
                        <div class="text-center mb-6 lg:mb-14">
                            <small class="text-purple-500 uppercase tracking-wider font-semibold">Fonctionnalités</small>
                            <h2 class="my-3 text-3xl font-bold text-gray-900">Simulez un audit complet</h2>
                            <p class="mb-0 text-xl text-gray-600">Apprenez à évaluer la conformité ISO 2700x avec des scénarios réalistes</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-10">
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center mb-5 border border-purple-400">
                                <i class="ti ti-checklist text-2xl"></i>
                            </div>
                            <h4 class="text-lg mb-2 text-gray-900">Contrôles ISO 27002</h4>
                            <p class="mb-0 text-gray-600">Évaluez une sélection de 15 contrôles représentatifs des principales sections de la norme.</p>
                        </div>
                    </div>
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center mb-5 border border-purple-400">
                                <i class="ti ti-search text-2xl"></i>
                            </div>
                            <h4 class="text-lg mb-2 text-gray-900">Collecte de Preuves</h4>
                            <p class="mb-0 text-gray-600">Analysez des politiques, configurations et logs simulés pour évaluer la conformité.</p>
                        </div>
                    </div>
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center mb-5 border border-purple-400">
                                <i class="ti ti-chart-bar text-2xl"></i>
                            </div>
                            <h4 class="text-lg mb-2 text-gray-900">Gestion des Risques</h4>
                            <p class="mb-0 text-gray-600">Appliquez l'ISO 27005 pour évaluer les risques et proposer des traitements adaptés.</p>
                        </div>
                    </div>
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center mb-5 border border-purple-400">
                                <i class="ti ti-file-text text-2xl"></i>
                            </div>
                            <h4 class="text-lg mb-2 text-gray-900">Rapport d'Audit</h4>
                            <p class="mb-0 text-gray-600">Générez automatiquement un rapport complet avec les non-conformités identifiées.</p>
                        </div>
                    </div>
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center mb-5 border border-purple-400">
                                <i class="ti ti-chart-line text-2xl"></i>
                            </div>
                            <h4 class="text-lg mb-2 text-gray-900">Suivi de Progression</h4>
                            <p class="mb-0 text-gray-600">Visualisez votre avancement et améliorez votre score de conformité.</p>
                        </div>
                    </div>
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-lg flex items-center justify-center mb-5 border border-purple-400">
                                <i class="ti ti-school text-2xl"></i>
                            </div>
                            <h4 class="text-lg mb-2 text-gray-900">Apprentissage Interactif</h4>
                            <p class="mb-0 text-gray-600">Formez-vous à la méthodologie d'audit à travers des scénarios réalistes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visualize & plan Section style Block original -->
        <section class="lg:py-20 py-8" id="standards">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-12 items-center gap-8">
                    <div class="lg:col-span-5 md:col-span-6 col-span-12">
                        <div>
                            <small class="text-purple-500 uppercase tracking-widest font-semibold">Normes internationales</small>
                            <div class="mb-6 mt-6">
                                <h2 class="mb-3 text-3xl font-bold text-gray-900">Maîtrisez les référentiels ISO 2700x</h2>
                                <p class="mb-0 text-xl text-gray-600">Comprenez les exigences des principales normes de sécurité de l'information.</p>
                            </div>
                            <ul class="mb-5 space-y-2">
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong class="font-semibold">ISO 27001:</strong> Système de Management de la Sécurité de l'Information (SMSI)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong class="font-semibold">ISO 27002:</strong> Code de bonnes pratiques - 114 contrôles de sécurité</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong class="font-semibold">ISO 27005:</strong> Gestion des risques de sécurité de l'information</span>
                                </li>
                            </ul>
                            <a href="#" class="text-purple-500 font-semibold inline-flex items-center hover:underline gap-3">
                                Commencer l'audit
                                <i class="ti ti-arrow-narrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="lg:col-start-7 lg:col-end-14 md:col-span-6 col-span-12">
                        <div class="relative rellax" data-rellax-percentage="1" data-rellax-speed="0.8" data-disable-parallax-down="md">
                            <figure>
                                <img src="{{ asset('assets/images/saas-img-1.jpg') }}" alt="ISO Standards" class="w-full rounded-xl" onerror="this.src='https://via.placeholder.com/500x400?text=ISO+Standards'"/>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collaborate Section style Block original -->
        <section class="lg:py-20 py-8" id="how-it-works">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-12 items-center gap-6">
                    <div class="md:col-span-6 col-span-12 lg:order-2 order-1">
                        <div class="relative rellax mb-7 lg:mb-0" data-rellax-percentage="1" data-rellax-speed="0.8" data-disable-parallax-down="md">
                            <figure>
                                <img src="{{ asset('assets/images/saas-img-2.jpg') }}" alt="Audit Process" class="w-full rounded-xl" onerror="this.src='https://via.placeholder.com/500x400?text=Audit+Process'"/>
                            </figure>
                            <div class="flex flex-col absolute bottom-0 mb-8 mx-4">
                                <img src="https://ui-avatars.com/api/?name=Expert+ISO&background=8B3DFF&color=fff&size=48" class="h-12 w-12 rounded-full border-2 border-white rellax" data-rellax-percentage="0.4" data-rellax-speed="0.4" data-disable-parallax-down="md" alt="Avatar">
                                <img src="https://ui-avatars.com/api/?name=Auditeur&background=8B3DFF&color=fff&size=48" class="h-12 w-12 rounded-full border-2 border-white rellax mt-2" data-rellax-percentage="0.5" data-rellax-speed="0.5" data-disable-parallax-down="md" alt="Avatar">
                                <img src="https://ui-avatars.com/api/?name=Consultant&background=8B3DFF&color=fff&size=48" class="h-12 w-12 rounded-full border-2 border-white rellax mt-2" data-rellax-percentage="0.6" data-rellax-speed="0.6" data-disable-parallax-down="md" alt="Avatar">
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-start-8 lg:col-end-14 md:col-span-6 col-span-12 lg:order-2 order-1">
                        <div class="mt-md-5">
                            <small class="text-purple-500 uppercase tracking-widest font-semibold">Comment ça marche</small>
                            <div class="mb-6 mt-6">
                                <h2 class="mb-3 text-3xl font-bold text-gray-900">Un simulateur d'audit en 4 étapes</h2>
                                <p class="mb-0 text-xl text-gray-600">Suivez la méthodologie complète d'un audit de conformité ISO 2700x.</p>
                            </div>
                            <ul class="space-y-4 mb-5">
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong>Scénario d'entreprise</strong> - Découvrez l'entreprise fictive à auditer et ses systèmes critiques.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong>Collecte de preuves</strong> - Analysez les documents, configurations et logs simulés.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong>Évaluation de conformité</strong> - Déterminez si les contrôles respectent les exigences ISO.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ti ti-circle-check text-purple-500 mt-1"></i>
                                    <span class="text-gray-700"><strong>Rapport d'audit</strong> - Générez votre rapport avec les non-conformités et recommandations.</span>
                                </li>
                            </ul>
                            <a href="#" class="btn btn-primary">Commencer votre premier audit</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action style Block original -->
        <section class="my-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-12 bg-pattern rounded-xl lg:p-14 p-6">
                    <div class="lg:col-span-8 lg:col-start-3 col-span-12 justify-center text-center">
                        <div class="relative z-1 my-lg-5">
                            <div class="mb-8 text-center">
                                <h3 class="text-3xl text-white mb-1">Prêt à devenir auditeur certifié ?</h3>
                                <p class="mb-0 text-white opacity-90">Commencez dès maintenant votre simulation d'audit ISO 2700x — aucun engagement requis.</p>
                            </div>
                            <div class="flex justify-center gap-4">
                                <a href="#" class="btn bg-white text-gray-900 hover:bg-gray-100">
                                    Commencer l'audit
                                </a>
                                <a href="#features" class="btn border border-white text-white hover:bg-white hover:text-gray-900 scroll-smooth">
                                    En savoir plus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer style Block original -->
    <footer class="pt-7 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-12 gap-6">
                <div class="lg:col-span-9 col-span-12">
                    <div class="grid grid-cols-12 gap-6" id="ft-links">
                        <div class="lg:col-span-3 md:col-span-6 col-span-12">
                            <div class="mb-3 pb-2 flex justify-between border-b border-gray-200 lg:border-b-0">
                                <h4 class="text-lg font-semibold text-gray-900">Contenus</h4>
                            </div>
                            <ul class="mb-0 space-y-2">
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Simulateur d'audit</a></li>
                                <li><a href="#standards" class="text-gray-600 hover:text-purple-500 scroll-smooth">Normes ISO 2700x</a></li>
                                <li><a href="#features" class="text-gray-600 hover:text-purple-500 scroll-smooth">Fonctionnalités</a></li>
                            </ul>
                        </div>
                        <div class="lg:col-span-3 md:col-span-6 col-span-12">
                            <div class="mb-3 pb-2 flex justify-between border-b border-gray-200 lg:border-b-0">
                                <h4 class="text-lg font-semibold text-gray-900">Ressources</h4>
                            </div>
                            <ul class="mb-0 space-y-2">
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Documentation</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Guide d'audit</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">FAQ</a></li>
                            </ul>
                        </div>
                        <div class="lg:col-span-3 md:col-span-6 col-span-12">
                            <div class="mb-3 pb-2 flex justify-between border-b border-gray-200 lg:border-b-0">
                                <h4 class="text-lg font-semibold text-gray-900">À propos</h4>
                            </div>
                            <ul class="mb-0 space-y-2">
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Le projet</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Contact</a></li>
                            </ul>
                        </div>
                        <div class="lg:col-span-3 md:col-span-6 col-span-12">
                            <div class="mb-3 pb-2 flex justify-between border-b border-gray-200 lg:border-b-0">
                                <h4 class="text-lg font-semibold text-gray-900">Légal</h4>
                            </div>
                            <ul class="mb-0 space-y-2">
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Mentions légales</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">Politique de confidentialité</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-purple-500">CGU</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-3 md:col-span-6 col-span-12">
                    <div class="me-7">
                        <h4 class="mb-4 font-semibold text-gray-900">Learn=>Audit : ISO 2700x</h4>
                        <p class="text-gray-600">Simulateur d'audit de conformité pour la formation aux normes ISO 27001, 27002 et 27005.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pb-8">
            <div class="grid grid-cols-12 items-center gap-6">
                <div class="lg:col-span-3 col-span-12">
                    <a class="mb-4 mb-lg-0 block text-inverse" href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo/logo.svg') }}" alt="Learn=>Audit" class="logo" onerror="this.src='https://via.placeholder.com/120x32?text=Learn%3D%3EAudit'">
                    </a>
                </div>
                <div class="md:col-span-9 lg:col-span-6 col-span-12">
                    <div class="text-sm mb-3 mb-lg-0 text-lg-center text-gray-600">
                        Copyright © {{ date('Y') }}
                        <span><a href="#" class="text-gray-900 hover:text-purple-500">Learn=>Audit</a></span>
                        | Projet pédagogique d'audit ISO 2700x
                    </div>
                </div>
                <div class="lg:col-span-3">
                    <div class="lg:text-end flex items-center lg:justify-end">
                        <div class="flex gap-4">
                            <a href="#" class="text-gray-600 hover:text-purple-500">
                                <i class="ti ti-brand-linkedin text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-purple-500">
                                <i class="ti ti-brand-github text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-purple-500">
                                <i class="ti ti-mail text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll function
        document.querySelectorAll('.scroll-smooth').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>

</html>