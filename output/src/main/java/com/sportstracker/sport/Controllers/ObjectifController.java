package com.sportstracker.sport.Controllers;

import com.sportstracker.sport.Models.Objectif;
import com.sportstracker.sport.Services.ObjectifService;
import jakarta.servlet.http.HttpSession;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@Controller
@RequestMapping("/coaches/objectifs")
public class ObjectifController {

    @Autowired
    private ObjectifService objectifService;

    /**
     * Affiche la liste des objectifs pour un coach donné (basé sur la session).
     */
    @GetMapping
    public String listObjectives(HttpSession session, Model model) {
        Long coachId = (Long) session.getAttribute("user_id");
        if (coachId == null) {
            model.addAttribute("error", "Vous devez être connecté pour voir vos objectifs.");
            return "/auth/signin"; // Rediriger vers la page de connexion si non connecté
        }
        List<Objectif> objectifs = objectifService.getObjectifsByCoachId(coachId);
        model.addAttribute("objectifs", objectifs);
        return "/coaches/objectifs"; // Vue : objectifs/list.html
    }

    /**
     * Crée un nouvel objectif pour le coach connecté.
     */
    @PostMapping("/create")
    public String createObjective(@ModelAttribute Objectif objectif, HttpSession session, Model model) {
        Long coachId = (Long) session.getAttribute("user_id");
        if (coachId == null) {
            model.addAttribute("error", "Vous devez être connecté pour ajouter un objectif.");
            return "/auth/signin"; // Rediriger vers la page de connexion si non connecté
        }

        try {
            objectifService.createObjectif(coachId, objectif);
            return "redirect:/coaches/objectifs"; // Redirection vers la liste des objectifs
        } catch (Exception e) {
            model.addAttribute("error", "Erreur lors de la création de l'objectif : " + e.getMessage());
            return "/coaches/objectifs"; // Retourner au formulaire en cas d'erreur
        }
    }

    /**
     * Met à jour un objectif existant pour le coach connecté.
     */
    @PostMapping("/{id}/update")
    public String updateObjective(@PathVariable Long id, @ModelAttribute Objectif objectif, HttpSession session, Model model) {
        Long coachId = (Long) session.getAttribute("user_id");
        if (coachId == null) {
            model.addAttribute("error", "Vous devez être connecté pour modifier un objectif.");
            return "/auth/signin";
        }

        try {
            objectifService.updateObjectif(id, objectif);
            return "redirect:/coaches/objectifs"; // Redirection vers la liste des objectifs
        } catch (Exception e) {
            model.addAttribute("error", "Erreur lors de la mise à jour de l'objectif : " + e.getMessage());
            return "/coaches/objectifs";
        }
    }

    /**
     * Supprime un objectif pour le coach connecté.
     */
    @PostMapping("/{id}/delete")
    public String deleteObjective(@PathVariable Long id, HttpSession session, Model model) {
        Long coachId = (Long) session.getAttribute("user_id");
        if (coachId == null) {
            model.addAttribute("error", "Vous devez être connecté pour supprimer un objectif.");
            return "/auth/signin";
        }

        try {
            objectifService.deleteObjectif(id);
            return "redirect:/coaches/objectifs"; // Redirection vers la liste des objectifs
        } catch (Exception e) {
            model.addAttribute("error", "Erreur lors de la suppression de l'objectif : " + e.getMessage());
            return "error"; // Vue : error.html pour afficher un message d'erreur
        }
    }
}
