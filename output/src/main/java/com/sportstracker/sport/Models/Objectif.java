package com.sportstracker.sport.Models;

//import com.fasterxml.jackson.annotation.JsonBackReference;
import jakarta.persistence.*;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

@Entity
@Table(name = "objectifs")
public class Objectif {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private String description;

    @Column(name = "dateEcheance")
    private LocalDate dateEcheance;

    private Boolean isDeleted = false;

    private String titre;

    @ManyToOne
    @JoinColumn(name = "coach_id", nullable = false)
    //@JsonBackReference
    private Coach coach;

    @OneToMany(mappedBy = "objectif", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<Performance> performances = new ArrayList<>();

    public String getTitre() {
        return titre;
    }

    public Long getId() {
        return id;
    }

    public void setTitre(String nom) {
        this.titre = nom;
    }

    public void setDateEcheance(LocalDate dateEcheance) {
        this.dateEcheance = dateEcheance;
    }

    public LocalDate getDateEcheance() {
        return dateEcheance;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public Coach getCoach() {
        return coach;
    }

    public void setCoach(Coach coach) {
        this.coach = coach;
    }
}
