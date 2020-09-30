<?php 

namespace App\Transformers\Api\V1;

use \App\Models\Invoice;

class InvoiceTransformer extends \League\Fractal\TransformerAbstract {

    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(Invoice $invoice)
    {
        return [
            'id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'date' => $invoice->date,
            'notes' => $invoice->notes,
            'amount' => $invoice->amount,
            'created_at' => $invoice->created_at,
            'updated_at' => $invoice->updated_at,
        ];
    }

}