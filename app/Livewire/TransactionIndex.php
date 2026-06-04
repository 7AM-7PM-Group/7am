<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\JurnalApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionIndex extends Component
{
    #[Url(except: '')]
    public $date = '';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $page = '';

    #[Url(except: '')]
    public $status = '';

    #[Validate('required')]
    public $cancellation_reason = '';

    public $transaction, $transaction_number = '';

    public function mount()
    {
        $this->date = $this->date ?? date('Y-m-d');

        $this->transaction = collect(['transaction_number' => '6']);
    }

    public function updateDate()
    {
        // dd($this->date);
    }

    public function resetFilters()
    {
        $this->date = '';
        $this->search = '';
        $this->status = '';
        $this->page = '';
    }

    public function export()
    {
        // Ambil data dari DB
        $items = TransactionItem::whereIn('transaction_id', $this->transactions->pluck('id'))
            ->get();

        // Load template
        $templatePath = storage_path('app/public/SalesInvoiceImportTemplateMCTDA.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $startRow = 2; // asumsi header di baris 1
        $row = $startRow;

        foreach ($items as $item) {
            $sheet->setCellValue("A{$row}", $item->invoice_number);
            $sheet->setCellValue("B{$row}", $item->date);
            $sheet->setCellValue("C{$row}", $item->customer_name);
            $sheet->setCellValue("D{$row}", $item->product_name);
            $sheet->setCellValue("E{$row}", $item->qty);
            $sheet->setCellValue("F{$row}", $item->price);
            $sheet->setCellValue("G{$row}", $item->payment_method);
            $sheet->setCellValue("H{$row}", $item->paid_amount);
            $row++;
        }

        // Download file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Sales_Export_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }

    public function importInvoice(JurnalApi $jurnalApi, $id)
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::where('id', $id)->first();

            // dd($transaction);

            if (!$transaction) {
                Session::flash('error', "Transaction not found");
                return;
            }

            if ($transaction->mekari_sync_status != 'pending') {
                Session::flash('error', "Transaction status are $transaction->mekari_sync_status");
                return;
            }

            $memo = $transaction->note;

            if ($transaction->shipping->type == 'delivery') {
                $memo .= "\nDelivery to {$transaction->shipping->name} - {$transaction->shipping->address}";
            } else {
                $memo .= "\nPick Up at {$transaction->shipping->name}";
            }

            $body = [
                "sales_invoice" => [
                    "transaction_date" => $transaction->shipping_date->format('Y-m-d'),
                    "transaction_lines_attributes" => [],
                    "shipping_date" => $transaction->shipping_date->format('Y-m-d'),
                    "shipping_price" => 0,
                    "shipping_address" => $transaction->shipping->address,
                    "is_shipped" => true,
                    "ship_via" => "ship",
                    "reference_no" =>  $transaction->transaction_number,
                    "tracking_no" => $transaction->transaction_number,
                    "address" => '',
                    "term_name" => 'Cash on Delivery',
                    "due_date" => $transaction->user->Businesses->tenor > 0 ? $transaction->shipping_date->addDays($transaction->user->Businesses->tenor)->format('Y-m-d') : $transaction->shipping_date->format('Y-m-d'),
                    "deposit_to_name" => Setting::where('key', 'deposit_to_name')->value('value'),
                    "deposit" => 0,
                    "discount_unit" => $transaction->discount,
                    "witholding_account_name" => Setting::where('key', 'witholding_account_name')->value('value'),
                    "witholding_value" => (int) Setting::where('key', 'witholding_value')->value('value'),
                    "witholding_type" => Setting::where('key', 'witholding_type')->value('value'),
                    "discount_type_name" => "Value",
                    "warehouse_name" => Setting::where('key', 'warehouse_name')->value('value'),
                    "warehouse_code" => Setting::where('key', 'warehouse_code')->value('value'),
                    "person_name" => $transaction->user->Businesses->name ?? "Toko 1",
                    "tags" => [],
                    "email" => $transaction->shipping->email,
                    "transaction_no" => "",
                    "message" => $memo,
                    "memo" => $memo,
                    "custom_id" => "",
                    "source" => "Website B2B 7AM",
                    "use_tax_inclusive" => Setting::where('key', 'use_tax_inclusive')->value('value') === 'true',
                    "tax_after_discount" => Setting::where('key', 'tax_after_discount')->value('value') === 'true',
                ],
            ];

            foreach ($transaction->items as $key => $item) {
                $body['sales_invoice']['transaction_lines_attributes'][] = [
                    "quantity" => $item->qty,
                    "rate" => $item->price,
                    "discount" => 0,
                    'product_id ' => $item->product->jurnal_id,
                    "product_name" => $item->product->name,
                    "line_tax_id" => (int) Setting::where('key', 'line_tax_id')->value('value'),
                    "line_tax_name" => Setting::where('key', 'line_tax_name')->value('value')
                ];
            }

            $response = $jurnalApi->request(
                'POST',
                '/public/jurnal/api/v1/sales_invoices',
                $body
            );
            // return response()->json($response);
            // dd($response);
            // dd($response['sales_invoice']['transaction_no']);

            if (!isset($response['sales_invoice'])) {
                throw new \Exception(($response['error_full_messages'][0] ?? 'Unknown error'));
            }

            $number = $response['sales_invoice']['transaction_no'];

            $transaction->update(['number' => $number, 'mekari_sync_status' => 'synced']);
            DB::commit();
            // dd($response1, $response);
            session()->flash('success', "Invoice $number imported successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "Transaction $transaction->transaction_number import failed: " . $th->getMessage());
            // if (config('app.debug', false)) throw $th;
        }
    }

    public function showCancelOrderModal($id)
    {
        $transaction = Transaction::where('id', $id)->first();

        if (!$transaction) {
            session()->flash('error', "Transaction not found");
            return;
        }

        $this->transaction = $transaction;
        $this->transaction_number = $transaction->transaction_number;
        $this->cancellation_reason = '';
        $this->dispatch('modal-show', name: 'cancel-order');
    }

    public function cancelOrder()
    {
        $transaction = $this->transaction;

        if (!$transaction) {
            session()->flash('error', "Transaction not found");
        }

        if ($transaction->status != 'ordered' || $transaction->mekari_sync_status != 'pending') {
            session()->flash('error', "Your order cannot be cancelled");
        }
        try {
            DB::beginTransaction();
            $transaction->update([
                'cancellation_reason' => $this->cancellation_reason,
            ]);

            $transaction->delete();

            // $this->getHistory();

            DB::commit();
            $this->dispatch('modal-close', name: 'cancel-order');
            Mail::to($transaction->user->email)->queue(new \App\Mail\Order\Cancel($transaction->slug));

            session()->flash('success', "Order has been cancelled");
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug', false)) throw $th;
            session()->flash('error', $th->getMessage());
        }
    }

    public function importPayment(JurnalApi $jurnalApi, $id)
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::where('id', $id)->first();
            if (!$transaction) {
                Session::flash('error', "Transaction not found");
                return;
            }
            if ($transaction->payment->mekari_sync_status != 'pending') {
                Session::flash('error', "Payment status are {$transaction->payment->mekari_sync_status}");
                return;
            }
            $body = [
                "receive_payment" => [
                    "transaction_date" => $transaction->shipping_date->format('Y-m-d'),
                    "records_attributes" => [
                        [
                            "transaction_no" => $transaction->number,
                            "amount" => $transaction->total,
                        ],
                    ],
                    "custom_id" => "ReceivePayment" . $transaction->number,
                    "payment_method_name" => Setting::where('key', 'payment_method_name')->value('value'),
                    "payment_method_id" => (int) Setting::where('key', 'payment_method_id')->value('value'),
                    "is_draft" => false,
                    "deposit_to_name" => Setting::where('key', 'payment_deposit_to_name')->value('value'),
                    "memo" => "Payment order $transaction->number",
                    "witholding_account_name" => Setting::where('key', 'payment_witholding_account_name')->value('value'),
                    "witholding_value" => (int) Setting::where('key', 'payment_witholding_value')->value('value'),
                    "witholding_type" => Setting::where('key', 'payment_witholding_type')->value('value'),
                ],
            ];

            $response1 = $jurnalApi->request(
                'POST',
                '/public/jurnal/api/v1/receive_payments',
                $body
            );

            $transaction->payment->update(['mekari_sync_status' => 'synced']);

            DB::commit();

            Session::flash('success', "Payment for transaction $transaction->number imported successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug', false)) throw $th;
            session()->flash('error', $th->getMessage());
        }
    }

    #[On('update-transaction')]
    public function updateTransaction()
    {
        $this->render();
    }

    public function payTransaction($id)
    {
        // dd($id);
        $this->dispatch('pay-modal', id: $id);
    }

    public function render()
    {
        $transactions = Transaction::latest()
            ->filters([
                'date' => $this->date,
                'status' => $this->status,
                'search' => $this->search,
            ])
            ->paginate(24)->withQueryString();
        return view('livewire.transaction-index', compact('transactions'));
    }
}
