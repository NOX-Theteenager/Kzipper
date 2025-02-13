package com.sportstracker.sport.Repositories;

import com.sportstracker.sport.Models.Performance;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface PerformanceRepository extends JpaRepository<Performance, Long> {
    List<Performance> findByAthleteId(Long athleteId);
    List<Performance> findByObjectifId(Long objectifId);
    List<Performance> findByDateBetween(java.time.LocalDate startDate, java.time.LocalDate endDate);

}


