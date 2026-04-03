{{-- resources/views/entreprises/edit.blade.php --}}
@extends('layouts.app')

@section('content')
@include('pages.entreprises.form', [
    'title' => 'Modifier l\'entreprise',
    'subtitle' => 'Modifiez les informations de ' . $entreprise->nom,
    'action' => route('entreprises.update', $entreprise->id),
    'method' => 'PUT',
    'entreprise' => $entreprise
])
@endsection