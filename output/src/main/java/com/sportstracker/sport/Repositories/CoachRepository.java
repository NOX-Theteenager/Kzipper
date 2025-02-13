package com.sportstracker.sport.Repositories;

import com.sportstracker.sport.Models.Coach;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface CoachRepository extends JpaRepository <Coach, Long> {
    // rechercher un coach par sa specialite
    List<Coach> findBySpecialite(String specialite);

    Optional<Coach> findById(Long coachId);

}
