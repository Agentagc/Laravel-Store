<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'admin.panel')->name('panel');

// --- users ---//
Route::livewire('/users', 'admin.users.user-list')->name('admin.users.list');

// --- categories ---//
Route::livewire('/categories', 'admin.categories.category-list')->name('admin.categories.list');
Route::livewire('/trashed_categories', 'admin.categories.trashed-category-list')->name('admin.trashed_categories.list');

// --- brands ---//
Route::livewire('/brands', 'admin.brands.brand-list')->name('admin.brands.list');
Route::livewire('/trashed_brands', 'admin.brands.trashed-brand-list')->name('admin.brands.trashed_brand.list');

// --- colors ---//
Route::livewire('/colors', 'admin.colors.color-list')->name('admin.colors.list');

// --- guaranties ---//
Route::livewire('/guaranties', 'admin.guaranties.guaranty-list')->name('admin.guaranties.list');
