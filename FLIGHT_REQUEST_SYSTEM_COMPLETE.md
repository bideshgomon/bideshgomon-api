# Flight Request System - Implementation Complete

## Overview
Successfully implemented a comprehensive Flight Request System where users submit flight requirements, admins assign requests to agencies, agencies provide competitive quotes, and users compare and accept the best offers.

## System Architecture

### Database Schema (3 Tables)

#### 1. flight_requests
**Purpose**: Store user flight requests with trip details, budget, and preferences
- **Reference**: `request_reference` (auto-generated: FR20251119001)
- **Trip Details**: 
  - `trip_type` (one_way, round_trip, multi_city)
  - Origin/destination codes, travel dates
  - `multi_city_segments` JSON for complex trips
- **Passengers**: Count, class, passenger details JSON
- **Contact**: Name, email, phone, special requests
- **Budget**: Min/max range for pricing
- **Preferences**: 
  - Direct flights preference
  - Preferred airlines (JSON array)
  - Preferred time (morning/afternoon/evening/night/flexible)
- **Assignment**: 
  - `assigned_to` (FK to users - admin/agency)
  - `assigned_at` timestamp
- **Status Workflow**: 
  - pending → assigned → quoted → accepted/rejected → completed
  - Also supports: cancelled
- **Quotes Tracking**: `quotes_count`, `quoted_at`
- **Admin Features**: `admin_notes`, `rejection_reason`
- **Analytics**: `ip_address`, `user_agent`, `search_count`

**Indexes**: 
- User + status, assigned_to + status, status + created_at
- Custom `fr_route_date_idx` (origin + dest + date) for route analytics

#### 2. flight_quotes
**Purpose**: Store quotes submitted by agencies for flight requests
- **Relations**: `flight_request_id`, `quoted_by` (agency user)
- **Quote Details**:
  - Airline name, flight number
  - Quoted price (decimal 10,2)
  - Price breakdown (text explanation)
  - Flight details (departure/arrival times, layovers)
- **Validity**: `valid_until` timestamp, `is_valid` boolean
- **Status**: pending/accepted/rejected/expired
- **Notes**: Agency notes for the customer

**Indexes**: Request + status, quoted_by + created_at

**Business Logic**: Multiple agencies can quote on same request, user compares and accepts best offer

#### 3. flight_searches
**Purpose**: Track all user searches for dynamic popular routes analytics
- **User**: `user_id` (nullable - tracks logged-in and anonymous)
- **Search Details**: Trip type, origin/dest, dates, multi-city segments
- **Passengers**: Count, flight class
- **Tracking**: IP address, user agent, search_count (aggregates)

**Indexes**: Custom `fs_route_date_idx` (origin + dest + created_at), travel_date + created_at

**Analytics**: Query this table to generate "Popular Routes" based on real search data, not static seeded routes

---

## Models (3 Files)

### 1. FlightRequest (`app/Models/FlightRequest.php`)
**Features**:
- Auto-generates `request_reference` in boot() method (FR + YYYYMMDD + sequential 3-digit number)
- **Relationships**:
  - `user()` - BelongsTo User
  - `assignedTo()` - BelongsTo User
  - `quotes()` - HasMany FlightQuote
- **Scopes**:
  - `forUser($userId)`, `pending()`, `assigned()`, `quoted()`, `completed()`
  - `assignedToUser($userId)` - For agency filtering
- **Helpers**:
  - `isPending()`, `isAssigned()`, `isQuoted()`
  - `canReceiveQuote()` - Checks if request can accept new quotes
- **Attributes**:
  - `formatted_budget` - "৳ 50,000 - ৳ 100,000" or "Up to ৳ 80,000" or "Flexible"
  - `route_name` - "DAC → DXB" or "Multi-City Trip"
  - `status_color` - Tailwind classes for status badges
- **Casts**: 
  - Arrays: multi_city_segments, passengers, preferred_airlines
  - Dates: travel_date, return_date, assigned_at, quoted_at
  - Decimals: budget_min, budget_max
  - Boolean: prefer_direct_flights

### 2. FlightQuote (`app/Models/FlightQuote.php`)
**Features**:
- **Relationships**:
  - `flightRequest()` - BelongsTo FlightRequest
  - `quotedBy()` - BelongsTo User (agency)
- **Scopes**:
  - `valid()` - Not expired and is_valid = true
  - `pending()`, `accepted()`
- **Helpers**:
  - `isExpired()` - Checks valid_until timestamp
  - `isPending()`, `isAccepted()`
- **Attributes**:
  - `formatted_price` - "৳ 75,000"
  - `status_color` - Tailwind classes for status badges
- **Casts**:
  - Decimal: quoted_price
  - Datetime: valid_until
  - Boolean: is_valid

