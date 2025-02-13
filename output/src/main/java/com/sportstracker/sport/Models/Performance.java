package com.sportstracker.sport.Models;

//import com.fasterxml.jackson.annotation.JsonBackReference;
import jakarta.persistence.*;
import java.time.LocalDate;


@Entity
@Table(name = "performances")
public class Performance {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private String type;

    @Column(name = "date_performance")
    private LocalDate date;

    private Double valeur;

    private Boolean isDeleted = false;

    @ManyToOne
    @JoinColumn(name = "athlete_id", nullable = false)
    //@JsonBackReference
    private Athlete athlete;

    @ManyToOne
    @JoinColumn(name = "objectif_id", nullable = true)
    private Objectif objectif;

    public Long getId() {
        return id;
    }

    public void setAthlete(Athlete athlete) {
        this.athlete = athlete;
    }

    public Athlete getAthlete() {
        return athlete;
    }

    public void setObjectif(Objectif objectif) {
        this.objectif = objectif;
    }

    public Objectif getObjectif() {
        return objectif;
    }

    public Double getValeur() {
        return valeur;
    }

    public void setValeur(Double valeur) {
        this.valeur = valeur;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public LocalDate getDate() {
        return date;
    }

    public void setDate(LocalDate date) {
        this.date = date;
    }

    @Override
    public String toString(){
        return type + " " + valeur + " " + date ;
    }
}
