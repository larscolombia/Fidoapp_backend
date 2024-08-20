@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">{{ __('EBooks.title_table') }}</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $ebook->title }}" placeholder="{{ __('EBooks.Enter_title') }}" readonly>
            </div>

            <div class="mb-3">
                <label for="url" class="form-label">{{ __('EBooks.enlace') }}</label>
                <input type="url" class="form-control" id="url" name="url" value="{{ $ebook->url }}" placeholder="{{ __('EBooks.Enter_url') }}" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('EBooks.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('EBooks.Enter_description') . ' ' . __('EBooks.optional') }}" readonly>{{ $ebook->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="author" class="form-label">{{ __('EBooks.author') }}</label>
                <input type="text" class="form-control" id="author" name="author" value="{{ $ebook->author }}" placeholder="{{ __('EBooks.Enter_author') . ' ' . __('EBooks.optional') }}" readonly>
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">{{ __('EBooks.cover_image') }}</label>
                @if ($ebook->cover_image)
                    <img src="{{ asset($ebook->cover_image) }}" class="img-fluid" alt="{{ $ebook->title }}">
                @else
                    <p>{{ __('EBooks.No_cover_image') }}</p>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('backend.e-books.edit', ['e_book' => $ebook->id]) }}" class="btn btn-primary">{{ __('EBooks.edit') }}</a>
                <a href="{{ route('backend.e-books.index') }}" class="btn btn-secondary">{{ __('EBooks.back') }}</a>
            </div>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush