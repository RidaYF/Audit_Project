{{-- resources/views/entreprises/create.blade.php --}}
@extends('layouts.app')

@section('content')
@include('pages.entreprises.form', [
    'title' => 'Nouvelle entreprise',
    'subtitle' => 'Ajoutez une nouvelle entreprise à votre portefeuille',
    'action' => route('entreprises.store'),
    'method' => null,
    'entreprise' => null
])
@endsection