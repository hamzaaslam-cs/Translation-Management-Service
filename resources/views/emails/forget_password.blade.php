@extends('emails.layout')

@section('title', 'Welcome to Our Service!')
@section('header', 'Welcome Aboard!')

@section('content')
    <h1>Forget Password Email</h1>
    You can reset password from bellow link:
    <a href="{{ route('reset.password.get', $token) }}">Reset Password</a>
    <p>Thank you for signing up for our service. We’re excited to have you on board.</p>
@endsection

@section('footer-text')
    If you have any questions, feel free to contact us.
@endsection
