@extends('admin.layout')

@section('page-title','Application Settings')

@section('content')

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-body">

```
    <form method="POST" action="{{ route('admin.settings.update') }}" class="row g-3">
        @csrf

        @php
        $fields = [
            'app_name'              => 'App Name',
            'support_email'         => 'Support Email',
            'signup_bonus'          => 'Signup Bonus Coins',
            'referral_bonus'        => 'Referral Bonus',
            'min_withdraw'          => 'Minimum Withdrawal',
            'min_recharge'          => 'Minimum Recharge',
            'daily_login_reward'    => 'Daily Login Reward',
            'enable_games'          => 'Enable Games',
            'firebase_server_key'   => 'Firebase Server Key',
            'agora_app_id'          => 'Agora App ID',
            'agora_app_certificate' => 'Agora Certificate',
            'payment_gateway'       => 'Payment Gateway',
            'maintenance_mode'      => 'Maintenance Mode'
        ];
        @endphp

        @foreach($fields as $name => $label)

            @php
                $value = '';

                if(isset($settings)) {

                    if(is_array($settings)) {
                        $value = $settings[$name] ?? '';
                    }
                    elseif(is_object($settings)) {
                        $value = $settings->$name ?? '';
                    }
                }
            @endphp

            <div class="col-md-4">
                <label class="form-label">{{ $label }}</label>

                <input
                    type="text"
                    class="form-control"
                    name="{{ $name }}"
                    value="{{ $value }}"
                >
            </div>

        @endforeach

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                Save Settings
            </button>
        </div>

    </form>

</div>
```

</div>

@endsection
