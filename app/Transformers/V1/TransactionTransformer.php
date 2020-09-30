<?php 

namespace App\Transformers\Api\V1;

use \App\Models\Transaction;

class TransactionTransformer extends \League\Fractal\TransformerAbstract {

    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(Transaction $transaction)
    {
        return [
            'id' => $transaction->id,
            'parent_id' => $transaction->parent_id,
            'user_id' => $transaction->user_id,
            'date' => $transaction->date,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'stripe_charge' => $transaction->stripe_charge,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ];
    }

}