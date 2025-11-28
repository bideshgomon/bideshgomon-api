# Phase 4: Wallet System - Implementation Plan

## Overview
Implement digital wallet system for users with transaction tracking, balance management, and Bangladesh currency formatting.

## Database Schema

### Wallets Table
```sql
CREATE TABLE wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'BDT',
    status ENUM('active', 'suspended', 'closed') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Wallet Transactions Table
```sql
CREATE TABLE wallet_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wallet_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM('credit', 'debit') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    balance_before DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    description VARCHAR(255),
    reference_type VARCHAR(50), -- 'referral_reward', 'service_payment', 'manual_adjustment'
    reference_id BIGINT UNSIGNED,
    status ENUM('pending', 'completed', 'failed', 'reversed') DEFAULT 'pending',
    processed_by BIGINT UNSIGNED, -- admin user who processed (if manual)
    processed_at TIMESTAMP NULL,
    metadata JSON, -- Additional transaction data
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

## Models

### Wallet Model
```php
// app/Models/Wallet.php
class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'currency', 'status'];
    protected $casts = ['balance' => 'decimal:2'];
    
    // Relationships
    public function user() // belongsTo User
    public function transactions() // hasMany WalletTransaction
    
    // Methods
    public function credit($amount, $description, $referenceType = null, $referenceId = null)
    public function debit($amount, $description, $referenceType = null, $referenceId = null)
    public function hasBalance($amount)
    public function getFormattedBalance() // Returns ৳5,000.00
}
```

### WalletTransaction Model
```php
// app/Models/WalletTransaction.php
class WalletTransaction extends Model
{
    protected $fillable = [...];
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];
    
    // Relationships
    public function wallet() // belongsTo Wallet
    public function processedBy() // belongsTo User (admin)
    
    // Methods
    public function complete()
    public function fail($reason)
    public function reverse()
}
```

## Service Layer

### WalletService
```php
// app/Services/WalletService.php
class WalletService
{
    public function createWallet(User $user): Wallet
    public function creditWallet(Wallet $wallet, float $amount, string $description, ...): WalletTransaction
    public function debitWallet(Wallet $wallet, float $amount, string $description, ...): WalletTransaction
    public function getBalance(User $user): float
    public function getTransactionHistory(User $user, int $perPage = 15)
    public function canDebit(User $user, float $amount): bool
    public function reverseTransaction(WalletTransaction $transaction): void
}
```

## Routes

### User Routes (web.php)
```php
Route::middleware(['auth', 'role:user|agency|consultant'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
});
```

### Admin Routes
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/wallets', [Admin\WalletController::class, 'index'])->name('wallets.index');
    Route::get('/wallets/{wallet}', [Admin\WalletController::class, 'show'])->name('wallets.show');
    Route::post('/wallets/{wallet}/credit', [Admin\WalletController::class, 'credit'])->name('wallets.credit');
    Route::post('/wallets/{wallet}/debit', [Admin\WalletController::class, 'debit'])->name('wallets.debit');
    Route::post('/transactions/{transaction}/reverse', [Admin\WalletController::class, 'reverseTransaction'])->name('transactions.reverse');
});
```

## Vue Components

### User Wallet Pages
```
resources/js/Pages/Wallet/
├── Index.vue           # Wallet dashboard (balance, recent transactions)
├── Transactions.vue    # Full transaction history with filters
```

### Admin Wallet Pages
```
resources/js/Pages/Admin/Wallets/
├── Index.vue           # All wallets list with balances
├── Show.vue            # Single wallet details + transaction history
└── Partials/
    ├── CreditWalletForm.vue   # Add money to wallet
    ├── DebitWalletForm.vue    # Deduct money from wallet
    └── TransactionsList.vue   # Reusable transactions table
```

## Features

### User Features
- ✅ View wallet balance with BD currency (৳)
- ✅ View transaction history with pagination
- ✅ Filter transactions by type (credit/debit)
- ✅ Download transaction statement (CSV/PDF)
- ✅ Transaction details modal (amount, date, reference)

### Admin Features
- ✅ View all user wallets
- ✅ Search wallets by user
- ✅ Manual credit/debit with reason
- ✅ Transaction reversal with audit log
- ✅ Suspend/activate wallets
- ✅ Wallet statistics (total balance, transaction volume)

## Bangladesh Localization

### Currency Formatting
- All amounts displayed as: **৳5,000.00**
- Use `format_bd_currency()` helper on backend
- Use `formatCurrency()` from composable on frontend

### Transaction Descriptions
Use Bangladesh-friendly terminology:
- "মোবাইল রিচার্জ" (Mobile Recharge)
- "সেবা পেমেন্ট" (Service Payment)
- "রেফারেল বোনাস" (Referral Bonus)

## Validation Rules

### Transaction Amount
```php
'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
'description' => ['required', 'string', 'max:255'],
'reference_type' => ['nullable', 'in:referral_reward,service_payment,manual_adjustment'],
```

### Wallet Status
```php
'status' => ['required', 'in:active,suspended,closed'],
```

## Security Considerations

1. **Authorization**: Only admins can manually adjust wallets
2. **Audit Trail**: All transactions logged with balance snapshots
3. **Concurrency**: Use database transactions for balance updates
4. **Validation**: Prevent negative balances (unless business allows)
5. **Rate Limiting**: Limit admin wallet operations to prevent abuse

## Testing Checklist

- [ ] Create wallet automatically when user registers
- [ ] Credit wallet increases balance correctly
- [ ] Debit wallet decreases balance correctly
- [ ] Cannot debit more than available balance
- [ ] Transaction history shows correct records
- [ ] Balance snapshots (before/after) are accurate
- [ ] Admin can credit/debit any user wallet
- [ ] Admin can reverse transactions
- [ ] Bangladesh currency formatting works
- [ ] Wallet suspension prevents transactions

## Implementation Steps

1. **Create Migrations**: wallets, wallet_transactions tables
2. **Create Models**: Wallet, WalletTransaction with relationships
3. **Create Service**: WalletService with transaction logic
4. **Create Controllers**: WalletController (user), Admin\WalletController (admin)
5. **Create Routes**: User wallet routes, admin wallet routes
6. **Create Vue Pages**: User wallet pages, admin wallet pages
7. **Auto-create Wallet**: Update User registration to create wallet
8. **Test Everything**: Manual tests + write automated tests

## Estimated Time
- Backend (migrations, models, service): **30 minutes**
- Controllers + Routes: **20 minutes**
- Vue Components (user): **25 minutes**
- Vue Components (admin): **30 minutes**
- Testing: **15 minutes**
- **Total: ~2 hours**

## Dependencies
- Phase 0: Bangladesh helpers ✅
- Phase 1: Roles system ✅
- Phase 2: User profiles ✅
- Phase 3: Profile UI ✅

## Next Phase Preview
**Phase 5: Referral System**
- Links to wallet for reward payouts
- Referral codes, campaigns, tracking
- Reward tiers and gamification
