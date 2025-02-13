package com.sportstracker.sport.Repositories;

import com.sportstracker.sport.Models.Athlete;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface AthleteRepository extends JpaRepository<Athlete, Long> {
    List<Athlete> findByCoachId(Long coachId);
    List<Athlete> findByNomContainingIgnoreCase(String nom);

    @Query("SELECT a FROM Athlete a WHERE a.coach.id IS NULL")
    List<Athlete> findAthletesWithoutCoach();
}

