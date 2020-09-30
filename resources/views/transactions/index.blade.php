@extends('layouts.app', [
    'title' => 'Billing',
    'active_menu_parent' => 'reports',
    'active_menu_item' => 'transactions'
])

@section('content')

<div class="row">
	{{-- 
    <div class="col-md-6 transaction-balance-info">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>$ {{ $balance }}</h3>
                <h4>Balance</h4>
                
                <p>Next Bill: ${{ Auth::user()->billing_amount }} on {{ displayDate(Auth::user()->billing_next_bill_at) }}
                
            </div>
            <div class="icon">
                <i class="fa fa-balance-scale" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    --}}
    <div class="col-md-6 transaction-payment-info">
        <div class="panel panel-primary">
            <div class="panel-body">
                @if (Auth::user()->stripe_customer_id)
                    <p>All charges will be automatically billed to your <strong>{{ Auth::user()->card_brand }}</strong> credit card ending in <strong>{{ Auth::user()->card_last_four }}</strong>.
                @else
                    <p>No Payment Method Yet</p>
                @endif

                <p>
                    <a href="{{ secure_url('settings/billing') }}" class="btn btn-primary btn-lg">Update Payment Method</a>
                </p>

            </div>
        </div>
    </div>
</div>

<div class="panel">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>By</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr class="row-hover">
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->user->first_name . " " . $transaction->user->last_name }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ displayDate($transaction->created_at) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Looks like you don't have any recent transactions</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{ $transactions->appends(request()->except('page'))->withPath('/transactions')->links() }}

@endsection