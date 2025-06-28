<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\PurchaseRequest;

class AutoGeneratePurchaseRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase-request:auto-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate purchase requests for items below reorder point';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = Item::whereColumn('stock', '<', 'reorder_point')->get();

        foreach ($items as $item) {
            $exists = PurchaseRequest::where('item_id', $item->id)
                ->whereIn('status', ['waiting_approval', 'approved'])
                ->exists();

            if (!$exists) {
                PurchaseRequest::create([
                    'item_id' => $item->id,
                    'current_stock' => $item->stock,
                    'reorder_point' => $item->reorder_point,
                    'requested_quantity' => $item->reorder_point - $item->stock,
                    'status' => 'waiting_approval',
                ]);
            }
        }

        $this->info('Auto purchase requests generated.');
    }
}
