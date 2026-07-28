<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'admin.panel')->name('panel');
Route::livewire('/users', 'admin.users.user-list')->name('admin.users.list');

Route::livewire('/categories', 'admin.categories.category-list')->name('admin.categories.list');
Route::livewire('/trashed_categories', 'admin.categories.trashed-category-list')->name('admin.trashed_categories.list');
