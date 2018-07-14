@extends('layouts.app')

@section('contentheader_title')
        Tipos de Membresías
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h3>Seleccionar:</h3>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <div class="lg:tw-flex tw-flex-wrap">
                @foreach($membershipTypes as $membershipType)
                    <div class="lg:tw-w-1/2 {{ $loop->iteration % 2 === 0 ? 'lg:tw-pl-4' : '' }}">
                        <div class="tw-shadow tw-py-4 tw-px-4 tw-rounded-lg md:tw-flex tw-items-center tw-justify-between tw-mb-4 tw-bg-grey-lighter">
                            <div class="tw-text-center tw-bg-flex lg:tw-text-center">
                                <span class="fa fa-id-card-o fa-2x tw-text-blue-darker tw-block tw-mb-1" aria-hidden="true"></span>
                                <h2 class="tw-text-black tw-uppercase tw-text-sm">{{ $membershipType->name }}</h2>
                            </div>
                            <div class="tw-mb-4 lg:tw-mb-0">
                                <span class="tw-text-xl tw-text-blue-darker tw-rounded tw-py-2 tw-px-4 tw-font-bold"> ${{ $membershipType->price_in_dollars }}</span>
                            </div>
                            <div>
                                <a class="vg-button tw-bg-indigo-dark hover:tw-bg-indigo-light" href="{{ route('admin.memberships.create', $membershipType) }}">
                                    <i class="fa fa-shopping-cart tw-mr-2" aria-hidden="true"></i>
                                    <span>Comprar</span>
                                </a>
                            </div>
                        </div><!-- /.end lg:tw-w-1/2 -->
                    </div><!-- /.end tw-flex -->
                @endforeach
            </div><!-- /.end tw-flex -->
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection

