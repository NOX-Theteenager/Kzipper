package com.sportstracker.sport.Services;

import com.sportstracker.sport.Models.Coach;
import com.sportstracker.sport.Models.Objectif;
import com.sportstracker.sport.Repositories.ObjectifRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.sportstracker.sport.Repositories.CoachRepository;

import java.util.List;

@Service
public class ObjectifService {

    @Autowired
    private ObjectifRepository objectifRepository;

    @Autowired
    private CoachRepository coachRepository;

    public List<Objectif> getAllObjectifs() {
        return objectifRepository.findAll();
    }

    public Objectif getObjectifById(Long id) {
        return objectifRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Objectif non trouvé avec l'ID : " + id));
    }

    public Objectif createObjectif(Long coachId, Objectif objectif) {
        Coach coach = coachRepository.findById(coachId)
                .orElseThrow(() -> new RuntimeException("Coach non trouvé avec l'ID : " + coachId));
        objectif.setCoach(coach);
        return objectifRepository.save(objectif);
    }

    public Objectif updateObjectif(Long id, Objectif objectifDetails) {
        Objectif objectif = getObjectifById(id);
        objectif.setDescription(objectifDetails.getDescription());
        objectif.setDateEcheance(objectifDetails.getDateEcheance());
        objectif.setTitre(objectifDetails.getTitre());
        return objectifRepository.save(objectif);
    }

    public void deleteObjectif(Long id) {
        Objectif objectif = getObjectifById(id);
        objectifRepository.delete(objectif);
    }

    public List<Objectif> getObjectifsByCoachId(Long coachId) {
        return objectifRepository.findByCoachId(coachId);
    }
}