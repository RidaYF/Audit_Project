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