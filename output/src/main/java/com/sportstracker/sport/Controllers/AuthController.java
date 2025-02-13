package com.sportstracker.sport.Controllers;

import com.sportstracker.sport.Models.Athlete;
import com.sportstracker.sport.Models.Coach;
import com.sportstracker.sport.Models.Utilisateur;
import com.sportstracker.sport.Services.AthleteService;
import com.sportstracker.sport.Services.CoachService;
import com.sportstracker.sport.Services.ObjectifService;
import com.sportstracker.sport.Services.UtilisateurService;
import jakarta.servlet.http.HttpSession;
import org.springframework.beans.factory.annotation.Autowired;
import com.sportstracker.sport.Config.PasswordUtil;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.time.LocalDate;
import java.util.Map;
import java.util.Optional;

@Controller
@RequestMapping("/auth")
public class AuthController {



    @Autowired
    private UtilisateurService utilisateurService;

    @Autowired
    private AthleteService athleteService;

    @Autowired
    private CoachService coachService;

    @Autowired
    private ObjectifService objectifService;

    @Autowired
    private PasswordUtil passwordUtil;

    /**
     * Affiche le formulaire d'inscription pour les utilisateurs (Athlètes et Coachs).
     */
    @GetMapping("/signup")
    public String showSignupForm(Model model) {
        Utilisateur utilisateur = new Utilisateur();
        utilisateur.setRole(Utilisateur.Role.ROLE_ATHLETE); // Définir un rôle par défaut
        model.addAttribute("utilisateur", utilisateur);
        return "auth/signup"; // Vue : auth/signup.html
    }

    /**
     * Inscription d'un utilisateur (athlète ou coach).
     * @param payload Détails de l'utilisateur soumis via le formulaire.
     * @param model Modèle pour les messages d'erreur ou de succès.
     * @return Redirection ou retour à la vue d'inscription avec un message d'erreur.
     */

    @PostMapping("/signup")
    public String signup(@RequestParam Map<String, String> payload, Model model) {
        String role = payload.get("role");
        String identifiant = payload.get("identifiant");
        String motDePasse = payload.get("motDePasse"); // Nouveau champ

        if (role == null || (!role.equals("ROLE_ATHLETE") && !role.equals("ROLE_COACH"))) {
            model.addAttribute("error", "Rôle invalide. Veuillez choisir Athlète ou Coach.");
            return "auth/signup";
        }

        if (identifiant == null || utilisateurService.getUtilisateurByIdentifiant(identifiant).isPresent()) {
            model.addAttribute("error", "L'identifiant est déjà utilisé.");
            return "auth/signup";
        }

        if (motDePasse == null || motDePasse.length() < 6) { // Vérifiez la complexité du mot de passe si nécessaire
            model.addAttribute("error", "Le mot de passe doit contenir au moins 6 caractères.");
            return "auth/signup";
        }

        try {
            // Génération du sel et hachage du mot de passe
            String salt = PasswordUtil.generateSalt();
            String motDePasseHache = PasswordUtil.hashPassword(motDePasse, salt);

            if (role.equals("ROLE_ATHLETE")) {
                Athlete athlete = new Athlete();
                athlete.setIdentifiant(identifiant);
                athlete.setSalt(salt); // Stocker le sel
                athlete.setMotDePasse(motDePasseHache); // Stocker le mot de passe haché
                athlete.setRole(Utilisateur.Role.ROLE_ATHLETE);
                athlete.setNom(payload.get("nom"));

                String dateNaissanceStr = payload.get("dateNaissance");
                if (dateNaissanceStr != null && !dateNaissanceStr.isEmpty()) {
                    athlete.setDateNaissance(LocalDate.parse(dateNaissanceStr));
                }

                //utilisateurService.createUtilisateur(athlete);
                //athleteService.createAthlete(athlete);

                System.out.println("identifiant: " + identifiant);
                System.out.println("nom: " + payload.get("nom"));
                System.out.println("mot de passe: " + payload.get("motDePasse"));
                System.out.println("role: " + payload.get("role"));

                model.addAttribute("success", "Athlète créé avec succès !");
            } else if (role.equals("ROLE_COACH")) {
                Coach coach = new Coach();
                coach.setIdentifiant(identifiant);
                coach.setSalt(salt); // Stocker le sel
                coach.setMotDePasse(motDePasseHache); // Stocker le mot de passe haché
                coach.setRole(Utilisateur.Role.ROLE_COACH);
                coach.setNom(payload.get("nom"));
                coach.setSpecialite(payload.get("specialite"));

                //utilisateurService.createUtilisateur(coach);
                //coachService.createCoach(coach);

                System.out.println("identifiant: " + identifiant);
                System.out.println("nom: " + payload.get("nom"));
                System.out.println("mot de passe: " + payload.get("motDePasse"));
                System.out.println("role: " + payload.get("role"));                model.addAttribute("success", "Coach créé avec succès !");
            }
            return "auth/signin";
        } catch (Exception e) {
            model.addAttribute("error", "Erreur lors de l'inscription : " + e.getMessage());
            return "auth/signup";
        }
    }





    /**
     * Affiche le formulaire de connexion pour les utilisateurs.
     */

    @GetMapping("/signin")
    public String showSigninForm(Model model) {
        model.addAttribute("utilisateur", new Utilisateur());
        return "auth/signin"; // Vue : auth/signin.html
    }


/**
     * Connexion d'un utilisateur.
     * @param payload L'identifiant fourni par le formulaire.
     * @param model Modèle pour afficher les erreurs ou les données utilisateur.
     * @return Redirection ou retour au formulaire avec un message d'erreur.
     */

@PostMapping("/signin")
public String signin(@RequestParam Map<String, String> payload, Model model, HttpSession session) {
    String identifiant = payload.get("identifiant");
    String motDePasse = payload.get("motDePasse"); // Nouveau champ

    Optional<Utilisateur> utilisateurOptional = utilisateurService.getUtilisateurByIdentifiant(identifiant);

    if (utilisateurOptional.isPresent()) {
        Utilisateur utilisateur = utilisateurOptional.get();

        // Vérification du mot de passe
        try {
            String salt = utilisateur.getSalt(); // Récupérer le sel
            String hashedPassword = utilisateur.getMotDePasse(); // Récupérer le mot de passe haché
            boolean isPasswordValid = PasswordUtil.verifyPassword(motDePasse, salt, hashedPassword);

            if (!isPasswordValid) {
                model.addAttribute("error", "Identifiant ou mot de passe incorrect.");
                return "auth/signin";
            }

            // Connexion réussie
            session.setAttribute("user_id", utilisateur.getId());
            if (utilisateur.getRole() == Utilisateur.Role.ROLE_ATHLETE) {
                return "redirect:/athletes/dashboard";
            } else {
                return "redirect:/coaches/dashboard";
            }
        } catch (Exception e) {
            model.addAttribute("error", "Erreur lors de la vérification du mot de passe.");
            return "auth/signin";
        }
    } else {
        model.addAttribute("error", "Identifiant ou mot de passe incorrect.");
        return "auth/signin";
    }
}




}