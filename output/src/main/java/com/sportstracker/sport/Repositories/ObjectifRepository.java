package com.sportstracker.sport.Repositories;

import com.sportstracker.sport.Models.Objectif;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ObjectifRepository extends JpaRepository<Objectif, Long> {
    List<Objectif> findByCoachId(Long coachId);
    List<Objectif> findByDateEcheanceBefore(java.time.LocalDate date);
}