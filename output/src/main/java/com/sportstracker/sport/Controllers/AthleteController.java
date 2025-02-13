package com.sportstracker.sport.Controllers;

import com.sportstracker.sport.Models.Athlete;
import com.sportstracker.sport.Models.Coach;
import com.sportstracker.sport.Models.Objectif;
import com.sportstracker.sport.Models.Performance;
import com.sportstracker.sport.Services.AthleteService;
import com.sportstracker.sport.Services.ObjectifService;
import com.sportstracker.sport.Services.PerformanceService;
import jakarta.servlet.http.HttpSession;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.time.LocalDate;
import java.util.*;

@Controller
@RequestMapping("/athletes")
public class AthleteController {

    @Autowired
    private AthleteService athleteService;

    @Autowired
    private ObjectifService objectifService;

    @Autowired
    private PerformanceService performanceService;

    // Affiche tous les athlètes
    @GetMapping
    public String getAllAthletes(Model model) {
        List<Athlete> athletes = athleteService.getAllAthletes();
        model.addAttribute("athletes", athletes);
        return "athletes/list"; // Vue : athletes/list.html
    }

    // Affiche les détails d'un athlète
    @GetMapping("/{id}")
    public String getAthleteById(@PathVariable Long id, Model model) {
        Athlete athlete = athleteService.getAthleteById(id);
        model.addAttribute("athlete", athlete);
        return "athletes/detail"; // Vue : athletes/detail.html
    }

    // Affiche le formulaire de création d'un athlète
    @GetMapping("/new")
    public String showCreateForm(Model model) {
        model.addAttribute("athlete", new Athlete());
        return "athletes/form"; // Vue : athletes/form.html
    }

    @GetMapping("/edit")
    public String ShowEdit(Model model, HttpSession session){
        Athlete athlete = athleteService.getAthleteById( (Long) session.getAttribute("user_id"));
        List<Objectif> objectifs = athlete.getCoach() != null ? objectifService.getObjectifsByCoachId(athlete.getCoach().getId()) : Collections.emptyList();
        List<Performance> performances = performanceService.getPerformancesByAthleteId( (Long) session.getAttribute("user_id"));

        model.addAttribute("athlete", athlete);
        model.addAttribute("objectifs", objectifs);
        model.addAttribute("performances", performances);

        return "athletes/edit";
    }

    // Crée un nouvel athlète
    @PostMapping
    public String createAthlete(@ModelAttribute Athlete athlete) {
        athleteService.createAthlete(athlete);
        return "redirect:/athletes"; // Redirection vers la liste des athlètes
    }

    @PostMapping("/update")
    public String updateCoach(@ModelAttribute("coach") Athlete athlete) {
        athleteService.updateAthlete(athlete.getId(), athlete); // Mise à jour des données du coach
        return "redirect:/athletes/dashboard"; // Redirection vers la liste des coaches
    }

    // Met à jour les informations d'un athlète
    @PostMapping("/{id}")
    public String updateAthlete(@PathVariable Long id, @ModelAttribute Athlete athlete) {
        athleteService.updateAthlete(id, athlete);
        return "redirect:/athletes"; // Redirection vers la liste des athlètes
    }

    // Supprime un athlète
    @PostMapping("/{id}/delete")
    public String deleteAthlete(@PathVariable Long id) {
        athleteService.deleteAthlete(id);
        return "redirect:/athletes"; // Redirection vers la liste des athlètes
    }

    @GetMapping("/dashboard")
    public String displayDashboard(Model model, HttpSession session) {
        Long user_id = (Long) session.getAttribute("user_id");
        Enumeration<String> attributeNames = session.getAttributeNames();
        while (attributeNames.hasMoreElements()) {
            String attributeName = attributeNames.nextElement();
            System.out.println("Attribute: " + attributeName + " Value: " + session.getAttribute(attributeName));
        }

        if (user_id == null) {
            model.addAttribute("error", "connectez vous pour acceder a votre dashboard");
            return "redirect:/auth/signin";
        }

        Athlete athlete = athleteService.getAthleteById(user_id);
        List<Objectif> objectifs = athlete.getCoach() != null ? objectifService.getObjectifsByCoachId(athlete.getCoach().getId()) : Collections.emptyList();
        List<Performance> performances = performanceService.getPerformancesByAthleteId(user_id);

        model.addAttribute("athlete", athlete);
        model.addAttribute("objectifs", objectifs);
        model.addAttribute("performances", performances);

        return "athletes/dashboard";
    }

