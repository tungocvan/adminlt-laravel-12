<?php
use App\Models\User;
use function Livewire\Volt\{state,computed,title};
title('Users');

state(['count' => 0]);

$increment = fn () => $this->count++;
$descrement = fn () => $this->count--;
$countUser = computed(function () {
    return User::all();
});

?>

<div>
    <h3>{{ $count }}</h3>
    <button wire:click="increment">+</button>
    <button wire:click="descrement">-</button>
    <h1>Search Results:</h1>
 
    <ul>
        @foreach($this->countUser as $post)
            <li wire:key="{{ $post->id }}">{{ $post->name }}</li>
        @endforeach
    </ul>
</div>
