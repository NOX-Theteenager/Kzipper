package com.sportstracker.sport.Controllers;

import jakarta.servlet.http.HttpSession;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class HomeController {

    @GetMapping("/")
    public String home(Model model) {
        model.addAttribute("message", "Hello, Thymeleaf!");

        return "home"; // Renvoie vers le template home.html
    }

    @GetMapping("/logout")
    public String logout(HttpSession session) {
        // Invalider la session
        session.invalidate();
        return "redirect:/auth/signin"; // Redirection après déconnexion
    }
}
