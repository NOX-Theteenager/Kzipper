package com.sportstracker.sport.Services;

import com.sportstracker.sport.Models.Athlete;
import com.sportstracker.sport.Models.Objectif;
import com.sportstracker.sport.Models.Performance;
import com.sportstracker.sport.Repositories.AthleteRepository;
import com.sportstracker.sport.Repositories.ObjectifRepository;
import com.sportstracker.sport.Repositories.PerformanceRepository;
import org.springframework.stereotype.Service;
import org.springframework.beans.factory.annotation.Autowired;


import java.util.List;
import java.util.Optional;

@Service
public class PerformanceService {

    @Autowired
    private PerformanceRepository performanceRepository;

    @Autowired
    private AthleteRepository athleteRepository;

    @Autowired
    private ObjectifRepository objectifRepository;

    public List<Performance> getAllPerformances() {
        return performanceRepository.findAll();
    }

    public Performance getPerformanceById(Long id) {
        return performanceRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Performance non trouvée avec l'ID : " + id));
    }

    public Performance createPerformance(Long athleteId, Long objectifId, Performance performance) {
        Athlete athlete = athleteRepository.findById(athleteId)
                .orElseThrow(() -> new RuntimeException("Athlète non trouvé avec l'ID : " + athleteId));
        Objectif objectif = objectifRepository.findById(objectifId)
                .orElseThrow(() -> new RuntimeException("Objectif non trouvé avec l'ID : " + objectifId));
        performance.setAthlete(athlete);
        performance.setObjectif(objectif);
        return performanceRepository.save(performance);
    }

    public Performance createPerformance(Performance performance) {
        return performanceRepository.save(performance);
    }

    public Performance updatePerformance(Long id, Performance performanceDetails) {
        Performance performance = getPerformanceById(id);
        performance.setValeur(performanceDetails.getValeur());
        performance.setType(performanceDetails.getType());
        performance.setDate(performanceDetails.getDate());
        return performanceRepository.save(performance);
    }

    public void deletePerformance(Long id) {
        Performance performance = getPerformanceById(id);
        performanceRepository.delete(performance);
    }

    public List<Performance> getPerformancesByAthleteId(Long id){
        return performanceRepository.findByAthleteId(id);
    }
}