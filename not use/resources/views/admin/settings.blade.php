@extends('admin.layout')

@section('content')

<h3 class="mb-4">Application Settings</h3>

<ul class="nav nav-tabs" id="settingsTab">

    <li class="nav-item">
        <button class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#general">
            General
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#coin">
            Coin
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#withdraw">
            Withdraw
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#recharge">
            Recharge
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#task">
            Task
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#game">
            Game
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#firebase">
            Firebase
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#agora">
            Agora
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#payment">
            Payment
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#maintenance">
            Maintenance
        </button>
    </li>

</ul>

<div class="tab-content bg-white p-4 border border-top-0">

    <!-- General -->

    <div class="tab-pane fade show active" id="general">

        <h5>General Settings</h5>

        <div class="mb-3">
            <label>App Name</label>
            <input type="text" class="form-control" value="Voice Chat">
        </div>

        <div class="mb-3">
            <label>Support Email</label>
            <input type="text" class="form-control">
        </div>

    </div>

    <!-- Coin -->

    <div class="tab-pane fade" id="coin">

        <h5>Coin Settings</h5>

        <div class="mb-3">
            <label>Signup Bonus Coins</label>
            <input type="number" class="form-control" value="100">
        </div>

        <div class="mb-3">
            <label>Referral Bonus</label>
            <input type="number" class="form-control" value="50">
        </div>

    </div>

    <!-- Withdraw -->

    <div class="tab-pane fade" id="withdraw">

        <h5>Withdrawal Settings</h5>

        <div class="mb-3">
            <label>Minimum Withdrawal</label>
            <input type="number" class="form-control" value="1000">
        </div>

    </div>

    <!-- Recharge -->

    <div class="tab-pane fade" id="recharge">

        <h5>Recharge Settings</h5>

        <div class="mb-3">
            <label>Minimum Recharge</label>
            <input type="number" class="form-control" value="100">
        </div>

    </div>

    <!-- Task -->

    <div class="tab-pane fade" id="task">

        <h5>Task Settings</h5>

        <div class="mb-3">
            <label>Daily Login Reward</label>
            <input type="number" class="form-control">
        </div>

    </div>

    <!-- Game -->

    <div class="tab-pane fade" id="game">

        <h5>Game Settings</h5>

        <div class="mb-3">
            <label>Enable Games</label>
            <select class="form-control">
                <option>Yes</option>
                <option>No</option>
            </select>
        </div>

    </div>

    <!-- Firebase -->

    <div class="tab-pane fade" id="firebase">

        <h5>Firebase Settings</h5>

        <div class="mb-3">
            <label>Firebase Server Key</label>
            <textarea class="form-control"></textarea>
        </div>

    </div>

    <!-- Agora -->

    <div class="tab-pane fade" id="agora">

        <h5>Agora Settings</h5>

        <div class="mb-3">
            <label>App ID</label>
            <input type="text" class="form-control">
        </div>

        <div class="mb-3">
            <label>App Certificate</label>
            <input type="text" class="form-control">
        </div>

    </div>

    <!-- Payment -->

    <div class="tab-pane fade" id="payment">

        <h5>Payment Options</h5>

        <div class="form-check">
            <input class="form-check-input" type="checkbox">
            <label class="form-check-label">
                Razorpay
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox">
            <label class="form-check-label">
                Paytm
            </label>
        </div>

    </div>

    <!-- Maintenance -->

    <div class="tab-pane fade" id="maintenance">

        <h5>Maintenance Mode</h5>

        <div class="form-check form-switch">

            <input class="form-check-input"
                   type="checkbox">

            <label class="form-check-label">
                Enable Maintenance Mode
            </label>

        </div>

    </div>

</div>

<div class="mt-3">
    <button class="btn btn-success">
        Save Settings
    </button>
</div>

@endsection