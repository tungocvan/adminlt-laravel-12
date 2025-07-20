@extends('adminlte::page')
@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
@stop

@section('content')
<div class="row mb-2">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Profile Information') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("Update your account's profile information and email address.") }}
                        </p>
                        
                        <form method="POST" action="{{ route('admin.profile-update') }}">
                            @csrf
                            @method('patch')
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name</strong>
                                    <input type="text" name="name" placeholder="Name" class="form-control" value="{{ old('name', $user->name) }}">
                                </div>
                                <div class="form-group">
                                    <strong>Email</strong>
                                    <input type="email" name="email" placeholder="Email" class="form-control" value="{{ old('email', $user->email) }}">
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
                                </div>
                            </div>
                        </form>
                        <strong class="mt-1 text-sm text-gray-600">
                            {{ __("User Created At: ").$newDate }}
                        </strong>
                </div>
            </div>


        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Update Password') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("Ensure your account is using a long, random password to stay secure.") }}
                            @if (session('status') === 'password-updated')
                            <p class="text-sm text-red-600">
                                {{ __("Password Updated Success.") }}
                            </p>
                            @endif
                            @if (session('status') === 'password-ko-khop')
                            <p class="text-sm text-red-600">
                                {{ __("Password Ko Khop.") }}
                            </p>
                            @endif
                            @if (session('status') === 'password-ko-dung')
                            <p class="text-sm text-red-600">
                                {{ __("Password Ko Dung.") }}
                            </p>
                            @endif
                        </p>
                        <form method="POST" action="{{ route('admin.profile-password') }}">
                            @csrf
                            @method('put')
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Current Password</strong>
                                    <input type="password" name="password-old" placeholder="Password" class="form-control" value="">
                                </div>
                                <div class="form-group">
                                    <strong>Password</strong>
                                    <input type="password" name="password" placeholder="Password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <strong>Confirm Password</strong>
                                    <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control" value="">
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                </div>
                            </div>
                        </form>
                </div>
            </div>


        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Delete Account') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.") }}
                        </p>
                        <form method="POST" action="{{ route('admin.profile-destroy') }}">
                            @csrf
                            @method('delete')
                            <button type= 'submit' class="btn btn-danger btn-sm mt-2 mb-3">
                                {{ __('Delete Account') }}
                            </button>
                        </form>

                </div>
            </div>


        </div>
    </div>
</div>

@session('success')
    <div class="alert alert-success" role="alert">

    </div>
@endsession



<p class="text-center text-primary"><small>Tutorial by Từ Ngọc Vân.</small><strong class="mt-1 text-sm text-gray-600">
    {{ __("IP your address: "). $clientIP }}
</strong></p>
@endsection
