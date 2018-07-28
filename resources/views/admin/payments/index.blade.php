@extends('layouts.app')

@section('contentheader_title')
    Pagos
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 xl:tw-flex xl:tw-justify-start">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2 tw-text-lg">Listado últimos pagos:</h3>
            </div>

        </div><!-- /.box-header -->
        <div class="box-body tw-px-0">
            <table class="table table-striped table-hover tw-min-w-lg">
                <thead>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Plan</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Precio Unitario</th>
                    <th class="text-center">Precio Total</th>
                    <th class="text-center">Creado por</th>
                    <th class="text-center">Fecha Pago</th>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td class="tw-text-left">
                            <img class="tw-h-10 tw-rounded-full tw-mr-2" src="{{ Storage::url( $payment->customer->avatar ) }}" alt="{{ $payment->customer->full_name }}">
                            {{ $payment->customer->full_name }}
                        </td>
                        <td>{{ $payment->membership->plan->name }}</td>
                        <td class="tw-text-center">{{ $payment->membership_quantity }}</td>
                        <td class="tw-text-center">${{ $payment->membership->plan->price_in_dollars }}</td>
                        <td class="tw-text-center">${{ $payment->total_price_in_dollars }}</td>
                        <td class="tw-text-center">{{ $payment->user->full_name }}</td>
                        <td class="tw-text-center">{{ $payment->user->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $payments->links() }}
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection
