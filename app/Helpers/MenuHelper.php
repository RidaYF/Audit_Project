<?php

namespace App\Helpers;

class MenuHelper
{
    /**
     * Menu principal - Audit et conformité
     */
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Tableau de bord',
                'path' => route('dashboard'),
                'new' => false
            ],
            [
                'icon' => 'audit',
                'name' => 'Audit en cours',
                'path' => route('audit.current'),
                'new' => false
            ],
            [
                'icon' => 'controls',
                'name' => 'Contrôles ISO 27002',
                'subItems' => [
                    ['name' => 'A.5 - Politiques de sécurité', 'path' => route('controls.iso', ['section' => 'A.5']), 'new' => false],
                    ['name' => 'A.6 - Organisation de la sécurité', 'path' => route('controls.iso', ['section' => 'A.6']), 'new' => false],
                    ['name' => 'A.7 - Sécurité RH', 'path' => route('controls.iso', ['section' => 'A.7']), 'new' => false],
                    ['name' => 'A.8 - Gestion des actifs', 'path' => route('controls.iso', ['section' => 'A.8']), 'new' => false],
                    ['name' => 'A.9 - Contrôle d\'accès', 'path' => route('controls.iso', ['section' => 'A.9']), 'new' => false],
                    ['name' => 'A.10 - Cryptographie', 'path' => route('controls.iso', ['section' => 'A.10']), 'new' => false],
                    ['name' => 'A.12 - Sécurité des opérations', 'path' => route('controls.iso', ['section' => 'A.12']), 'new' => false],
                    ['name' => 'A.16 - Gestion des incidents', 'path' => route('controls.iso', ['section' => 'A.16']), 'new' => false],
                ],
            ],
            [
                'icon' => 'evidence',
                'name' => 'Collecte de preuves',
                'path' => route('evidence.collect'),
                'new' => false
            ],
            [
                'icon' => 'evaluation',
                'name' => 'Évaluation de conformité',
                'path' => route('evaluation.index'),
                'new' => false
            ],
            [
                'icon' => 'risk',
                'name' => 'Gestion des risques (ISO 27005)',
                'path' => route('risk.management'),
                'new' => false
            ],
        ];
    }

    /**
     * Rapports et documentation
     */
    public static function getReportsItems()
    {
        return [
            [
                'icon' => 'report',
                'name' => 'Rapport d\'audit',
                'path' => route('report.index'),
                'new' => false
            ],
            [
                'icon' => 'history',
                'name' => 'Historique des audits',
                'path' => route('audit.history'),
                'new' => false
            ],
            [
                'icon' => 'statistics',
                'name' => 'Statistiques de conformité',
                'path' => route('statistics.index'),
                'new' => false
            ],
        ];
    }



    /**
     * Retourne tous les groupes de menu
     */
    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'AUDIT & CONFORMITÉ',
                'items' => self::getMainNavItems()
            ],
            [
                'title' => 'RAPPORTS & ANALYSE',
                'items' => self::getReportsItems()
            ],
        ];
    }

    /**
     * Vérifie si un chemin est actif
     */
    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    /**
     * Retourne le SVG de l'icône correspondante
     */
    public static function getIconSvg($iconName)
    {
        $icons = [
            // Dashboard
            'dashboard' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"/></svg>',

            // Audit
            'audit' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Contrôles
            'controls' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4H20V20H4V4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 8H16M8 12H16M8 16H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Collecte de preuves
            'evidence' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Évaluation
            'evaluation' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Gestion des risques
            'risk' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V12M12 16H12.01M3 12C3 4 12 4 12 4C12 4 21 4 21 12C21 20 12 20 12 20C12 20 3 20 3 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Rapport
            'report' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4H20V20H4V4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 12H16M8 16H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Historique
            'history' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8V12L15 15M12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Statistiques
            'statistics' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 17L9 11L13 15L21 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Documentation
            'documentation' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4H20V20H4V4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 8H16M8 12H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // FAQ
            'faq' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 9C9 7.89543 9.89543 7 11 7H13C14.1046 7 15 7.89543 15 9C15 10.1046 14.1046 11 13 11H12V13M12 17H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            // Support
            'support' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 17.0518V12C20 7.58174 16.4183 4 12 4C7.58168 4 3.99994 7.58174 3.99994 12V17.0518M19.9998 14.041V19.75C19.9998 20.5784 19.3282 21.25 18.4998 21.25H13.9998M6.5 18.75H5.5C4.67157 18.75 4 18.0784 4 17.25V13.75C4 12.9216 4.67157 12.25 5.5 12.25H6.5C7.32843 12.25 8 12.9216 8 13.75V17.25C8 18.0784 7.32843 18.75 6.5 18.75ZM17.4999 18.75H18.4999C19.3284 18.75 19.9999 18.0784 19.9999 17.25V13.75C19.9999 12.9216 19.3284 12.25 18.4999 12.25H17.4999C16.6715 12.25 15.9999 12.9216 15.9999 13.75V17.25C15.9999 18.0784 16.6715 18.75 17.4999 18.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/></svg>';
    }
}