    @GetMapping("/objectifs")
    public String getObjectifs(Model model, HttpSession session){
        Long user_id = (Long) session.getAttribute("user_id");

        if (user_id == null) {
            model.addAttribute("error", "connectez vous pour acceder a vos objectifs");
            return "redirect:/auth/signin";
        }

        Athlete athlete = athleteService.getAthleteById(user_id);
        List<Objectif> objectifs = athlete.getCoach() != null ? objectifService.getObjectifsByCoachId(athlete.getCoach().getId()) : Collections.emptyList();
        List<Performance> performances = athlete.getPerformances();

        model.addAttribute("athlete", athlete);
        model.addAttribute("objectifs", objectifs);
        model.addAttribute("performances", performances);

        return "athletes/objectifs";
    }


    // -----------------------------------------------------------------------------------------------------------------
    // ------------------------------------------------Performances---------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

    @GetMapping("/performances")
    public String getPerformances(Model model, HttpSession session){
        Long user_id = (Long) session.getAttribute("user_id");

        if (user_id == null) {
            model.addAttribute("error", "connectez vous pour acceder a vos performances");
            return "redirect:/auth/signin";
        }

        List<Performance> performances = performanceService.getPerformancesByAthleteId(user_id);
        Athlete athlete = athleteService.getAthleteById(user_id);
        List<Objectif> objectifs = athlete.getCoach() != null ? objectifService.getObjectifsByCoachId(athlete.getCoach().getId()) : Collections.emptyList();
        model.addAttribute("athlete", athlete);
        model.addAttribute("performances", performances);
        model.addAttribute("objectifs", objectifs);

        return "athletes/performances";
    }

    @PostMapping("/performances/new")
    public String addPerformance(@RequestParam Double valeur,
                                 @RequestParam String type,
                                 @RequestParam LocalDate date,
                                 @RequestParam Long objectifId,
                                 Model model,
                                 HttpSession session) {
        Long athlete_id = (Long) session.getAttribute("user_id");
        if (athlete_id == null) {
            model.addAttribute("error", "connectez vous pour ajouter une performance");
            return "redirect:/auth/signin";
        }
        Performance performance = new Performance();
        performance.setDate(date);
        performance.setType(type);
        performance.setValeur(valeur);
        performanceService.createPerformance(athlete_id, objectifId, performance);
        return "redirect:/athletes/performances";
    }

    @PostMapping("/performances/{id}/update")
    public String updatePerformance(@RequestParam Double valeur,
                                 @RequestParam String type,
                                 @RequestParam LocalDate date,
                                 @RequestParam Long objectifId,
                                 @PathVariable Long id,
                                 Model model,
                                 HttpSession session) {
        Long athlete_id = (Long) session.getAttribute("user_id");
        if (athlete_id == null) {
            model.addAttribute("error", "connectez vous pour modifier vos performances");
            return "redirect:/auth/signin";
        }
        Performance performance = new Performance();
        performance.setDate(date);
        performance.setType(type);
        performance.setValeur(valeur);
        performanceService.updatePerformance(id, performance);
        return "redirect:/athletes/performances";
    }

    @PostMapping("/performances/{id}/delete")
    public String deletePerformance(@PathVariable Long id, HttpSession session, Model model) {
        Long coachId = (Long) session.getAttribute("user_id");
        if (coachId == null) {
            model.addAttribute("error", "Vous devez être connecté pour supprimer une performance.");
            return "/auth/signin";
        }

        try {
            performanceService.deletePerformance(id);
            return "redirect:/athletes/performances"; // Redirection vers la liste des objectifs
        } catch (Exception e) {
            model.addAttribute("error", "Erreur lors de la suppression de la performance : " + e.getMessage());
            return "error"; // Vue : error.html pour afficher un message d'erreur
        }
    }



}
