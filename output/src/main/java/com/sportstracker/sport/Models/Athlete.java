package com.sportstracker.sport.Models;

//mport com.fasterxml.jackson.annotation.JsonManagedReference;
import jakarta.persistence.*;

import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

@Entity
@Table(name = "athletes")
public class Athlete extends Utilisateur {
    @ManyToOne
    @JoinColumn(name = "coach_id", nullable = true)
    //@JsonBackReference // Éviter la sérialisation récursive de coach
    private Coach coach;

    @OneToMany(mappedBy = "athlete", cascade = CascadeType.ALL, orphanRemoval = true)
    //@JsonManagedReference
    private List<Performance> performances = new ArrayList<>();

    private LocalDate dateNaissance;

    @Column(name = "date_naissance")
    public LocalDate getDateNaissance() {
        return dateNaissance;
    }

    public void setDateNaissance(LocalDate dateNaissance) {
        this.dateNaissance = dateNaissance;
    }

    // Getters et setters
    public Coach getCoach() {
        return coach;
    }

    public void setCoach(Coach coach) {
        this.coach = coach;
    }

    public List<Performance> getPerformances() {
        return performances;
    }

    public void setPerformances(List<Performance> performances) {
        this.performances = performances;
    }
}
