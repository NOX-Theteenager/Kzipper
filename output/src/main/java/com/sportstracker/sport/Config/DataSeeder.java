/*
package com.sportstracker.sport.Config;

import com.sportstracker.sport.Models.*;
import com.sportstracker.sport.Services.*;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.CommandLineRunner;
import org.springframework.stereotype.Component;

import java.security.NoSuchAlgorithmException;
import java.time.LocalDate;
import java.util.List;
import java.util.Random;

@Component
public class DataSeeder implements CommandLineRunner {

    @Autowired
    private UtilisateurService utilisateurService;

    @Autowired
    private CoachService coachService;

    @Autowired
    private AthleteService athleteService;

    @Autowired
    private ObjectifService objectifService;

    @Autowired
    private PerformanceService performanceService;

    @Override
    public void run(String... args) throws Exception {
        // Générer des utilisateurs (Coachs et Athlètes)
        generateUsers(50); // Crée 50 utilisateurs

        // Générer des objectifs
        generateObjectives(30); // Crée 30 objectifs

        // Générer des performances
        generatePerformances(100); // Crée 100 performances
    }

    private void generateUsers(int count) throws NoSuchAlgorithmException {
        Random random = new Random();

        for (int i = 0; i < count; i++) {
            String identifiant = "user" + (i + 1);
            String password = "password" + (i + 1);
            String salt = PasswordUtil.generateSalt();
            String hashedPassword = PasswordUtil.hashPassword(password, salt);
            String nom = "Nom " + (i + 1);

            // Déterminer le rôle (Coach ou Athlète)
            Utilisateur.Role role = (i % 2 == 0) ? Utilisateur.Role.ROLE_ATHLETE : Utilisateur.Role.ROLE_COACH;

            Utilisateur utilisateur;
            if (role == Utilisateur.Role.ROLE_ATHLETE) {
                Athlete athlete = new Athlete();
                athlete.setIdentifiant(identifiant);
                athlete.setMotDePasse(hashedPassword);
                athlete.setSalt(salt);
                athlete.setNom(nom);
                athlete.setRole(role);
                athlete.setDateNaissance(LocalDate.of(random.nextInt(30) + 1990, random.nextInt(12) + 1, random.nextInt(28) + 1));
                utilisateurService.createUtilisateur(athlete);
                athleteService.createAthlete(athlete);
            } else {
                Coach coach = new Coach();
                coach.setIdentifiant(identifiant);
                coach.setMotDePasse(hashedPassword);
                coach.setSalt(salt);
                coach.setNom(nom);
                coach.setRole(role);
                coach.setSpecialite("Spécialité " + (i + 1));
                utilisateurService.createUtilisateur(coach);
                coachService.createCoach(coach);
            }
        }
    }

    private void generateObjectives(int count) {
        Random random = new Random();

        // Créer des objectifs pour chaque coach
        List<Coach> coaches = coachService.getAllCoaches(); // Récupérer tous les coachs existants

        for (int i = 0; i < count; i++) {
            Coach coach = coaches.get(random.nextInt(coaches.size()));
            Objectif objectif = new Objectif();
            objectif.setTitre("Objectif " + (i + 1));
            objectif.setDescription("Description de l'objectif " + (i + 1));
            objectif.setDateEcheance(LocalDate.of(random.nextInt(2) + 2024, random.nextInt(12) + 1, random.nextInt(28) + 1));
            objectif.setCoach(coach);

            // Créer l'objectif et l'associer au coach
            objectifService.createObjectif(coach.getId(), objectif);
        }
    }

    private void generatePerformances(int count) {
        Random random = new Random();

        // Créer des performances pour chaque athlète
        List<Athlete> athletes = athleteService.getAllAthletes(); // Récupérer tous les athlètes existants

        for (int i = 0; i < count; i++) {
            Athlete athlete = athletes.get(random.nextInt(athletes.size()));

            // Associer un athlète à un objectif donné par son coach
            Coach coach = athlete.getCoach();
            if (coach == null) continue;  // Si l'athlète n'a pas de coach, passer à l'athlète suivant

            // Récupérer les objectifs associés à ce coach
            List<Objectif> objectifs = objectifService.getObjectifsByCoachId(coach.getId());  // Méthode à implémenter dans `objectifService`
            if (objectifs.isEmpty()) continue; // Si aucun objectif n'est associé au coach, passer à l'athlète suivant

            // Sélectionner un objectif au hasard parmi ceux assignés au coach
            Objectif objectif = objectifs.get(random.nextInt(objectifs.size()));

            // Créer la performance
            Performance performance = new Performance();
            performance.setType("Type de performance " + (i + 1));
            performance.setDate(LocalDate.of(random.nextInt(2) + 2024, random.nextInt(12) + 1, random.nextInt(28) + 1));
            performance.setValeur(random.nextDouble() * 100); // Valeur de performance entre 0 et 100

            // Associer un athlète et un objectif à la performance
            performance.setAthlete(athlete);
            performance.setObjectif(objectif);

            // Sauvegarder la performance
            performanceService.createPerformance(performance);
        }
    }
}
*/