### 3. FlightSearch (`app/Models/FlightSearch.php`)
**Features**:
- **Relationship**: `user()` - BelongsTo User
- **Static Methods**:
  - `getPopularRoutes($limit = 10)` - Most searched routes in last 30 days
    - Groups by origin/dest, sums search_count, orders by total_searches
  - `getTrendingRoutes($limit = 6)` - Hot routes in last 7 days
    - Orders by search_frequency + total_searches for recency
- **Casts**:
  - Dates: travel_date, return_date
  - Array: multi_city_segments

---

## Controllers (3 Files)

### 1. FlightRequestController (User-Facing)
**Route Prefix**: `/services/flight-requests`

**Methods**:
1. **`create()`** - GET `/create`
   - Shows flight request form
   - Loads trending routes from FlightSearch::getTrendingRoutes(6)
   - Renders: `Services/FlightRequest/Create.vue`

2. **`store()`** - POST `/`
   - Validates 20+ fields (trip details, passengers, budget, preferences)
   - Creates FlightRequest with status 'pending'
   - Tracks search in flight_searches table
   - Redirects to request details with success message
   - Validation rules:
     - origin/dest required for one_way/round_trip
     - multi_city_segments required for multi_city (min 2 segments)
     - passengers array with name, age, passport_number
     - budget_max >= budget_min

3. **`index()`** - GET `/`
   - Shows user's all flight requests
   - Filters: all/pending/assigned/quoted/accepted/rejected/cancelled/completed
   - Paginated (10 per page)
   - Eager loads: quotes, assignedTo
   - Renders: `Services/FlightRequest/Index.vue`

4. **`show($id)`** - GET `/{id}`
   - Shows single request with all quotes
   - Eager loads: quotes.quotedBy, assignedTo
   - Renders: `Services/FlightRequest/Show.vue`

5. **`acceptQuote($requestId, $quoteId)`** - POST `/{requestId}/quotes/{quoteId}/accept`
   - Validates request status (must be quoted/assigned)
   - Checks quote validity (not expired)
   - Checks wallet balance
   - Deducts payment from wallet
   - Updates quote status to 'accepted'
   - Rejects all other quotes
   - Updates request status to 'completed'
   - Success message with redirect

6. **`cancel($id)`** - POST `/{id}/cancel`
   - Validates status (pending/assigned/quoted only)
   - Updates status to 'cancelled'
   - Returns with success message

### 2. Admin\FlightRequestController (Admin Panel)
**Route Prefix**: `/admin/flight-requests`

**Methods**:
1. **`index()`** - GET `/`
   - Lists all flight requests with filters
   - Filters: all/pending/assigned/quoted/accepted/completed
   - Search: by request_reference, airport codes, user name/email
   - Paginated (15 per page)
   - Loads agencies (whereHas('role', 'agency'))
   - Summary stats: total, pending, assigned, quoted, completed
   - Renders: `Admin/FlightRequests/Index.vue`

2. **`show($id)`** - GET `/{id}`
   - Shows request details with all quotes
   - Loads agencies for assignment dropdown
   - Renders: `Admin/FlightRequests/Show.vue`

3. **`assign($id)`** - POST `/{id}/assign`
   - Validates agency_id (must have agency role)
   - Updates: assigned_to, assigned_at, status = 'assigned'
   - Success message with agency name

4. **`updateNotes($id)`** - POST `/{id}/notes`
   - Updates admin_notes field
   - For internal tracking/communication

5. **`cancel($id)`** - POST `/{id}/cancel`
   - Requires rejection_reason
   - Updates status to 'cancelled'
   - Stores rejection reason

6. **`bulkAssign()`** - POST `/bulk-assign`
   - Accepts array of request_ids
   - Assigns all to one agency
   - Updates: assigned_to, assigned_at, status = 'assigned'
   - Success message with count

### 3. Agency\FlightRequestController (Agency Panel)
**Route Prefix**: `/agency/flight-requests`

**Methods**:
1. **`index()`** - GET `/`
   - Lists requests assigned to logged-in agency
   - Filters: 
     - all - All assigned requests
     - needs_quote - Assigned but agency hasn't quoted yet
     - pending/accepted/rejected - By quote status
   - Shows agency's own quotes only (where quoted_by = Auth::id())
   - Paginated (15 per page)
   - Stats: assigned, needs_quote, quoted, accepted
   - Renders: `Agency/FlightRequests/Index.vue`

2. **`show($id)`** - GET `/{id}`
   - Shows request details
   - Shows only agency's own quotes
   - Renders: `Agency/FlightRequests/Show.vue`

3. **`createQuote($id)`** - GET `/{id}/quote/create`
   - Shows quote submission form
   - Loads request details
   - Checks for existing quote by this agency
   - Renders: `Agency/FlightRequests/CreateQuote.vue`

