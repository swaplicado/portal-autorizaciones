@extends('layouts.principal')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
      
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10" style="text-align: center">
          <figure>
            <blockquote class="blockquote" style="margin-right: -4%; margin-left: -5%;">
              <h2>Bienvenido</h2>
              <h2>{{\Auth::user()->names}}</h2>
              <h2>a</h2>
              <h2 style="white-space: nowrap;">Portal autorizaciones</h2>
            </blockquote>
            <figcaption class="blockquote-footer" style="padding-left: 7%">
                <div class="row">
                  @if(\Auth::user()->hasPermissionByKeyCode('autorizador.dps'))
                    <div class="col-12 col-md-6">
                      <a type="button" href="{{ route('dps.pending') }}" class="btn btn-primary mb-2">OC por autorizar</a>
                    </div>
                  @endif
                  @if(\Auth::user()->hasPermissionByKeyCode('autorizador.dps'))
                    <div class="col-12 col-md-6">
                      <a type="button" href="{{ route('dps.index') }}" class="btn btn-primary">Todas las OC</a>
                    </div>
                  @endif
                </div>
            </figcaption>
          </figure>
        </div>
        <div class="col-md-1">
        </div>
      </div>
    </div>
  </div>
@endsection