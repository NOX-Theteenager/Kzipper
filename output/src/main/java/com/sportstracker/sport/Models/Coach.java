package com.sportstracker.sport.Models;

//import com.fasterxml.jackson.annotation.JsonManagedReference;
import jakarta.persistence.*;
//import org.apache.logging.log4j.util.Lazy;

import java.util.ArrayList;
import java.util.List;

@Entity
@Table(name = "coaches")
public class Coach extends Utilisateur {
    private String specialite;

    @OneToMany(mappedBy = "coach", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY)
    //@JsonManagedReference // Permet de sérialiser la liste des athlètes
    private List<Athlete> athletes = new ArrayList<>();

    @OneToMany(mappedBy = "coach", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY)
    //@JsonManagedReference
    private List<Objectif> objectifs = new ArrayList<>();

    // Getters et setters
    public String getSpecialite() {
        return specialite;
    }

    public void setSpecialite(String specialite) {
        this.specialite = specialite;
    }

    public List<Athlete> getAthletes() {
        return athletes;
    }

    public void setAthletes(List<Athlete> athletes) {
        this.athletes = athletes;
    }

    public List<Objectif> getObjectifs() {
        return objectifs;
    }

    public void setObjectifs(List<Objectif> objectifs) {
        this.objectifs = objectifs;
    }
}
