@extends('layouts.layout')

@section('additionalThemeJs')
    <script type="text/javascript" src="{{ asset('/') }}js/select2.min.js"></script>
    <script type="text/javascript" src="{{ asset('/') }}js/pnotify.min.js"></script>
@endsection

@section('additionalAppJs')
    <script type="text/javascript" src="{{ asset('/') }}js/systemmanaging.js"></script>
    <script type="text/javascript">
        var baseUrl = "{{ asset('/') }}";
    </script>
@endsection

@section('systemmanaging')
    class="active"
@endsection

@section('pageHeader')
    <!-- Page header -->
    <div class="page-header page-header-transparent">
        <div class="page-header-content">
            <div class="page-title">
                <h4> <span class="text-semibold">Menadžment sistema</span></h4>

                <ul class="breadcrumb position-left">
                    <li><a href="{{ url('/home') }}">Početna</a></li>
                    <li><a href="#">Menadžment sistema</a></li>
                    <li><a href="#">Lokacija aplikacije</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /page header -->
@endsection

@section('content')
    <div class="panel panel-flat">
        <div class="panel-body">
            <div class="tabbable tab-content-bordered">
                <ul class="nav nav-tabs nav-justified bg-telekom-slova">
                    <li class="active"><a href="#" data-toggle="tab">Stavka fakture</a></li>
                    <li><a href="#">Tip ugovora</a></li>
                    <li><a href="#">Tip servisa</a></li>
                    <li><a href="#">Tip tehnologije</a></li>
                    <li><a href="#">Partner</a></li>
                    <li><a href="#">Servis</a></li>
                    <li><a href="#">Senzor</a></li>
                    <li><a href="#">Lokacija aplikacije</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 1) active @endif" id="stavkeFakture">

                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Naziv</th>
                                    <th>Tip naknade</th>
                                    <th>Mesečna naknada</th>
                                    <th>Zavisi od vrste senzora</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($stavkeFakture as $sf)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sf->naziv }}</td>
                                        <td>@if($sf->tipNaknade==1) Mesečna @else Jednokratna @endif</td>
                                        <td>{{ $sf->naknada }}</td>
                                        <td><input type="checkbox" @if($sf->zavisiOdVrsteSenzora == 1) checked @endif disabled /></td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/1/'.$sf->idStavkaFakture) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeStavkeFakture({{ $sf->idStavkaFakture }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <form class="form-horizontal" @if(isset($stavka))  action="{{ route('editStavkaFakture') }}" @else  action="{{ route('addStavkaFakture') }}" @endif method="POST" id="formStavkaFakture">
                            {{ csrf_field() }}
                            @if(isset($stavka) && count($stavka)>0)
                                <input type="hidden" name="idStavkaFakture" id="idStavkaFakture" value="{{ $stavka[0]->idStavkaFakture }}" />
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <h5 class="panel-title">Dodaj novu stavku fakture</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="nazivStavkeFakture">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="nazivStavkeFakture" id="nazivStavkeFakture" class="form-control" placeholder="Naziv stavke fakture" @if(isset($stavka) && count($stavka)>0) value="{{ $stavka[0]->naziv }}" @else value="{{ old('nazivStavkaFakture') }}" @endif>
                                                    <label id="nazivStavkeFaktureError" for="naziv" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('nazivStavkeFakture'))
                                                        <label id="nazivStavkeFaktureError2" for="nazivStavkeFakture" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="mesecnaNaknada">Naknada:</label>
                                                <div class="col-lg-9">
                                                    <input type="number" name="mesecnaNaknada" id="mesecnaNaknada" class="form-control" step="0.1" @if(isset($stavka) && count($stavka)>0) value="{{ $stavka[0]->naknada }}" @else value="0" @endif>
                                                    <label id="mesecnaNaknadaError" for="mesecnaNaknada" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('mesecnaNaknada'))
                                                        <label id="mesecnaNaknadaError2" for="mesecnaNaknada" class="validation-error-label" style="display: block;">Mora biti vece od 0!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="tipNaknade">Tip naknade:</label>
                                                <div class="col-lg-9">
                                                    <select name="tipNaknade" id="tipNaknade" data-placeholder="Tip naknade" class="select">
                                                        <option></option>
                                                        <option value="1" @if(isset($stavka) && count($stavka)>0 && $stavka[0]->tipNaknade == 1) selected @endif>Mesečna</option>
                                                        <option value="2" @if(isset($stavka) && count($stavka)>0 && $stavka[0]->tipNaknade == 2) selected @endif>Jednokratna</option>
                                                    </select>
                                                    <label id="tipNaknadeError" for="tipNaknade" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('tipNaknade'))
                                                        <label id="tipNaknadeError2" for="tipNaknade" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="tipNaknade">Zavisi od vrste senzora:</label>
                                                <div class="col-lg-9">
                                                    <input type="checkbox" name="zavisnost" id="zavisnost" @if(isset($stavka) && count($stavka)>0 && $stavka[0]->zavisiOdVrsteSenzora == 1) checked @endif />
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" class="btn bg-telekom-slova" onclick="proveriStavkuFakture()">@if(isset($stavka)) Sačuvaj izmene @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 2) active @endif" id="tipUgovora">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Tip ugovora</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tipoviUgovora as $tu)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $tu->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/2/'.$tu->idTipUgovora) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeTipaUgovora({{ $tu->idTipUgovora }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($tipUgovora)) action="{{ route('editTipUgovora') }}" @else action="{{ route('addTipUgovora') }}" @endif method="POST" id="formaTipUgovora">
                            {{ csrf_field() }}
                            @if(isset($tipUgovora) && count($tipUgovora)>0)
                                <input type="hidden" name="idTipUgovora" id="idTipUgovora" value="{{ $tipUgovora[0]->idTipUgovora }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($tipUgovora))
                                                <h5 class="panel-title">Izmeni tip ugovora</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novi tip ugovora</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="nazivTipUgovora">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="nazivTipUgovora" id="nazivTipUgovora" class="form-control" placeholder="Naziv tipa ugovora" @if(isset($tipUgovora) && count($tipUgovora)>0) value="{{ $tipUgovora[0]->naziv }}" @else value="{{ old('nazivTipUgovora') }}" @endif>
                                                    <label id="nazivTipUgovoraError" for="nazivTipUgovora" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('nazivTipUgovora'))
                                                        <label id="nazivTipUgovoraError2" for="nazivTipUgovora" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriTipUgovora()" class="btn bg-telekom-slova">@if(isset($tipUgovora)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 3) active @endif" id="tipServisa">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Tip servisa</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tipoviServisa as $ts)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ts->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/3/'.$ts->idTipServisa) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeTipaServisa({{ $ts->idTipServisa }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($tipServisa)) action="{{ route('editTipServisa') }}" @else action="{{ route('addTipServisa') }}" @endif method="POST" id="formaTipServisa">
                            {{ csrf_field() }}
                            @if(isset($tipServisa) && count($tipServisa)>0)
                                <input type="hidden" name="idTipServisa" id="idTipServisa" value="{{ $tipServisa[0]->idTipServisa }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($tipServisa))
                                                <h5 class="panel-title">Izmeni tip servisa</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novi tip servisa</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="nazivTipServisa">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="nazivTipServisa" id="nazivTipServisa" class="form-control" placeholder="Naziv tipa servisa" @if(isset($tipServisa) && count($tipServisa)>0) value="{{ $tipServisa[0]->naziv }}" @else value="{{ old('nazivTipServisa') }}" @endif>
                                                    <label id="nazivTipServisaError" for="nazivTipServisa" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('nazivTipServisa'))
                                                        <label id="nazivTipServisaError2" for="nazivTipServisa" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriTipServisa()" class="btn bg-telekom-slova">@if(isset($tipServisa)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 4) active @endif" id="tipTehnologije">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Tip tehnologije</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tipoviTehnologija as $tt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $tt->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/4/'.$tt->idTehnologije) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeTipaTehnologije({{ $tt->idTehnologije }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($tipTehnologije)) action="{{ route('editTipTehnologije') }}" @else action="{{ route('addTipTehnologije') }}" @endif method="POST" id="formaTipTehnologije">
                            {{ csrf_field() }}
                            @if(isset($tipTehnologije) && count($tipTehnologije)>0)
                                <input type="hidden" name="idTipTehnologije" id="idTipTehnologije" value="{{ $tipTehnologije[0]->idTehnologije }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($tipTehnologije))
                                                <h5 class="panel-title">Izmeni tip tehnologije</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novi tip tehnologije</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="nazivTipTehnologije">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="nazivTipTehnologije" id="nazivTipTehnologije" class="form-control" placeholder="Naziv tipa tehnologije" @if(isset($tipTehnologije) && count($tipTehnologije)>0) value="{{ $tipTehnologije[0]->naziv }}" @else value="{{ old('nazivTipServisa') }}" @endif>
                                                    <label id="nazivTipTehnologijeError" for="nazivTipTehnologije" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('nazivTipTehnologije'))
                                                        <label id="nazivTipTehnologijeError2" for="nazivTipTehnologije" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriTipTehnologije()" class="btn bg-telekom-slova">@if(isset($tipTehnologije)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 5) active @endif" id="partner">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Partner</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($partneri as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/5/'.$p->idPartner) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjePartnera({{ $p->idPartner }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($partner)) action="{{ route('editPartner') }}" @else action="{{ route('addPartner') }}" @endif method="POST" id="formaPartner">
                            {{ csrf_field() }}
                            @if(isset($partner) && count($partner)>0)
                                <input type="hidden" name="idPartner" id="idPartner" value="{{ $partner[0]->idPartner }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($partner))
                                                <h5 class="panel-title">Izmeni partnera</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novog partnera</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="partner">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="partner" id="partnerInput" class="form-control" placeholder="Naziv partnera" @if(isset($partner) && count($partner)>0) value="{{ $partner[0]->naziv }}" @else value="{{ old('partner') }}" @endif>
                                                    <label id="partnerError" for="partner" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('partner'))
                                                        <label id="partnerError2" for="partner" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriPartnera()" class="btn bg-telekom-slova">@if(isset($partner)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 6) active @endif" id="servisTabPane">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Naziv servisa</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($servisi as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $s->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/6/'.$s->idNazivServisa) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeServisa({{ $s->idNazivServisa }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($servis)) action="{{ route('editServis') }}" @else action="{{ route('addServis') }}" @endif method="POST" id="formaServis">
                            {{ csrf_field() }}
                            @if(isset($servis) && count($servis)>0)
                                <input type="hidden" name="idNazivServisa" id="idNazivServisa" value="{{ $servis[0]->idNazivServisa }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($servis))
                                                <h5 class="panel-title">Izmeni servis</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novi servis</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="partner">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="servis" id="servisInput" class="form-control" placeholder="Naziv servis" @if(isset($servis) && count($servis)>0) value="{{ $servis[0]->naziv }}" @else value="{{ old('servis') }}" @endif>
                                                    <label id="servisError" for="servis" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('partner'))
                                                        <label id="servisError2" for="partner" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriServis()" class="btn bg-telekom-slova">@if(isset($servis)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 7) active @endif" id="senzorTabPane">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Naziv senzora</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($senzori as $se)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $se->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/7/'.$se->idVrstaSenzora) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeSenzora({{ $se->idVrstaSenzora }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($senzor)) action="{{ route('editSenzor') }}" @else action="{{ route('addSenzor') }}" @endif method="POST" id="formaSenzor">
                            {{ csrf_field() }}
                            @if(isset($senzor) && count($senzor)>0)
                                <input type="hidden" name="idTipSenzora" id="idTipSenzora" value="{{ $senzor[0]->idVrstaSenzora }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($senzor))
                                                <h5 class="panel-title">Izmeni senzor</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novi senzor</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="partner">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="senzor" id="senzorInput" class="form-control" placeholder="Naziv senzora" @if(isset($senzor) && count($senzor)>0) value="{{ $senzor[0]->naziv }}" @else value="{{ old('senzor') }}" @endif>
                                                    <label id="senzorError" for="senzor" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('vrstasenzora'))
                                                        <label id="senzorError2" for="partner" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriSenzor()" class="btn bg-telekom-slova">@if(isset($senzor)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane animated zoomIn has-padding  @if($tabela == 8) active @endif" id="lokacijaAplikacijeTabPane">
                        <div class="table-responsive">
                            <table class="table table-bordered table-framed">
                                <thead class="bg-telekom-slova">
                                <tr>
                                    <th>#</th>
                                    <th>Naziv lokacije aplikacije</th>
                                    <th>Akcije</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($lokacije as $lo)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $lo->naziv }}</td>
                                        <td>
                                            <ul class="icons-list text-center">
                                                <li class="text-danger-800"><a href="{{ url('/systemmanaging/8/'.$lo->idLokacijaApp) }}" data-popup="tooltip" title="Izmeni"><i class="icon-pencil7"></i></a></li>
                                                <li> | </li>
                                                <li class="text-danger-800"><a href="#" onclick="brisanjeLokacije({{ $lo->idLokacijaApp }})" data-popup="tooltip" title="Obriši"><i class="icon-trash"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form class="form-horizontal" @if(isset($lokacija)) action="{{ route('editLokApp') }}" @else action="{{ route('addLokApp') }}" @endif method="POST" id="formaLokacija">
                            {{ csrf_field() }}
                            @if(isset($lokacija) && count($lokacija)>0)
                                <input type="hidden" name="idLokacijAplikacije" id="idLokacijAplikacije" value="{{ $lokacija[0]->idLokacijaApp }}"/>
                            @endif
                            <div class="panel panel-flat" style="border: none;">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            @if(isset($lokacija))
                                                <h5 class="panel-title">Izmeni lokaciju</h5>
                                            @else
                                                <h5 class="panel-title">Dodaj novu lokaciju</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label" for="partner">Naziv:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="lokacija" id="lokacijaInput" class="form-control" placeholder="Naziv lokacije aplikacije" @if(isset($lokacija) && count($lokacija)>0) value="{{ $lokacija[0]->naziv }}" @else value="{{ old('lokacija') }}" @endif>
                                                    <label id="lokacijaError" for="lokacijaInput" class="validation-error-label" style="display: none;"></label>
                                                    @if($errors->any() && $errors->has('lokacija'))
                                                        <label id="lokacijaError2" for="lokacijaInput" class="validation-error-label" style="display: block;">Obavezno polje!</label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" onclick="proveriLokaciju()" class="btn bg-telekom-slova">@if(isset($lokacija)) Izmeni @else Dodaj @endif <i class="icon-arrow-right14 position-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>
    @if($errors->any())
        <script>
            new PNotify({
                title: 'Greška!',
                text: 'Ispravite greške za nastavak!',
                addclass: 'bg-telekom-slova',
                hide: false,
                buttons: {
                    sticker: false
                }
            });
        </script>
    @endif
    @empty(!session('greska'))
        <script>
            $( document ).ready(function() {
                new PNotify({
                    title: 'Greška!',
                    text: '{{ session('greska') }}',
                    addclass: 'bg-telekom-slova',
                    hide: false,
                    buttons: {
                        sticker: false
                    }
                });
            });
        </script>
        @php
            Illuminate\Support\Facades\Session::forget('greska');
        @endphp
    @endempty
    @empty(!session('success'))
        <script>
            $( document ).ready(function() {
                new PNotify({
                    title: 'Uspeh!',
                    text: '{{ session('success') }}',
                    addclass: 'bg-success'
                });
            });
        </script>
        @php
            Illuminate\Support\Facades\Session::forget('success');
        @endphp
    @endempty
@endsection
