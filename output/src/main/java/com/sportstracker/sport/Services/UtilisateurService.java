package com.sportstracker.sport.Services;

import com.sportstracker.sport.Models.Utilisateur;
import com.sportstracker.sport.Repositories.UtilisateurRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.Optional;
import java.util.List;

@Service
public class UtilisateurService {

    @Autowired
    private UtilisateurRepository utilisateurRepository;

    public List<Utilisateur> getAllUtilisateurs() {
        return utilisateurRepository.findAll();
    }

    public Utilisateur getUtilisateurById(Long id) {
        return utilisateurRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Utilisateur non trouvé avec l'ID : " + id));
    }

    public Utilisateur createUtilisateur(Utilisateur utilisateur) {
        return utilisateurRepository.save(utilisateur);
    }

    public Utilisateur updateUtilisateur(Long id, Utilisateur utilisateurDetails) {
        Utilisateur utilisateur = getUtilisateurById(id);
        utilisateur.setNom(utilisateurDetails.getNom());
        utilisateur.setIdentifiant(utilisateurDetails.getIdentifiant());
        return utilisateurRepository.save(utilisateur);
    }

    public void deleteUtilisateur(Long id) {
        Utilisateur utilisateur = getUtilisateurById(id);
        utilisateur.setIsDeleted(true);
        utilisateurRepository.save(utilisateur);
    }

    public Optional<Utilisateur> getUtilisateurByIdentifiant(String identifiant) {
        return utilisateurRepository.findByIdentifiant(identifiant);
    }
}
