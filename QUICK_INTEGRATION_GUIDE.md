# Plugin System - Quick Integration Guide

**For Remaining 32 Services**

---

## 5-Minute Integration Checklist

### Step 1: Identify Service (30 seconds)
- Find service controller (e.g., `WorkPermitController.php`)
- Locate `store()` or booking method
- Note the primary model being created

### Step 2: Add Trait (1 minute)
```php
// At top of file
use App\Traits\CreatesServiceApplications;

// In class
class YourServiceController extends Controller
{
    use CreatesServiceApplications;
    
    // ... rest of code
}
```

### Step 3: Add ServiceApplication Creation (2 minutes)
```php
// In store() method, after your model is created
DB::beginTransaction();
try {
    // Your existing code
    $model = YourModel::create($validated);
    
    // ADD THIS ONE LINE:
    $this->createServiceApplicationFor(
        $model,                    // Your model instance
        'your-service-slug',       // From service_modules table
        [                          // Service-specific data
            'field1' => $validated['field1'],
            'field2' => $validated['field2'],
            // ... any relevant data
        ]
    );
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### Step 4: Test (1.5 minutes)
```bash
# Create a test booking/application
# Check service_applications table
# Verify application_number generated
# Done!
```

---

## Service Slugs (Copy-Paste Ready)

### Travel Services
```php
$this->createServiceApplicationFor($model, 'flight-booking', $data);
$this->createServiceApplicationFor($model, 'hotel-booking', $data);
$this->createServiceApplicationFor($model, 'travel-insurance', $data);
$this->createServiceApplicationFor($model, 'airport-transfer', $data);
$this->createServiceApplicationFor($model, 'tour-package', $data);
```

### Visa Services
```php
$this->createServiceApplicationFor($model, 'tourist-visa', $data);
$this->createServiceApplicationFor($model, 'work-permit', $data);
$this->createServiceApplicationFor($model, 'student-visa', $data);
$this->createServiceApplicationFor($model, 'business-visa', $data);
$this->createServiceApplicationFor($model, 'transit-visa', $data);
$this->createServiceApplicationFor($model, 'medical-visa', $data);
```

### Document Services
```php
$this->createServiceApplicationFor($model, 'translation', $data);
$this->createServiceApplicationFor($model, 'document-attestation', $data);
$this->createServiceApplicationFor($model, 'certificate-verification', $data);
$this->createServiceApplicationFor($model, 'police-clearance', $data);
$this->createServiceApplicationFor($model, 'birth-certificate', $data);
$this->createServiceApplicationFor($model, 'marriage-certificate', $data);
```

### Education Services
```php
$this->createServiceApplicationFor($model, 'university-admission', $data);
$this->createServiceApplicationFor($model, 'school-enrollment', $data);
$this->createServiceApplicationFor($model, 'language-course', $data);
$this->createServiceApplicationFor($model, 'professional-training', $data);
$this->createServiceApplicationFor($model, 'scholarship', $data);
$this->createServiceApplicationFor($model, 'credential-evaluation', $data);
```

### Employment Services
```php
$this->createServiceApplicationFor($model, 'job-application', $data);
$this->createServiceApplicationFor($model, 'job-posting', $data);
$this->createServiceApplicationFor($model, 'interview-prep', $data);
$this->createServiceApplicationFor($model, 'career-counseling', $data);
$this->createServiceApplicationFor($model, 'cv-writing', $data);
```

### Financial Services
```php
$this->createServiceApplicationFor($model, 'money-transfer', $data);
$this->createServiceApplicationFor($model, 'forex-exchange', $data);
$this->createServiceApplicationFor($model, 'banking-setup', $data);
```

### Other Services
```php
$this->createServiceApplicationFor($model, 'health-checkup', $data);
$this->createServiceApplicationFor($model, 'sim-card', $data);
$this->createServiceApplicationFor($model, 'driving-license', $data);
$this->createServiceApplicationFor($model, 'accommodation', $data);
$this->createServiceApplicationFor($model, 'legal-consultation', $data);
```

---

## Example Integration (Work Permit)

### Before (No Agency Integration)
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    $workPermit = WorkPermit::create($validated);
    
    return redirect()->route('work-permit.show', $workPermit);
}
```

