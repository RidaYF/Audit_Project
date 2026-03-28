<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use App\Models\Auditeur;
use App\Models\Entreprise;
use App\Models\Reference;
use App\Models\Clause;
use App\Models\Controle;
use App\Models\Preuve;
use App\Models\Audit;
use App\Models\AuditReference;
use App\Models\Evaluation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Vider les tables avant de remplir (optionnel)
        // Décommentez si vous voulez réinitialiser les données
        // $this->truncateTables();

        // ============================================
        // 1. CRÉATION DES UTILISATEURS ET AUDITEURS
        // ============================================
        
        // Création de l'utilisateur admin (avec vérification d'existence)
        $admin = Utilisateur::firstOrCreate(
            ['email' => 'admin@audit-simulator.com'],
            [
                'id' => Str::uuid(),
                'username' => 'admin_system',
                'password_hash' => Hash::make('password123'),
                'role' => 'admin',
                'niveau' => 'expert',
                'progression_globale' => 100,
                'is_active' => true,
                'date_inscription' => now(),
                'derniere_connexion' => now(),
            ]
        );

        // Création de l'auditeur principal (formateur)
        $formateur = Utilisateur::firstOrCreate(
            ['email' => 'jean.dupont@audit-simulator.com'],
            [
                'id' => Str::uuid(),
                'username' => 'jean_dupont',
                'password_hash' => Hash::make('password123'),
                'role' => 'formateur',
                'niveau' => 'expert',
                'progression_globale' => 95,
                'is_active' => true,
                'date_inscription' => now(),
                'derniere_connexion' => now(),
            ]
        );

        Auditeur::firstOrCreate(
            ['utilisateur_id' => $formateur->id],
            [
                'id' => Str::uuid(),
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'telephone' => '+33 6 12 34 56 78',
                'certification' => 'Lead Auditor ISO 27001, CISSP',
                'experience_ans' => 12,
                'specialites' => json_encode(['ISO 27001', 'ISO 27005', 'Sécurité Cloud']),
            ]
        );

        // Création de l'auditeur apprenant (débutant)
        $apprenant = Utilisateur::firstOrCreate(
            ['email' => 'marie.martin@etudiant.com'],
            [
                'id' => Str::uuid(),
                'username' => 'marie_martin',
                'password_hash' => Hash::make('password123'),
                'role' => 'auditeur',
                'niveau' => 'debutant',
                'progression_globale' => 25,
                'is_active' => true,
                'date_inscription' => now(),
                'derniere_connexion' => now(),
            ]
        );

        Auditeur::firstOrCreate(
            ['utilisateur_id' => $apprenant->id],
            [
                'id' => Str::uuid(),
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'telephone' => '+33 6 98 76 54 32',
                'certification' => 'En cours de formation',
                'experience_ans' => 1,
                'specialites' => json_encode(['ISO 27001']),
            ]
        );

        // ============================================
        // 2. CRÉATION DE L'ENTREPRISE FICTIVE
        // ============================================
        
        $entreprise = Entreprise::firstOrCreate(
            ['nom' => 'TechInnov Solutions'],
            [
                'id' => Str::uuid(),
                'secteur' => 'Services Informatiques',
                'taille' => 'PME',
                'image_logo' => '/storage/logos/techinnov.png',
                'systemes_cles' => 'Système de gestion client (CRM), Plateforme e-commerce, Infrastructure cloud AWS, Base de données clients',
                'localisation' => 'Paris, France',
                'contexte_metier' => 'TechInnov Solutions est une entreprise de services numériques qui accompagne les PME dans leur transformation digitale. L\'entreprise gère des données sensibles clients et traite des transactions financières.',
                'departements' => 'Direction, Commercial, Développement, Infrastructure, Support Client, RH',
                'actifs_critiques' => 'Base de données clients, Clés API, Codes sources propriétaires, Infrastructures de production',
                'chiffre_affaires' => '5M€',
                'effectif' => 45,
                'is_active' => true,
            ]
        );

        // ============================================
        // 3. CRÉATION DES RÉFÉRENTIELS ISO
        // ============================================
        
        $iso27001 = Reference::firstOrCreate(
            ['code' => 'ISO27001'],
            [
                'id' => Str::uuid(),
                'nom' => 'ISO/IEC 27001:2022',
                'description' => 'Système de Management de la Sécurité de l\'Information (SMSI) - Exigences',
                'type_norme' => 'management',
                'version' => '2022',
                'date_publication' => '2022-10-01',
                'url_officielle' => 'https://www.iso.org/standard/82875.html',
                'ordre_affichage' => 1,
                'is_active' => true,
            ]
        );

        $iso27002 = Reference::firstOrCreate(
            ['code' => 'ISO27002'],
            [
                'id' => Str::uuid(),
                'nom' => 'ISO/IEC 27002:2022',
                'description' => 'Code de bonnes pratiques pour la gestion de la sécurité de l\'information',
                'type_norme' => 'control',
                'version' => '2022',
                'date_publication' => '2022-02-01',
                'url_officielle' => 'https://www.iso.org/standard/75652.html',
                'ordre_affichage' => 2,
                'is_active' => true,
            ]
        );

        $iso27005 = Reference::firstOrCreate(
            ['code' => 'ISO27005'],
            [
                'id' => Str::uuid(),
                'nom' => 'ISO/IEC 27005:2022',
                'description' => 'Gestion des risques de sécurité de l\'information',
                'type_norme' => 'risk',
                'version' => '2022',
                'date_publication' => '2022-10-01',
                'url_officielle' => 'https://www.iso.org/standard/82875.html',
                'ordre_affichage' => 3,
                'is_active' => true,
            ]
        );

        // ============================================
        // 4. CRÉATION DES CLAUSES ISO 27001
        // ============================================
        
        // Clause principale 4 - Contexte de l'organisation
        $clause4 = Clause::firstOrCreate(
            [
                'reference_id' => $iso27001->id,
                'numero' => '4',
            ],
            [
                'id' => Str::uuid(),
                'parent_id' => null,
                'titre' => 'Contexte de l\'organisation',
                'description' => 'L\'organisation doit déterminer les enjeux externes et internes pertinents pour son objectif stratégique et qui affectent sa capacité à atteindre les résultats attendus de son SMSI.',
                'niveau' => 1,
                'chemin_complet' => '4',
                'niveau_importance' => 'eleve',
                'ordre_affichage' => 1,
            ]
        );
        
        // Sous-clause 4.1 - Compréhension de l'organisation et de son contexte
        Clause::firstOrCreate(
            [
                'reference_id' => $iso27001->id,
                'numero' => '4.1',
            ],
            [
                'id' => Str::uuid(),
                'parent_id' => $clause4->id,
                'titre' => 'Compréhension de l\'organisation et de son contexte',
                'description' => 'L\'organisation doit déterminer les enjeux externes et internes pertinents pour son objectif stratégique.',
                'niveau' => 2,
                'chemin_complet' => '4.1',
                'niveau_importance' => 'eleve',
                'ordre_affichage' => 1,
            ]
        );
        
        // Clause principale 5 - Leadership
        $clause5 = Clause::firstOrCreate(
            [
                'reference_id' => $iso27001->id,
                'numero' => '5',
            ],
            [
                'id' => Str::uuid(),
                'parent_id' => null,
                'titre' => 'Leadership',
                'description' => 'La direction doit démontrer son leadership et son engagement envers le SMSI.',
                'niveau' => 1,
                'chemin_complet' => '5',
                'niveau_importance' => 'critique',
                'ordre_affichage' => 2,
            ]
        );
        
        // Clause principale 6 - Planification
        $clause6 = Clause::firstOrCreate(
            [
                'reference_id' => $iso27001->id,
                'numero' => '6',
            ],
            [
                'id' => Str::uuid(),
                'parent_id' => null,
                'titre' => 'Planification',
                'description' => 'L\'organisation doit planifier, mettre en œuvre et maintenir des processus pour répondre aux exigences et gérer les risques.',
                'niveau' => 1,
                'chemin_complet' => '6',
                'niveau_importance' => 'eleve',
                'ordre_affichage' => 3,
            ]
        );

        // ============================================
        // 5. CRÉATION DES CONTRÔLES ISO 27002
        // ============================================
        
        // Contrôle A.5 - Politiques de sécurité
        $controlePolitique = Controle::firstOrCreate(
            [
                'clause_id' => $clause5->id,
                'code' => 'A.5.1',
            ],
            [
                'id' => Str::uuid(),
                'titre' => 'Politiques de sécurité de l\'information',
                'description' => 'Les politiques de sécurité de l\'information doivent être définies, approuvées par la direction et communiquées à tous les employés.',
                'objectif' => 'Fournir une orientation et un soutien de la direction pour la sécurité de l\'information conformément aux exigences de l\'entreprise.',
                'categorie' => 'Politiques de sécurité',
                'sous_categorie' => 'Gouvernance',
                'niveau_critique' => 'critique',
                'type_controle' => 'preventif',
                'ponderation_defaut' => 10,
                'temps_estime' => 15,
                'conseils_audit' => 'Vérifier l\'existence d\'une politique de sécurité approuvée, sa revue annuelle et la preuve de communication aux employés.',
            ]
        );
        
        // Preuve pour le contrôle Politique
        Preuve::firstOrCreate(
            [
                'controle_id' => $controlePolitique->id,
                'titre' => 'Politique de sécurité de l\'information',
            ],
            [
                'id' => Str::uuid(),
                'type_preuve' => 'document',
                'contenu' => 'La politique de sécurité a été approuvée par le PDG en janvier 2024. Elle couvre les domaines clés : gestion des accès, classification des données, sécurité physique, réponse aux incidents.',
                'source' => '/documents/politique_securite_v2.pdf',
                'est_conforme' => true,
                'est_critique' => true,
                'ordre_affichage' => 1,
                'points_max' => 10,
                'explication_attendu' => 'Une politique de sécurité doit être approuvée par la direction, datée, et couvrir les domaines essentiels de la sécurité.',
            ]
        );
        
        Preuve::firstOrCreate(
            [
                'controle_id' => $controlePolitique->id,
                'titre' => 'Preuve de communication',
            ],
            [
                'id' => Str::uuid(),
                'type_preuve' => 'document',
                'contenu' => 'Email de diffusion de la politique à tous les employés daté du 15/01/2024. Taux d\'ouverture : 78%. 5 employés n\'ont pas accusé réception.',
                'source' => '/logs/email_communication.log',
                'est_conforme' => true,
                'est_critique' => false,
                'ordre_affichage' => 2,
                'points_max' => 5,
                'explication_attendu' => 'La politique doit être communiquée à l\'ensemble des employés avec une preuve de réception.',
            ]
        );
        
        // Contrôle A.9 - Gestion des accès
        $controleAcces = Controle::firstOrCreate(
            [
                'clause_id' => $clause6->id,
                'code' => 'A.9.2.1',
            ],
            [
                'id' => Str::uuid(),
                'titre' => 'Enregistrement et désenregistrement des utilisateurs',
                'description' => 'Un processus formel d\'enregistrement et de désenregistrement des utilisateurs doit être mis en œuvre pour l\'attribution et la suppression des droits d\'accès.',
                'objectif' => 'Assurer que seuls les utilisateurs autorisés ont accès aux systèmes d\'information.',
                'categorie' => 'Contrôle d\'accès',
                'sous_categorie' => 'Gestion des utilisateurs',
                'niveau_critique' => 'critique',
                'type_controle' => 'preventif',
                'ponderation_defaut' => 10,
                'temps_estime' => 20,
                'conseils_audit' => 'Vérifier le processus d\'onboarding/offboarding, examiner les logs de création/suppression de comptes.',
            ]
        );
        
        Preuve::firstOrCreate(
            [
                'controle_id' => $controleAcces->id,
                'titre' => 'Processus d\'onboarding',
            ],
            [
                'id' => Str::uuid(),
                'type_preuve' => 'document',
                'contenu' => 'Un formulaire de demande d\'accès doit être rempli par le manager et approuvé par la DSI. Le compte est créé sous 48h.',
                'source' => '/documents/processus_onboarding.pdf',
                'est_conforme' => true,
                'est_critique' => false,
                'ordre_affichage' => 1,
                'points_max' => 10,
                'explication_attendu' => 'Un processus formel avec validation hiérarchique.',
            ]
        );
        
        Preuve::firstOrCreate(
            [
                'controle_id' => $controleAcces->id,
                'titre' => 'Logs de création de comptes',
            ],
            [
                'id' => Str::uuid(),
                'type_preuve' => 'log',
                'contenu' => 'Logs Active Directory des 6 derniers mois : 15 comptes créés, 8 comptes désactivés. 3 comptes d\'anciens employés toujours actifs.',
                'source' => '/logs/ad_audit.log',
                'est_conforme' => false,
                'est_critique' => true,
                'ordre_affichage' => 2,
                'points_max' => 10,
                'explication_attendu' => 'Tous les comptes d\'anciens employés doivent être désactivés immédiatement après leur départ.',
            ]
        );
        
        // ============================================
        // 6. CRÉATION DE L'AUDIT
        // ============================================
        
        $audit = Audit::firstOrCreate(
            [
                'titre' => 'Audit de conformité ISO 27001 - TechInnov Solutions',
                'auditeur_id' => $apprenant->auditeur->id,
                'entreprise_id' => $entreprise->id,
            ],
            [
                'id' => Str::uuid(),
                'description' => 'Audit de certification initial pour le périmètre : Infrastructure Cloud et Données Clients. Vérification des exigences de l\'ISO 27001 et des contrôles clés de l\'ISO 27002.',
                'date_debut' => now(),
                'date_fin' => now()->addDays(5),
                'status' => 'en_cours',
                'score_total' => 65.5,
                'temps_total' => 7200,
                'commentaire_global' => 'L\'entreprise a mis en place des bases solides mais présente des lacunes dans la gestion des accès et la mise à jour de l\'inventaire.',
                'signature_auditeur' => null,
            ]
        );
        
        // Liaison de l'audit avec les référentiels
        AuditReference::firstOrCreate(
            [
                'audit_id' => $audit->id,
                'reference_id' => $iso27001->id,
            ],
            [
                'id' => Str::uuid(),
                'objectif_specifique' => 'Vérifier la conformité du SMSI aux exigences de l\'ISO 27001',
                'ponderation' => 5,
            ]
        );
        
        AuditReference::firstOrCreate(
            [
                'audit_id' => $audit->id,
                'reference_id' => $iso27002->id,
            ],
            [
                'id' => Str::uuid(),
                'objectif_specifique' => 'Évaluer l\'application des contrôles de sécurité selon l\'ISO 27002',
                'ponderation' => 3,
            ]
        );
        
        AuditReference::firstOrCreate(
            [
                'audit_id' => $audit->id,
                'reference_id' => $iso27005->id,
            ],
            [
                'id' => Str::uuid(),
                'objectif_specifique' => 'Analyser la gestion des risques résiduels',
                'ponderation' => 2,
            ]
        );
        
        // ============================================
        // 7. CRÉATION DES ÉVALUATIONS
        // ============================================
        
        // Évaluation pour Politique de sécurité
        Evaluation::firstOrCreate(
            [
                'audit_id' => $audit->id,
                'controle_id' => $controlePolitique->id,
            ],
            [
                'id' => Str::uuid(),
                'statut' => 'conforme',
                'justification' => 'La politique de sécurité est approuvée, complète et a été communiquée. Le taux de communication est satisfaisant.',
                'recommandation' => 'Organiser une session de rattrapage pour les employés n\'ayant pas accusé réception.',
                'gravite_risque' => null,
                'probabilite_risque' => null,
                'niveau_risque_calcule' => null,
                'preuves_consultees' => json_encode(['Politique de sécurité de l\'information', 'Preuve de communication']),
                'commentaire_formateur' => 'Bon travail, l\'évaluation est correcte.',
                'date_evaluation' => now(),
                'points_obtenus' => 14,
            ]
        );
        
        // Évaluation pour Gestion des accès (non-conforme)
        Evaluation::firstOrCreate(
            [
                'audit_id' => $audit->id,
                'controle_id' => $controleAcces->id,
            ],
            [
                'id' => Str::uuid(),
                'statut' => 'non_conforme',
                'justification' => 'Bien que le processus d\'onboarding soit formel, 3 comptes d\'anciens employés sont toujours actifs dans l\'Active Directory, créant un risque d\'accès non autorisé.',
                'recommandation' => 'Mettre en place une revue mensuelle des comptes utilisateurs et automatiser la désactivation des comptes lors du départ des employés.',
                'gravite_risque' => 'eleve',
                'probabilite_risque' => 'moyen',
                'niveau_risque_calcule' => 'eleve',
                'preuves_consultees' => json_encode(['Processus d\'onboarding', 'Logs de création de comptes']),
                'commentaire_formateur' => 'Bonne identification de la non-conformité. L\'évaluation du risque est appropriée.',
                'date_evaluation' => now(),
                'points_obtenus' => 5,
            ]
        );
        
        $this->command->info('✅ Base de données initialisée avec succès !');
        $this->command->info('📊 Données créées/mises à jour :');
        $this->command->info('   - Utilisateurs : 3');
        $this->command->info('   - Auditeurs : 2');
        $this->command->info('   - Entreprise : 1');
        $this->command->info('   - Référentiels : 3');
        $this->command->info('   - Clauses : 5');
        $this->command->info('   - Contrôles : 4');
        $this->command->info('   - Audit : 1');
    }
}