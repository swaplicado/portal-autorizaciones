@extends('layouts.principal')

@section('headStyles')
<style>
  .full-unavailable {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #4b007a 0%, #000000 100%);
    color: #fff;
    border-radius: 8px;
    padding: 40px;
  }
  .unavailable-card{
    text-align: center;
    max-width: 780px;
  }
  .unavailable-card h1{font-size:36px;margin-bottom:10px;color:#ffd1ff;}
  .unavailable-card p{font-size:18px;margin-bottom:20px;color:#e6e6e6;}
  .btn-portal{
    background:#6a00d9;border-color:#6a00d9;color:#fff;
  }
  .btn-portal:hover{background:#5500b3;border-color:#5500b3;}
  .small-note{color:#bfbfbf;font-size:14px;margin-top:20px;}
  @media (max-width:576px){
    .unavailable-card h1{font-size:28px}
    .unavailable-card p{font-size:16px}
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="full-unavailable">
      <div class="unavailable-card">
        <h1>Este portal ya no está disponible</h1>
        <p>Las autorizaciones ahora se gestionan en el Portal de Compras. Haga clic en el botón para ir allá</p>
        <a href="https://aeth.swaplicado.com/auth/login" class="btn btn-portal btn-lg" target="_blank" rel="noopener">Ir al nuevo portal</a>
        <div class="small-note">Si el enlace no funciona, contacte al administrador para obtener la URL correcta.</div>
      </div>
    </div>
  </div>
</div>
@endsection