### After (5 minutes to add)
```php
use App\Traits\CreatesServiceApplications;

class WorkPermitController extends Controller
{
    use CreatesServiceApplications;
    
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        
        DB::beginTransaction();
        try {
            $workPermit = WorkPermit::create($validated);
            
            // ADDED: 3 minutes to write this
            $this->createServiceApplicationFor(
                $workPermit,
                'work-permit',
                [
                    'destination_country' => $validated['country'],
                    'job_title' => $validated['job_title'],
                    'employer_name' => $validated['employer'],
                    'duration_months' => $validated['duration'],
                    'salary' => $validated['salary'],
                ]
            );
            
            DB::commit();
            return redirect()->route('work-permit.show', $workPermit);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

**Result**: 
- ✅ Agencies can now quote on work permits
- ✅ Commission tracked automatically (15%)
- ✅ Same quote workflow as other services
- ✅ Revenue aggregated in platform dashboard

---

## Common Patterns

### Pattern 1: Simple Application
```php
$this->createServiceApplicationFor(
    $application,
    'service-slug',
    $validated  // Pass all validated data
);
```

### Pattern 2: Selective Data
```php
$this->createServiceApplicationFor(
    $booking,
    'service-slug',
    [
        'key1' => $validated['field1'],
        'key2' => $validated['field2'],
        'total' => $totalAmount,
    ]
);
```

### Pattern 3: With Calculated Fields
```php
$this->createServiceApplicationFor(
    $model,
    'service-slug',
    array_merge($validated, [
        'calculated_fee' => $fee,
        'discount_applied' => $discount,
        'final_amount' => $total,
    ])
);
```

---

## Troubleshooting

### Issue: ServiceApplication not created
**Solution**: Check if service slug exists in `service_modules` table
```bash
php artisan tinker
ServiceModule::where('slug', 'your-slug')->first();
```

### Issue: Foreign key error
**Solution**: Ensure model ID column matches pattern
- Expected: `your_model_id` (e.g., `flight_booking_id`)
- Trait auto-detects from class name

### Issue: No application_number
**Solution**: Model boot() method handles this automatically
- Already implemented in ServiceApplication model
- No action needed

---

## Testing Quick Commands

```bash
# 1. Check if service exists
php artisan tinker --execute="echo ServiceModule::where('slug', 'work-permit')->exists();"

# 2. View recent applications
php artisan tinker --execute="ServiceApplication::latest()->take(5)->get(['id', 'service_module_id', 'application_number']);"

# 3. Count applications by service
php artisan tinker --execute="ServiceApplication::with('serviceModule')->get()->groupBy('serviceModule.name')->map(fn($g) => $g->count());"

# 4. Check commission rates
php artisan tinker --execute="ServiceModule::all(['name', 'platform_commission_rate']);"
```

---

## Integration Time Estimate

| Services | Time | Notes |
|----------|------|-------|
| 1-5 services | 25 min | Learning curve |
| 6-10 services | 30 min | Getting faster |
| 11-20 services | 50 min | In the zone |
| 21-36 services | 80 min | Cruise control |
| **TOTAL** | **~3 hours** | **All 36 done!** |

---

## Checklist Template

```
Service: _______________________
Controller: ____________________
Model: _________________________

[ ] Import trait added
[ ] Trait used in class
[ ] createServiceApplicationFor() called
[ ] Service slug verified
[ ] Application data array complete
[ ] DB transaction wrapped
[ ] Tested with sample data
[ ] Checked service_applications table
[ ] Verified application_number generated
[ ] Ready for agency quotes

Time taken: _____ minutes
```

---

## Ready-to-Copy Integration Code

```php
// 1. Add to top of controller
use App\Traits\CreatesServiceApplications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// 2. Add to class
use CreatesServiceApplications;

// 3. Wrap store() in transaction and add one line
DB::beginTransaction();
try {
    $model = YourModel::create($validated);
    
    $this->createServiceApplicationFor($model, 'service-slug', $validated);
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Service creation failed', ['error' => $e->getMessage()]);
    throw $e;
}
```

---

## Success Metrics

After integrating each service, verify:

✅ ServiceApplication created  
✅ application_number generated  
✅ application_data has correct structure  
✅ service_module_id matches  
✅ user_id set correctly  
✅ Status = 'pending'  
✅ No errors in logs  

**If all ✅, you're done! Move to next service.**

---

**Average Time**: 5 minutes per service  
**Total Remaining**: 32 services  
**Estimated Completion**: 2.5 hours  

**Let's scale to 36! 🚀**
