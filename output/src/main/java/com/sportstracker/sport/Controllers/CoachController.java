package com.sportstracker.sport.Controllers;

import com.sportstracker.sport.Models.Athlete;
import com.sportstracker.sport.Models.Coach;
import com.sportstracker.sport.Models.Objectif;
import com.sportstracker.sport.Services.AthleteService;
import com.sportstracker.sport.Services.CoachService;
import com.sportstracker.sport.Services.ObjectifService;
import com.sportstracker.sport.Services.UtilisateurService;
import jakarta.servlet.http.HttpSession;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.Enumeration;
import java.util.List;

@Controller
@RequestMapping("/coaches")
public class CoachController {

    @Autowired
    private CoachService coachService;

    @Autowired
    private ObjectifService objectifService;

    @Autowired
    private AthleteService athleteService;

    @Autowired
    private UtilisateurService utilisateurService;

    @GetMapping
    public String getAllCoaches(Model model) {
        List<Coach> coaches = coachService.getAllCoaches();
        model.addAttribute("coaches", coaches);
        return "coaches/list"; // Vue : coaches/list.html
    }

    @GetMapping("/{id}")
    public String getCoachById(@PathVariable Long id, Model model) {
        Coach coach = coachService.getCoachById(id);
        model.addAttribute("coach", coach);
        return "coaches/detail"; // Vue : coaches/detail.html
    }

    @GetMapping("/edit")
    public String editCoach(Model model, HttpSession session) {
        Coach coach = coachService.getCoachById((Long) session.getAttribute("user_id")); // Récupérer le coach depuis le service
        model.addAttribute("coach", coach);
        return "coaches/edit"; // Chemin vers le fichier Thymeleaf
    }

    @PostMapping("/update")
    public String updateCoach(@ModelAttribute("coach") Coach coach) {
        coachService.updateCoach(coach.getId(), coach); // Mise à jour des données du coach
        return "redirect:/coaches/dashboard"; // Redirection vers la liste des coaches
    }

    @PostMapping("/{id}")
    public String updateCoach(@PathVariable Long id, @ModelAttribute Coach coach) {
        coachService.updateCoach(id, coach);
        return "redirect:/coaches"; // Redirection vers la liste
    }

    @PostMapping("/{id}/delete")
    public String deleteCoach(@PathVariable Long id) {
        coachService.deleteCoach(id);
        return "redirect:/coaches"; // Redirection vers la liste
    }

    // Gestion des objectifs d'un coach
    @GetMapping("/{coachId}/objectifs")
    public String getObjectifsByCoachId(@PathVariable Long coachId, Model model) {
        List<Objectif> objectifs = objectifService.getObjectifsByCoachId(coachId);
        model.addAttribute("objectifs", objectifs);
        return "objectifs/list"; // Vue : objectifs/list.html
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
            return "redirect:/auth/signin";
        }

        Coach coach = coachService.getCoachById(user_id);
        List<Objectif> objectifs = objectifService.getObjectifsByCoachId(user_id);
        List<Athlete> athletes = athleteService.getAthletesByCoachId(user_id);

        model.addAttribute("coach", coach);
        model.addAttribute("objectifs", objectifs);
        model.addAttribute("athletes", athletes);

        return "coaches/dashboard";
    }

    @GetMapping("/logout")
    public String logout(HttpSession session) {
        // Invalider la session
        session.invalidate();
        return "redirect:/login"; // Redirection après déconnexion
    }

    // -----------------------------------------------------------------------------------------------------------------
    // ------------------------------------------------Athletes---------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

    @PostMapping("/athletes/add")
    public String addAthlete(Model model, HttpSession session, @RequestParam("athleteId") Long athlete_id){
        Long coach_id = (Long) session.getAttribute("user_id");
        if(coach_id == null){
            return "redirect:/auth/signin";
        }

        athleteService.updateCoach(athlete_id, coachService.getCoachById(coach_id));

        return "redirect:/coaches/athletes";
    }

    @PostMapping("/athletes/remove")
    public String removeAthlete(Model model, HttpSession session, @RequestParam("athleteId") Long athlete_id){
        Long coach_id = (Long) session.getAttribute("user_id");
        if(coach_id == null){
            return "redirect:/auth/signin";
        }

        athleteService.removeCoach(athlete_id);

        return "redirect:/coaches/athletes";
    }

    @GetMapping("/athletes")
    public String getAthletes(Model model, HttpSession session){
        Long coach_id = (Long) session.getAttribute("user_id");
        if(coach_id == null){
            return "redirect:/auth/signin";
        }

        List<Athlete> allAthletes = athleteService.getAthletesWithoutCoach();
        List<Athlete> assignedAthletes = athleteService.getAthletesByCoachId(coach_id);
        model.addAttribute("allAthletes", allAthletes);
        model.addAttribute("assignedAthletes", assignedAthletes);

        return "coaches/athletes";
    }
}
