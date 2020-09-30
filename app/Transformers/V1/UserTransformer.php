<?php

namespace App\Transformers\Api\V1;

use \App\Models\User;

class UserTransformer extends \League\Fractal\TransformerAbstract {

	protected $availableIncludes = [

	];

	protected $defaultIncludes = [

    ];

	public function transform(User $user)
	{
		return [
			'id' => $user->id,

			// Contact Info
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'full_name' => $user->full_name,
			'email' => $user->email,
			'company_name' => $user->company_name,
			'address' => $user->address,
			'secondary_address' => $user->secondary_address,
			'city' => $user->city,
			'state' => $user->state,
			'zip' => $user->zip,
			'country' => $user->country,
			'full_address' => $user->full_address,
			'phone' => $user->phone,
			'display_phone' => $user->display_phone,
			'website' => $user->website,
			'companyId' => $user->company_id,
			'company_rep' => $user->company_rep,

			// Admin
			'ip' => $user->ip,
			'is_approved' => $user->is_approved,
			'is_setup_contact' => $user->is_setup_contact,
			'is_suspended' => $user->is_suspended,
			'admin_notes' => $user->admin_notes,
			
			// Billing
			'stripe_customer_id' => $user->stripe_customer_id,
			'card_brand' => $user->card_brand,
			'card_last_four' => $user->card_last_four,
			'card_expiration' => $user->card_expiration,

			// Dates
			'created_at' => $user->created_at,
			'updated_at' => $user->updated_at,
		];
	}

}