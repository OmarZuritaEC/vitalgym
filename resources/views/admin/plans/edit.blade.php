@extends('layouts.app')

@section('contentheader_title')
    Editanto Plan <span class="tw-font">N# {{ $plan->name  }}</span>
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Actualizar plan</h2>
        </div>
        <div class="box-body">
            <form action="{{ route('admin.plans.update', $plan) }}" method="post" autocomplete="off">
                @csrf
                @method('PATCH')

                <div class="form-group {{ $errors->has('name') ? ' has-error': '' }}">
                    <label>Nombre:</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name', $plan->name) }}">
                    @if ($errors->has('name'))
                        <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('price') ? ' has-error': '' }}">
                    <label>Precio:</label>
                    <div class="tw-flex">
                        <div class="tw-px-3 tw-border tw-py-2 tw-leading-none">
                            <i class="fa fa-dollar"></i>
                        </div>
                        <input type="text"
                               name="price"
                               class="form-control tw-rounded-l-none tw-border-l-0"
                               id="js-input-price"
                               value="{{ old('price', $plan->price) }}">
                    </div>

                    @if ($errors->has('price'))
                        <span class="help-block">{{ $errors->first('price') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('is_premium') ? ' has-error': '' }}">
                    <label>Premium:</label>
                    <select name="is_premium" class="form-control">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    @if ($errors->has('is_premium'))
                        <span class="help-block">{{ $errors->first('is_premium') }}</span>
                    @endif
                </div>


                <div class="tw-mt-6">
                    <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center">
                        <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                        Actualizar
                    </button>
                    <a href="{{ route('admin.memberships.index') }}"
                       class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                        <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                        Volver
                    </a>
                </div><!-- ./End tw-m-b-->
            </form>
        </div>
    </div><!-- ./End box default-->
@endsection

@push('footer-scripts')
{{--    <script src="{{ asset('plugins/input-mask/inputmask.min.js') }}"></script>
    <script src="{{ asset('plugins/input-mask/inputmask.numeric.extensions.min.js') }}"></script>
    <script src="{{ asset('plugins/input-mask/jquery.inputmask.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            var inputPrice = $('#js-input-price');
            inputPrice.inputmask('decimal', {
                digits: 2,
                groupSeparator: ".",
                autoGroup: true,
                numericInput: true,
                unmaskAsNumber: true,
                rightAlign: false,
                autoUnmask: true,
                removeMaskOnSubmit: true,
            });
        });
    </script>--}}
@endpush
