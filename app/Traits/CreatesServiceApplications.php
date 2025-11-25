<?php

namespace App\Traits;

use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use Illuminate\Support\Facades\Log;

/**
 * Trait for easily integrating any service with the Universal Plugin System
 * 
 * Usage in your controller:
 * 
 * use CreatesServiceApplications;
 * 
 * $this->createServiceApplicationFor($primaryModel, 'service-slug', $applicationData);
 */
trait CreatesServiceApplications
{
    /**
     * Create a ServiceApplication for any service type
     * 
     * @param mixed $primaryModel The main model (TouristVisa, TranslationRequest, FlightBooking, etc.)
     * @param string $serviceSlug The service module slug (tourist-visa, translation, flight-booking, etc.)
     * @param array $applicationData Service-specific data to store in JSON
     * @param string|null $primaryModelKey Optional foreign key name (default: derived from class name)
     * @return ServiceApplication|null
     */
    protected function createServiceApplicationFor(
        $primaryModel,
        string $serviceSlug,
        array $applicationData,
        ?string $primaryModelKey = null
    ): ?ServiceApplication {
        try {
            // Get the service module
            $serviceModule = ServiceModule::where('slug', $serviceSlug)->first();
            
            if (!$serviceModule) {
                Log::warning("Service module not found: {$serviceSlug}");
                return null;
            }

            // Determine the foreign key name
            if (!$primaryModelKey) {
                $className = class_basename($primaryModel);
                $primaryModelKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . '_id';
            }

            // Create service application
            $serviceApplication = ServiceApplication::create([
                'user_id' => $primaryModel->user_id ?? auth()->id(),
                'service_module_id' => $serviceModule->id,
                $primaryModelKey => $primaryModel->id,
                'status' => 'pending',
                'application_data' => $applicationData,
            ]);

            Log::info('ServiceApplication created via trait', [
                'service_application_id' => $serviceApplication->id,
                'application_number' => $serviceApplication->application_number,
                'service_slug' => $serviceSlug,
                'primary_model' => get_class($primaryModel),
                'primary_model_id' => $primaryModel->id,
            ]);

            return $serviceApplication;

        } catch (\Exception $e) {
            Log::error('Failed to create ServiceApplication via trait', [
                'error' => $e->getMessage(),
                'service_slug' => $serviceSlug,
                'primary_model' => get_class($primaryModel),
            ]);
            
            return null;
        }
    }

    /**
     * Get agencies that can service this application based on various criteria
     * 
     * @param ServiceApplication $serviceApplication
     * @param array $filters Additional filters (country_id, resource_id, etc.)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getEligibleAgencies(ServiceApplication $serviceApplication, array $filters = [])
    {
        $serviceModule = $serviceApplication->serviceModule;
        
        // Based on assignment model, find eligible agencies
        return match($serviceModule->assignment_model) {
            'competitive' => $this->getCompetitiveAgencies($serviceApplication, $filters),
            'exclusive_resource' => $this->getExclusiveResourceAgencies($serviceApplication, $filters),
            'global_single' => $this->getGlobalSingleAgency($serviceModule),
            'multi_country' => $this->getMultiCountryAgencies($serviceApplication, $filters),
            default => collect([]),
        };
    }

    /**
     * Get agencies for competitive assignment (multiple agencies can quote)
     */
    private function getCompetitiveAgencies($serviceApplication, $filters)
    {
        $query = \App\Models\Agency::where('is_active', true);

        // Filter by country if provided
        if (isset($filters['country_id'])) {
            $query->whereHas('countryAssignments', function($q) use ($serviceApplication, $filters) {
                $q->where('country_id', $filters['country_id'])
                  ->where('service_module_id', $serviceApplication->service_module_id);
            });
        }

        return $query->get();
    }

    /**
     * Get agency that owns exclusive resource (university, school, etc.)
     */
    private function getExclusiveResourceAgencies($serviceApplication, $filters)
    {
        if (!isset($filters['resource_name'])) {
            return collect([]);
        }

        $resource = \App\Models\AgencyResource::where('resource_name', $filters['resource_name'])
            ->where('service_module_id', $serviceApplication->service_module_id)
            ->where('is_approved', true)
            ->where('is_primary_owner', true)
            ->first();

        return $resource ? collect([$resource->agency]) : collect([]);
    }

    /**
     * Get the single global agency for this service
     */
    private function getGlobalSingleAgency($serviceModule)
    {
        // Find the agency assigned globally for this service
        $assignment = \App\Models\Agency::whereHas('countryAssignments', function($q) use ($serviceModule) {
            $q->where('service_module_id', $serviceModule->id)
              ->whereNull('country_id'); // Global assignment
        })->first();

        return $assignment ? collect([$assignment]) : collect([]);
    }

    /**
     * Get agencies for multi-country regional specialists
     */
    private function getMultiCountryAgencies($serviceApplication, $filters)
    {
        // Similar to competitive but can be assigned to multiple countries at once
        return $this->getCompetitiveAgencies($serviceApplication, $filters);
    }

    /**
     * Notify eligible agencies about new application
     * 
     * @param ServiceApplication $serviceApplication
     * @param array $filters
     */
    protected function notifyEligibleAgencies(ServiceApplication $serviceApplication, array $filters = [])
    {
        $agencies = $this->getEligibleAgencies($serviceApplication, $filters);
        
        foreach ($agencies as $agency) {
            // TODO: Send notification (email, SMS, push)
            Log::info('Notifying agency of new application', [
                'agency_id' => $agency->id,
                'agency_name' => $agency->name,
                'application_number' => $serviceApplication->application_number,
                'service_name' => $serviceApplication->serviceModule->name,
            ]);
            
            // Placeholder for actual notification
            // Notification::send($agency, new NewApplicationNotification($serviceApplication));
        }
    }
}
