@extends('emails.layout')

@section('title', 'Welcome to Our Service!')
@section('header', 'Welcome Aboard!')

@section('content')
    <p>Hi {{$user->name}},</p>
    <p>Thank you for signing up for our service. We’re excited to have you on board.</p>
@endsection

@section('footer-text')
    If you have any questions, feel free to contact us.
@endsection