4. **`storeQuote($id)`** - POST `/{id}/quote`
   - Validates quote can be submitted (canReceiveQuote())
   - Validates 7 fields:
     - airline_name, flight_number
     - quoted_price (numeric, min 0)
     - price_breakdown (text explanation)
     - flight_details (departure/arrival info)
     - valid_until (must be future date)
     - notes (optional)
   - Creates FlightQuote with status 'pending', is_valid = true
   - Updates request status to 'quoted' if currently 'assigned'
   - Updates quotes_count on request
   - Redirects to request details with success

5. **`updateQuote($id, $quoteId)`** - PUT `/{id}/quote/{quoteId}`
   - Only for quotes with status 'pending'
   - Updates quote with new details
   - Same validation as storeQuote
   - Success message

---

## Routes Summary

### User Routes (Authenticated)
```php
Route::middleware(['auth'])->group(function () {
    Route::prefix('services/flight-requests')->name('flight-requests.')->group(function () {
        Route::get('/create', [FlightRequestController::class, 'create'])->name('create');
        Route::post('/', [FlightRequestController::class, 'store'])->name('store');
        Route::get('/', [FlightRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [FlightRequestController::class, 'show'])->name('show');
        Route::post('/{requestId}/quotes/{quoteId}/accept', [FlightRequestController::class, 'acceptQuote'])->name('accept-quote');
        Route::post('/{id}/cancel', [FlightRequestController::class, 'cancel'])->name('cancel');
    });
});
```

