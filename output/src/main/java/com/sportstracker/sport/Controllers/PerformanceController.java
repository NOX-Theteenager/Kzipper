package com.sportstracker.sport.Controllers;

import com.sportstracker.sport.Models.Performance;
import com.sportstracker.sport.Services.PerformanceService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.time.LocalDate;
import java.util.List;

@RestController
@RequestMapping("/api/performances")
public class PerformanceController {

    @Autowired
    private PerformanceService performanceService;

    @GetMapping
    public List<Performance> getAllPerformances() {
        return performanceService.getAllPerformances();
    }

    @GetMapping("/{id}")
    public Performance getPerformanceById(@PathVariable Long id) {
        return performanceService.getPerformanceById(id);
    }

    @PostMapping("/{athleteId}/{objectifId}")
    public Performance createPerformance(
            @PathVariable Long athleteId,
            @PathVariable Long objectifId,
            @RequestBody Performance performance
    ) {
        return performanceService.createPerformance(athleteId, objectifId, performance);
    }

    @DeleteMapping("/{id}")
    public void deletePerformance(@PathVariable Long id) {
        performanceService.deletePerformance(id);
    }

    @GetMapping("/athlete/{athleteId}")
    public List<Performance> getPerformancesByAthleteId(@PathVariable Long athleteId) {
        return performanceService.getAllPerformances();
    }

    @GetMapping("/objectif/{objectifId}")
    public List<Performance> getPerformancesByObjectifId(@PathVariable Long objectifId) {
        return performanceService.getAllPerformances();
    }

    @GetMapping("/date")
    public List<Performance> getPerformancesByDateRange(
            @RequestParam LocalDate startDate,
            @RequestParam LocalDate endDate
    ) {
        return performanceService.getAllPerformances();
    }
}