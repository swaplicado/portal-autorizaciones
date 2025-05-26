@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')

@endsection

@section('content')
  
<div class="card">
    <div class="card-header">
        <h3>Manuales</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table_purchase_orders" width="100%" cellspacing="0">
                <thead>
                    <th></th>
                </thead>
                <tbody>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/rmautorizar.pdf')}}" target="_blank">
                                    <h3>Requisiciones de materiales por autorizar</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/todasrm.pdf')}}" target="_blank">
                                    <h3>Todas las requisiciones de materiales</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/misrm.pdf')}}" target="_blank">
                                    <h3>Mis requisiciones de materiales</h3>
                                </a>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection