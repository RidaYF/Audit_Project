<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reference;
use App\Models\Clause;
use App\Models\Controle;
use App\Models\Utilisateur;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class IsoMasterDataSeeder extends Seeder
{
    public function run()
    {
        // Récupérer l'utilisateur système
        $systemUser = Utilisateur::where('email', 'system@iso-audit.local')->first();
        
        if (!$systemUser) {
            $systemUser = Utilisateur::create([
                'id' => Str::uuid(),
                'username' => 'system',
                'email' => 'system@iso-audit.local',
                'password_hash' => bcrypt("password123"), // Mot de passe sécurisé pour l'utilisateur système
                'role' => 'admin',
                'niveau' => 'expert',
                'progression_globale' => 100,
                'is_active' => true,
                'date_inscription' => now(),
            ]);
            $this->command->info('✅ Utilisateur système créé');
        }
        
        // Simuler l'authentification
        Auth::login($systemUser);
        
        // 1. Créer les références
        $references = [
            [
                'code' => 'ISO27001',
                'nom' => 'ISO/IEC 27001:2022',
                'description' => 'Système de Management de la Sécurité de l\'Information (SMSI)',
                'type_norme' => 'management',
                'version' => '2022'
            ],
            [
                'code' => 'ISO27002',
                'nom' => 'ISO/IEC 27002:2022',
                'description' => 'Code de bonnes pratiques pour les contrôles de sécurité',
                'type_norme' => 'control',
                'version' => '2022'
            ],
            [
                'code' => 'ISO27005',
                'nom' => 'ISO/IEC 27005:2022',
                'description' => 'Gestion des risques de sécurité de l\'information',
                'type_norme' => 'risk',
                'version' => '2022'
            ]
        ];
        
        foreach ($references as $ref) {
            $existingRef = Reference::where('code', $ref['code'])->first();
            
            if (!$existingRef) {
                Reference::create([
                    'id' => Str::uuid(),
                    'code' => $ref['code'],
                    'nom' => $ref['nom'],
                    'description' => $ref['description'],
                    'type_norme' => $ref['type_norme'],
                    'version' => $ref['version'],
                    'is_active' => true,
                    'created_by' => $systemUser->id,
                    'updated_by' => $systemUser->id,
                ]);
                $this->command->info("✅ Référence {$ref['code']} créée");
            } else {
                $this->command->info("ℹ️ Référence {$ref['code']} existe déjà");
            }
        }
        
        // 2. Créer les clauses ISO 27001
        $iso27001 = Reference::where('code', 'ISO27001')->first();
        
        if ($iso27001) {
            $clauses = [
                [
                    'numero' => '4', 
                    'titre' => 'Contexte de l\'organisation', 
                    'description' => 'Comprendre l\'organisation et son contexte, les besoins des parties intéressées, déterminer le domaine d\'application du SMSI', 
                    'niveau_importance' => 'critique', 
                    'ordre' => 4,
                    'niveau' => 1,
                    'chemin_complet' => '4. Contexte de l\'organisation'
                ],
                [
                    'numero' => '5', 
                    'titre' => 'Leadership', 
                    'description' => 'Engagement de la direction, politique, rôles et responsabilités', 
                    'niveau_importance' => 'critique', 
                    'ordre' => 5,
                    'niveau' => 1,
                    'chemin_complet' => '5. Leadership'
                ],
                [
                    'numero' => '6', 
                    'titre' => 'Planification', 
                    'description' => 'Actions pour traiter les risques et opportunités, objectifs du SMSI', 
                    'niveau_importance' => 'eleve', 
                    'ordre' => 6,
                    'niveau' => 1,
                    'chemin_complet' => '6. Planification'
                ],
                [
                    'numero' => '7', 
                    'titre' => 'Support', 
                    'description' => 'Ressources, compétences, sensibilisation, communication, documentation', 
                    'niveau_importance' => 'eleve', 
                    'ordre' => 7,
                    'niveau' => 1,
                    'chemin_complet' => '7. Support'
                ],
                [
                    'numero' => '8', 
                    'titre' => 'Fonctionnement', 
                    'description' => 'Planification et contrôle opérationnels, évaluation des risques, traitement des risques', 
                    'niveau_importance' => 'critique', 
                    'ordre' => 8,
                    'niveau' => 1,
                    'chemin_complet' => '8. Fonctionnement'
                ],
                [
                    'numero' => '9', 
                    'titre' => 'Évaluation de la performance', 
                    'description' => 'Suivi, mesure, analyse, évaluation, audit interne, revue de direction', 
                    'niveau_importance' => 'eleve', 
                    'ordre' => 9,
                    'niveau' => 1,
                    'chemin_complet' => '9. Évaluation de la performance'
                ],
                [
                    'numero' => '10', 
                    'titre' => 'Amélioration', 
                    'description' => 'Non-conformité, action corrective, amélioration continue', 
                    'niveau_importance' => 'eleve', 
                    'ordre' => 10,
                    'niveau' => 1,
                    'chemin_complet' => '10. Amélioration'
                ],
            ];
            
            $clauseCount = 0;
            foreach ($clauses as $clause) {
                $existingClause = Clause::where('reference_id', $iso27001->id)
                    ->where('numero', $clause['numero'])
                    ->first();
                
                if (!$existingClause) {
                    Clause::create([
                        'id' => Str::uuid(),
                        'reference_id' => $iso27001->id,
                        'numero' => $clause['numero'],
                        'titre' => $clause['titre'],
                        'description' => $clause['description'],
                        'niveau_importance' => $clause['niveau_importance'],
                        'ordre_affichage' => $clause['ordre'],
                        'niveau' => $clause['niveau'],
                        'chemin_complet' => $clause['chemin_complet'],
                        'created_by' => $systemUser->id,
                        'updated_by' => $systemUser->id,
                    ]);
                    $clauseCount++;
                }
            }
            
            $this->command->info("✅ Clauses ISO 27001: $clauseCount créées");
        }
        
        // 3. Créer les contrôles ISO 27002 (sans is_active)
        $iso27002 = Reference::where('code', 'ISO27002')->first();
        
        if ($iso27002) {
            $controls = [
                [
                    'code' => 'A.5.1',
                    'titre' => 'Politique de sécurité de l\'information',
                    'description' => 'La politique de sécurité de l\'information doit être définie, approuvée par la direction et communiquée à tous les employés',
                    'objectif' => 'Fournir une orientation et un soutien de la direction pour la sécurité de l\'information',
                    'categorie' => 'Politiques',
                    'niveau_critique' => 'critique',
                    'type_controle' => 'preventif',
                    'ponderation_defaut' => 1,
                    'temps_estime' => 30,
                    'conseils_audit' => "Vérifier l'existence et l'application de cette politique/contrôle"
                ],
                [
                    'code' => 'A.5.2',
                    'titre' => 'Rôles et responsabilités',
                    'description' => 'Les rôles et responsabilités en matière de sécurité de l\'information doivent être définis et attribués',
                    'objectif' => 'Assurer une répartition claire des responsabilités',
                    'categorie' => 'Organisation',
                    'niveau_critique' => 'eleve',
                    'type_controle' => 'preventif',
                    'ponderation_defaut' => 1,
                    'temps_estime' => 30,
                    'conseils_audit' => "Vérifier l'existence et l'application de cette politique/contrôle"
                ],
                [
                    'code' => 'A.6.1',
                    'titre' => 'Organisation interne',
                    'description' => 'Les responsabilités en matière de sécurité de l\'information doivent être définies et attribuées',
                    'objectif' => 'Établir une structure de gestion pour la sécurité de l\'information',
                    'categorie' => 'Organisation',
                    'niveau_critique' => 'eleve',
                    'type_controle' => 'preventif',
                    'ponderation_defaut' => 1,
                    'temps_estime' => 30,
                    'conseils_audit' => "Vérifier l'existence et l'application de cette politique/contrôle"
                ],
                [
                    'code' => 'A.8.1',
                    'titre' => 'Gestion des actifs',
                    'description' => 'Les actifs doivent être identifiés et gérés de manière appropriée',
                    'objectif' => 'Identifier et gérer les actifs de l\'organisation',
                    'categorie' => 'Gestion des actifs',
                    'niveau_critique' => 'eleve',
                    'type_controle' => 'detectif',
                    'ponderation_defaut' => 1,
                    'temps_estime' => 30,
                    'conseils_audit' => "Vérifier l'existence et l'application de cette politique/contrôle"
                ],
            ];
            
            $controlCount = 0;
            foreach ($controls as $control) {
                $existingControl = Controle::where('code', $control['code'])->first();
                
                if (!$existingControl) {
                    // Trouver une clause de référence
                    $clause = Clause::where('reference_id', $iso27002->id)->first();
                    
                    Controle::create([
                        'id' => Str::uuid(),
                        'clause_id' => $clause ? $clause->id : null,
                        'code' => $control['code'],
                        'titre' => $control['titre'],
                        'description' => $control['description'],
                        'objectif' => $control['objectif'],
                        'categorie' => $control['categorie'],
                        'niveau_critique' => $control['niveau_critique'],
                        'type_controle' => $control['type_controle'],
                        'ponderation_defaut' => $control['ponderation_defaut'],
                        'temps_estime' => $control['temps_estime'],
                        'conseils_audit' => $control['conseils_audit'],
                        'created_by' => $systemUser->id,
                        'updated_by' => $systemUser->id,
                    ]);
                    $controlCount++;
                }
            }
            
            $this->command->info("✅ Contrôles ISO 27002: $controlCount créés");
        }
        
        // 4. Créer les méthodes ISO 27005
        $iso27005 = Reference::where('code', 'ISO27005')->first();
        
        if ($iso27005) {
            $riskMethods = [
                [
                    'code' => 'RM-01',
                    'titre' => 'Analyse qualitative des risques',
                    'description' => 'Évaluation des risques basée sur des catégories (faible, moyen, élevé)',
                    'objectif' => 'Identifier et prioriser les risques selon leur impact et probabilité estimés',
                    'categorie' => 'Analyse qualitative',
                    'niveau_critique' => 'eleve',
                    'type_controle' => 'analyse',
                    'ponderation_defaut' => 2,
                    'temps_estime' => 60,
                    'conseils_audit' => "Vérifier l'application et la pertinence de la méthode d'analyse"
                ],
                [
                    'code' => 'RM-02',
                    'titre' => 'Analyse quantitative des risques',
                    'description' => 'Évaluation des risques basée sur des valeurs numériques et financières',
                    'objectif' => 'Mesurer précisément l\'impact financier des risques',
                    'categorie' => 'Analyse quantitative',
                    'niveau_critique' => 'eleve',
                    'type_controle' => 'analyse',
                    'ponderation_defaut' => 2,
                    'temps_estime' => 60,
                    'conseils_audit' => "Vérifier l'application et la pertinence de la méthode d'analyse"
                ],
            ];
            
            $methodCount = 0;
            foreach ($riskMethods as $method) {
                $existingMethod = Controle::where('code', $method['code'])->first();
                
                if (!$existingMethod) {
                    Controle::create([
                        'id' => Str::uuid(),
                        'clause_id' => null,
                        'code' => $method['code'],
                        'titre' => $method['titre'],
                        'description' => $method['description'],
                        'objectif' => $method['objectif'],
                        'categorie' => $method['categorie'],
                        'niveau_critique' => $method['niveau_critique'],
                        'type_controle' => $method['type_controle'],
                        'ponderation_defaut' => $method['ponderation_defaut'],
                        'temps_estime' => $method['temps_estime'],
                        'conseils_audit' => $method['conseils_audit'],
                        'created_by' => $systemUser->id,
                        'updated_by' => $systemUser->id,
                    ]);
                    $methodCount++;
                }
            }
            
            $this->command->info("✅ Méthodes ISO 27005: $methodCount créées");
        }
        
        // Déconnecter l'utilisateur système
        Auth::logout();
        
        // Récapitulatif
        $this->command->info("\n" . str_repeat('=', 50));
        $this->command->info("📊 RÉCAPITULATIF DES DONNÉES MAÎTRES");
        $this->command->info(str_repeat('=', 50));
        $this->command->info("Références: " . Reference::count());
        $this->command->info("Clauses ISO 27001: " . Clause::where('reference_id', $iso27001?->id)->count());
        $this->command->info("Contrôles ISO 27002: " . Controle::where('code', 'LIKE', 'A.%')->count());
        $this->command->info("Méthodes ISO 27005: " . Controle::where('code', 'LIKE', 'RM-%')->count());
        $this->command->info(str_repeat('=', 50));
        $this->command->info("✅ Données maîtres ISO initialisées avec succès !");
    }
}