@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.e-books.update', ['e_book' => $ebook->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('EBooks.title_table') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ $ebook->title }}" placeholder="{{ __('EBooks.Enter_title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('EBooks.enlace') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url"
                        name="url" value="{{ $ebook->url }}" placeholder="{{ __('EBooks.Enter_url') }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('EBooks.description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                        value="{{ $ebook->description }}" rows="3"
                        placeholder="{{ __('EBooks.Enter_description') . ' ' . __('EBooks.optional') }}">{{ $ebook->description }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label">{{ __('EBooks.author') }}</label>
                    <input type="text" class="form-control @error('author') is-invalid @enderror" id="author"
                        name="author" value="{{ $ebook->author }}"
                        placeholder="{{ __('EBooks.Enter_author') . ' ' . __('EBooks.optional') }}">
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="price" class="form-label">{{ __('course_platform.price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                        id="price" name="price" value="{{ $ebook->price }}"
                        placeholder="{{ __('course_platform.enter_price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="mb-3">
                    <label for="number_of_pages" class="form-label">{{ __('EBooks.number_of_pages') }}</label>
                    <input type="number" class="form-control @error('number_of_pages') is-invalid @enderror" min="1"
                        id="number_of_pages" value="{{ $ebook->number_of_pages }}" name="number_of_pages"
                        placeholder="{{ __('EBooks.Enter_number_of_pages') . ' ' . __('EBooks.optional') }}">
                    @error('number_of_pages')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="language" class="form-label">{{ __('EBooks.language') }}</label>
                    <input class="form-control @error('language') is-invalid @enderror" id="language" name="language"
                        value="{{ $ebook->language }}"
                        placeholder="{{ __('EBooks.Enter_language') . ' ' . __('EBooks.optional') }}" />
                    @error('language')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="cover_image" class="form-label">{{ __('EBooks.cover_image') }}</label>
                    <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image"
                        name="cover_image" accept="image/*">
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('EBooks.edit') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix('modules/product/style.css') }}'>
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush
