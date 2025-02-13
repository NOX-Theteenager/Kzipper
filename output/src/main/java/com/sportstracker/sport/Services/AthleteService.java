package com.sportstracker.sport.Services;

import com.sportstracker.sport.Models.Athlete;
import com.sportstracker.sport.Models.Coach;
import com.sportstracker.sport.Repositories.AthleteRepository;
import com.sportstracker.sport.Repositories.CoachRepository;
import org.springframework.stereotype.Service;

import org.springframework.beans.factory.annotation.Autowired;

import java.util.List;

@Service
public class AthleteService {

    @Autowired
    private AthleteRepository athleteRepository;

    @Autowired
    private CoachRepository coachRepository;

    public List<Athlete> getAllAthletes() {
        return athleteRepository.findAll();
    }

    public Athlete getAthleteById(Long id) {
        return athleteRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Athlète non trouvé avec l'ID : " + id));
    }

    public void createAthlete(Athlete athlete) {
        athleteRepository.save(athlete);
    }

    public void updateAthlete(Long id, Athlete athleteDetails) {
        Athlete athlete = getAthleteById(id);
        athlete.setNom(athleteDetails.getNom());
        athlete.setDateNaissance(athleteDetails.getDateNaissance());
        athlete.setCoach(athleteDetails.getCoach());
        athleteRepository.save(athlete);
    }

    public void updateCoach(Long AthleteId, Coach coach ){
        Athlete athlete = getAthleteById(AthleteId);
        athlete.setCoach(coach);
        athleteRepository.save(athlete);
    }

    public void removeCoach(Long AthleteId){
        Athlete athlete = getAthleteById(AthleteId);
        athlete.setCoach(null);
        athleteRepository.save(athlete);
    }

    public void deleteAthlete(Long id) {
        Athlete athlete = getAthleteById(id);
        athleteRepository.delete(athlete);
    }

    public List<Athlete> getAthletesByCoachId(Long coachId) {
        return athleteRepository.findByCoachId(coachId);
    }

    public List<Athlete> getAthletesWithoutCoach(){
        return athleteRepository.findAthletesWithoutCoach();

    }
}
