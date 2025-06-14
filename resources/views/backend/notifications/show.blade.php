@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection



@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <i class="{{ $module_icon }}"></i> {{ $module_title }} <small
                    class="text-muted">{{ __($module_action) }}</small>

                <x-slot name="subtitle">
                    @lang(':module_name Management Dashboard', ['module_name' => Str::title($module_name)])
                </x-slot>
                <x-slot name="toolbar">
                    <a href="{{ route("backend.$module_name.index") }}" class="btn btn-secondary mt-1 btn-sm"
                        data-bs-toggle="tooltip" title="{{ __(ucwords($module_name)) }} List"><i class="fas fa-list"></i>
                        Lista</a>
                </x-slot>
            </x-backend.section-header>

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <?php $data = json_decode($$module_name_singular->data);

                            ?>
                            <tbody>
                                <tr>
                                    <th>Título</th>
                                    <th>
                                        {{ $data->subject }}
                                    </th>
                                </tr>
                                <tr>
                                    <th>Texto</th>
                                    <td>
                                        {!! $data->text !!}
                                    </td>
                                </tr>
                                @if ($data->url_backend != '')
                                    <tr>
                                        <th>URL Backend</th>
                                        <td>
                                            Backend: <a href="{{ $data->url_backend }}">{{ $data->url_backend }}</a>
                                        </td>
                                    </tr>
                                @endif
                                @if ($data->url_frontend != '')
                                    <tr>
                                        <th>URL Frontend</th>
                                        <td>
                                            Frontend: <a href="{{ $data->url_frontend }}">{{ $data->url_frontend }}</a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    <small class="float-end text-muted">
                        Actualizado: {{ $$module_name_singular->updated_at->diffForHumans() }},
                        Creado: {{ $$module_name_singular->created_at->isoFormat('LLLL') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
