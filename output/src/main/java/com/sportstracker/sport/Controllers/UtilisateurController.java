package com.sportstracker.sport.Controllers;

import com.sportstracker.sport.Models.Utilisateur;
import com.sportstracker.sport.Services.UtilisateurService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@Controller
@RequestMapping("/utilisateurs")
public class UtilisateurController {

    @Autowired
    private UtilisateurService utilisateurService;

    @GetMapping
    public String getAllUtilisateurs(Model model) {
        List<Utilisateur> utilisateurs = utilisateurService.getAllUtilisateurs();
        model.addAttribute("utilisateurs", utilisateurs);
        return "utilisateurs/list"; // Vue : utilisateurs/list.html
    }

    @GetMapping("/{id}")
    public String getUtilisateurById(@PathVariable Long id, Model model) {
        Utilisateur utilisateur = utilisateurService.getUtilisateurById(id);
        model.addAttribute("utilisateur", utilisateur);
        return "utilisateurs/detail"; // Vue : utilisateurs/detail.html
    }

    @GetMapping("/new")
    public String showCreateForm(Model model) {
        model.addAttribute("utilisateur", new Utilisateur());
        return "utilisateurs/form"; // Vue : utilisateurs/form.html
    }

    @PostMapping
    public String createUtilisateur(@ModelAttribute Utilisateur utilisateur) {
        utilisateurService.createUtilisateur(utilisateur);
        return "redirect:/utilisateurs"; // Redirection vers la liste
    }

    @GetMapping("/{id}/edit")
    public String showUpdateForm(@PathVariable Long id, Model model) {
        Utilisateur utilisateur = utilisateurService.getUtilisateurById(id);
        model.addAttribute("utilisateur", utilisateur);
        return "utilisateurs/form"; // Vue : utilisateurs/form.html
    }

    @PostMapping("/{id}")
    public String updateUtilisateur(@PathVariable Long id, @ModelAttribute Utilisateur utilisateur) {
        utilisateurService.updateUtilisateur(id, utilisateur);
        return "redirect:/utilisateurs"; // Redirection vers la liste
    }

    @PostMapping("/{id}/delete")
    public String deleteUtilisateur(@PathVariable Long id) {
        utilisateurService.deleteUtilisateur(id);
        return "redirect:/utilisateurs"; // Redirection vers la liste
    }
}
