<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        try {
            // Fetch all transactions and current balance
            $transactions = Transaction::all();
            $users = User::all();
            $balances = [];
            foreach ($users as $user) {
                $balance = DB::table('transactions')->where('user_id', $user->id)->sum('amount');
                $balances[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'balance' => $balance,
                ];
            }

            return response()->json(['transactions' => $transactions, 'balances' => $balances], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching transactions and balances', 'error' => $e->getMessage()], 500);
        }
    }


    public function showDeposits()
    {
        try {
            $deposits = Transaction::where('transaction_type', 'Deposit')->get();
            return response()->json(['deposits' => $deposits], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching deposited transactions', 'error' => $e->getMessage()], 500);
        }
    }

    public function showWithdrawals()
    {
        try {
            $withdrawals = Transaction::where('transaction_type', 'Withdrawal')->get();
            return response()->json(['withdrawals' => $withdrawals], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching withdrawal transactions', 'error' => $e->getMessage()], 500);
        }
    }


    public function deposit(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
            ]);

            // Deposit the amount
            DB::beginTransaction();

            $user = User::findOrFail($request->input('user_id'));

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->transaction_type = 'Deposit';
            $transaction->amount = $request->input('amount');
            $transaction->fee = 0; // Assuming no fee for deposits
            $transaction->date = now();
            $transaction->save();

            $user->balance += $request->input('amount');
            $user->save();

            DB::commit();

            return response()->json(['message' => 'Amount deposited successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error depositing amount', 'error' => $e->getMessage()], 500);
        }
    }







    public function withdrawal(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
            ]);

            $user = User::findOrFail($request->input('user_id'));

            // Calculate withdrawal fee based on account type
            $accountType = $user->account_type;
            $baseFeeRate = ($accountType === 'Business') ? 0.025 : 0.015;

            // Check for Business accounts and adjust fee after 50K withdrawal
            if ($accountType === 'Business' && $user->balance < 50000) {
                $baseFeeRate = 0.015;
            }

            // Calculate withdrawal fee
            $withdrawalFee = $baseFeeRate * $request->input('amount');

            // Implement free withdrawal conditions for Individual accounts
            if ($accountType === 'Individual') {
                $today = now();

                if ($today->isFriday() || $today->dayOfWeek === Carbon::FRIDAY) {
                    $withdrawalFee = 0; // Free withdrawal on Fridays
                } elseif ($request->input('amount') <= 1000) {
                    $withdrawalFee = 0; // Free first 1K withdrawal
                } elseif ($request->input('amount') <= 5000) {
                    // Check if this is the first withdrawal of the month
                    $firstWithdrawalOfMonth = Transaction::where('user_id', $user->id)
                        ->whereMonth('date', now()->month)
                        ->where('transaction_type', 'Withdrawal')
                        ->doesntExist();

                    if ($firstWithdrawalOfMonth) {
                        $withdrawalFee = 0; // Free first 5K withdrawal each month
                    }
                }
            }

            // Apply withdrawal fee
            $withdrawalAmount = $request->input('amount') + $withdrawalFee;

            // Deduct the withdrawn amount
            DB::beginTransaction();

            if ($withdrawalAmount > $user->balance) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient balance for withdrawal'],
                ]);
            }

            // Create a new transaction for the withdrawal
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->transaction_type = 'Withdrawal';
            $transaction->amount = $withdrawalAmount;
            $transaction->fee = $withdrawalFee;
            $transaction->date = now();
            $transaction->save();

            // Update user's balance
            $user->balance -= $withdrawalAmount;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Amount withdrawn successfully',
                'withdrawn_amount' => $request->input('amount'),
                'withdrawal_fee' => $withdrawalFee,
                'remaining_balance' => $user->balance,
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error withdrawing amount', 'error' => $e->getMessage()], 500);
        }
    }

}
