package com.sportstracker.sport.Repositories;

import com.sportstracker.sport.Models.Utilisateur;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface UtilisateurRepository extends JpaRepository<Utilisateur, Long> {
    Optional<Utilisateur> findByIdentifiant(String identifiant);
    Optional<Utilisateur> findByNom(String nom);
}