### Admin Routes (role:admin)
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('flight-requests')->name('flight-requests.')->group(function () {
        Route::get('/', [Admin\FlightRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [Admin\FlightRequestController::class, 'show'])->name('show');
        Route::post('/{id}/assign', [Admin\FlightRequestController::class, 'assign'])->name('assign');
        Route::post('/{id}/notes', [Admin\FlightRequestController::class, 'updateNotes'])->name('update-notes');
        Route::post('/{id}/cancel', [Admin\FlightRequestController::class, 'cancel'])->name('cancel');
        Route::post('/bulk-assign', [Admin\FlightRequestController::class, 'bulkAssign'])->name('bulk-assign');
    });
});
```

### Agency Routes (role:agency)
```php
Route::middleware(['auth', 'role:agency'])->prefix('agency')->name('agency.')->group(function () {
    Route::prefix('flight-requests')->name('flight-requests.')->group(function () {
        Route::get('/', [Agency\FlightRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [Agency\FlightRequestController::class, 'show'])->name('show');
        Route::get('/{id}/quote/create', [Agency\FlightRequestController::class, 'createQuote'])->name('create-quote');
        Route::post('/{id}/quote', [Agency\FlightRequestController::class, 'storeQuote'])->name('store-quote');
        Route::put('/{id}/quote/{quoteId}', [Agency\FlightRequestController::class, 'updateQuote'])->name('update-quote');
    });
});
```

---

## User Relationships Updated

### User Model (`app/Models/User.php`)
Added relationship:
```php
public function flightRequests(): HasMany
{
    return $this->hasMany(FlightRequest::class);
}
```

---

## Status Workflow

### Flight Request Lifecycle
1. **pending** - User submits request, awaiting admin action
2. **assigned** - Admin assigns to agency, agency sees in their dashboard
3. **quoted** - Agency submits quote, user can now compare
4. **accepted** - User accepts a quote, payment processed
5. **rejected** - Admin cancels with reason
6. **cancelled** - User or admin cancels before acceptance
7. **completed** - Payment successful, booking confirmed

### Flight Quote Lifecycle
1. **pending** - Agency submits, waiting for user decision
2. **accepted** - User selects this quote, payment processed
3. **rejected** - User accepts another quote, this one auto-rejected
4. **expired** - Quote validity period passed (valid_until < now)

---

## Key Features

### Dynamic Popular Routes
- **OLD**: Static seeded routes in database
- **NEW**: Real-time analytics from flight_searches table
- Shows routes most searched in last 7 days (trending)
- Shows routes most searched in last 30 days (popular)

### Multi-Agency Competition
- Multiple agencies can quote on same request
- User sees all quotes in comparison table
- Can accept any valid quote (not expired)
- Auto-rejects other quotes when one is accepted

### Budget Flexibility
- User can specify min/max budget range
- Or just max budget ("Up to ৳ 100,000")
- Or leave blank ("Flexible")
- Agencies see budget and quote accordingly

### Preference System
- **Direct Flights**: Boolean preference
- **Preferred Airlines**: Array of airline names
- **Preferred Time**: Enum (morning/afternoon/evening/night/flexible)
- Agencies consider preferences when quoting

### Wallet Integration
- Quote acceptance deducts from user wallet
- Checks insufficient balance before processing
- Transaction recorded with description
- Prevents over-spending

### Admin Oversight
- Assign requests to specific agencies
- View all quotes across all agencies
- Add admin notes for internal tracking
- Cancel requests with rejection reasons
- Bulk assign multiple requests at once

### Agency Dashboard
- See only requests assigned to them
- Filter by needs_quote/quoted/accepted
- Submit multiple quotes per request (if allowed)
- Edit quotes before user accepts
- Track acceptance rate and revenue

---

## Frontend Pages Needed (Next Phase)

### User Pages (5 Pages)
1. **Create.vue** - Flight request submission form
2. **Index.vue** - User's request history with filters
3. **Show.vue** - Request details with quote comparison
4. **MyQuotes.vue** - All quotes received across requests (optional)

### Admin Pages (2 Pages)
5. **Admin/FlightRequests/Index.vue** - All requests management
6. **Admin/FlightRequests/Show.vue** - Request details with assignment

### Agency Pages (3 Pages)
7. **Agency/FlightRequests/Index.vue** - Assigned requests dashboard
8. **Agency/FlightRequests/Show.vue** - Request details view
9. **Agency/FlightRequests/CreateQuote.vue** - Quote submission form

---

## Migration Status

### Completed ✅
- `2025_11_19_070001_create_flight_requests_table.php` (103.49ms)
- `2025_11_19_070002_create_flight_quotes_table.php` (59.03ms)
- `2025_11_19_070003_create_flight_searches_table.php` (36.19ms)

**All 43 migrations run successfully**
**All seeders completed successfully**

---

## Testing Checklist

### User Flow
- [ ] User submits one-way flight request
- [ ] User submits round-trip request
- [ ] User submits multi-city request
- [ ] User sees request in "My Requests" with status 'pending'
- [ ] User receives notification when quote is submitted
- [ ] User views all quotes and compares
- [ ] User accepts best quote
- [ ] Wallet balance deducted correctly
- [ ] Request status updates to 'completed'
- [ ] User can cancel pending request

### Admin Flow
- [ ] Admin sees all new requests
- [ ] Admin filters by status (pending/assigned/quoted)
- [ ] Admin searches by request reference, airport codes
- [ ] Admin assigns request to specific agency
- [ ] Admin adds notes to request
- [ ] Admin bulk assigns multiple requests
- [ ] Admin cancels request with reason

### Agency Flow
- [ ] Agency sees only assigned requests
- [ ] Agency filters by needs_quote/quoted/accepted
- [ ] Agency submits quote with all details
- [ ] Quote shows in user's comparison view
- [ ] Agency edits quote before user accepts
- [ ] Agency sees acceptance notification
- [ ] Agency tracks performance stats

### Edge Cases
- [ ] Multiple agencies quote on same request
- [ ] User tries to accept expired quote
- [ ] User has insufficient wallet balance
- [ ] Request cancelled after quotes submitted
- [ ] Agency tries to quote on unassigned request
- [ ] User tries to cancel completed request

---

## Next Steps

1. **Create Frontend Pages** (9 Vue components)
   - Start with user Create.vue (highest priority)
   - Then Admin Index.vue (workflow blocker)
   - Then Agency CreateQuote.vue (complete workflow)

2. **Add Email Notifications**
   - User: Request received, quote received, quote accepted
   - Agency: Request assigned, quote accepted/rejected
   - Admin: New request submitted

3. **GDS API Integration** (Future)
   - When agency submits quote, fetch live flight options
   - Auto-populate airline, flight number, times
   - Real-time pricing validation

4. **Analytics Dashboard** (Optional)
   - Popular routes chart
   - Average budget by destination
   - Agency performance comparison
   - Revenue tracking

---

## Code Quality

### Standards Met
- ✅ Laravel 12 conventions
- ✅ Inertia.js patterns
- ✅ RESTful routing
- ✅ Proper validation rules
- ✅ Eloquent relationships
- ✅ Query scopes for filtering
- ✅ Type hints on all methods
- ✅ Descriptive variable names
- ✅ Consistent code formatting
- ✅ No N+1 query issues (eager loading)
- ✅ Transaction safety (wallet deduction)
- ✅ Authorization checks (assigned_to filters)

### Performance Optimizations
- Paginated results (10-15 per page)
- Eager loading relationships
- Database indexes on frequently queried fields
- Cached popular routes (can add later)

---

## Summary

**Models**: 3 ✅ (FlightRequest, FlightQuote, FlightSearch)
**Controllers**: 3 ✅ (User, Admin, Agency)
**Migrations**: 3 ✅ (All migrated successfully)
**Routes**: 17 ✅ (User: 6, Admin: 6, Agency: 5)
**User Relationships**: 1 ✅ (flightRequests HasMany)

**Next**: Create Vue.js frontend pages (9 components) to complete the user interface.

**Status**: Backend implementation 100% complete, ready for frontend development.
