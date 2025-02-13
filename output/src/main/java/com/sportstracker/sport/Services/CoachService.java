package com.sportstracker.sport.Services;

import com.sportstracker.sport.Models.Athlete;
import com.sportstracker.sport.Models.Coach;
import com.sportstracker.sport.Repositories.AthleteRepository;
import com.sportstracker.sport.Repositories.CoachRepository;
import jakarta.persistence.EntityNotFoundException;
import org.springframework.stereotype.Service;
import org.springframework.beans.factory.annotation.Autowired;

import java.util.List;
import java.util.Optional;

@Service
public class CoachService {

    @Autowired
    private CoachRepository coachRepository;

    @Autowired
    private AthleteRepository athleteRepository;

    public List<Coach> getAllCoaches() {
        return coachRepository.findAll();
    }

    public Coach getCoachById(Long id) {
        return coachRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Coach non trouvé avec l'ID : " + id));
    }

    public Coach createCoach(Coach coach) {
        return coachRepository.save(coach);
    }

    public Coach updateCoach(Long id, Coach coachDetails) {
        Coach coach = getCoachById(id);
        coach.setNom(coachDetails.getNom());
        coach.setSpecialite(coachDetails.getSpecialite());
        return coachRepository.save(coach);
    }

    public void deleteCoach(Long id) {
        Coach coach = getCoachById(id);
        coachRepository.delete(coach);
    }

    public Athlete addAthlete(Long coachId, Long athleteId) {
        // Rechercher le coach par son ID
        Coach coach = getCoachById(coachId);
        if (coach == null) {
            throw new EntityNotFoundException("Coach avec ID " + coachId + " non trouvé.");
        }

        // Rechercher l'athlète par son ID
        Optional<Athlete> optionalAthlete = athleteRepository.findById(athleteId);
        if (optionalAthlete.isEmpty()) {
            throw new EntityNotFoundException("Athlète avec ID " + athleteId + " non trouvé.");
        }

        // Associer l'athlète au coach
        Athlete athlete = optionalAthlete.get();
        athlete.setCoach(coach);

        // Sauvegarder l'athlète mis à jour dans la base de données
        athlete = athleteRepository.save(athlete);

        return athlete;
    }

}
