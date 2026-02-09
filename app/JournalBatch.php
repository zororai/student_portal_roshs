<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\LedgerPostingService;

class JournalBatch extends Model
{
    protected $fillable = [
        'reference',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'created_by',
        'approved_by',
        'posted_by',
        'approved_at',
        'posted_at',
    ];

    protected $casts = [
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Generate unique journal reference number
     */
    public static function generateReference()
    {
        $prefix = 'JB-' . date('Ymd') . '-';
        $lastBatch = self::where('reference', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastBatch) {
            $lastNumber = intval(substr($lastBatch->reference, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Calculate totals from entries
     */
    public function calculateTotals()
    {
        $this->total_debit = $this->entries()->sum('debit_amount');
        $this->total_credit = $this->entries()->sum('credit_amount');
        $this->save();
    }

    /**
     * Check if batch is balanced
     */
    public function isBalanced()
    {
        return round($this->total_debit, 2) === round($this->total_credit, 2);
    }

    /**
     * Approve the journal batch
     */
    public function approve($userId)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft journals can be approved');
        }

        if (!$this->isBalanced()) {
            throw new \Exception('Journal batch is not balanced');
        }

        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->save();

        AuditTrail::log(
            'journal_approved',
            "Journal batch {$this->reference} approved",
            $this,
            ['status' => 'draft'],
            ['status' => 'approved']
        );
    }

    /**
     * Post the journal batch to ledger
     */
    public function post($userId)
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Only approved journals can be posted');
        }

        $ledgerService = new LedgerPostingService();
        
        // Prepare debit and credit entries
        $debitEntries = [];
        $creditEntries = [];

        foreach ($this->entries as $entry) {
            $account = $entry->ledgerAccount;
            
            if ($entry->debit_amount > 0) {
                $debitEntries[] = [
                    'account_code' => $account->account_code,
                    'amount' => $entry->debit_amount,
                    'description' => $entry->narration,
                ];
            }
            
            if ($entry->credit_amount > 0) {
                $creditEntries[] = [
                    'account_code' => $account->account_code,
                    'amount' => $entry->credit_amount,
                    'description' => $entry->narration,
                ];
            }
        }

        // Post to ledger
        $ledgerService->postTransaction(
            $debitEntries,
            $creditEntries,
            [
                'reference_number' => $this->reference,
                'entry_date' => now(),
                'description' => $this->description,
                'created_by' => $userId,
                'notes' => "Posted from journal batch {$this->reference}",
            ]
        );

        $this->status = 'posted';
        $this->posted_by = $userId;
        $this->posted_at = now();
        $this->save();

        AuditTrail::log(
            'journal_posted',
            "Journal batch {$this->reference} posted to ledger",
            $this,
            ['status' => 'approved'],
            ['status' => 'posted']
        );
    }

    /**
     * Check if batch can be edited
     */
    public function canEdit()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if batch can be approved
     */
    public function canApprove()
    {
        return $this->status === 'draft' && $this->isBalanced();
    }

    /**
     * Check if batch can be posted
     */
    public function canPost()
    {
        return $this->status === 'approved';
    }
}
