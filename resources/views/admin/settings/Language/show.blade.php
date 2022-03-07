@extends('layouts/contentLayoutMaster')

@section('title', $language->name)

@section('content')

    {{-- Vertical Tabs start --}}
    <section id="vertical-tabs">

        <div class="row match-height">
            <div class="col-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">{{ $language->name }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="menu" data-toggle="tab"
                                           aria-controls="menu" href="#menu" role="tab" aria-selected="true">Menu</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="buttons" data-toggle="tab" aria-controls="buttons" href="#buttons" role="tab" aria-selected="false">Buttons</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="labels" data-toggle="tab" aria-controls="labels" href="#labels" role="tab" aria-selected="false">Labels</a>
                                    </li>
                                </ul>
                                <div class="tab-content">

                                    <div class="tab-pane active" id="menu" role="tabpanel" aria-labelledby="menu">
                                        <form class="form form-vertical" action="{{ route('admin.languages.store') }}" method="post">
                                            @csrf
                                            <div class="form-body">

                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <input type="text" id="sender_id" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <button type="submit" class="btn btn-primary btn-sm mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                                </div>


                                            </div>
                                        </form>
                                    </div>


                                    <div class="tab-pane" id="buttons" role="tabpanel" aria-labelledby="buttons">
                                        <form class="form form-vertical" action="{{ route('admin.languages.store') }}" method="post">
                                            @csrf
                                            <div class="form-body">

                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <input type="text" id="sender_id" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <button type="submit" class="btn btn-primary mr-1 btn-sm mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                                </div>


                                            </div>
                                        </form>
                                    </div>


                                    <div class="tab-pane" id="labels" role="tabpanel" aria-labelledby="labels">
                                        <form class="form form-vertical" action="{{ route('admin.languages.store') }}" method="post">
                                            @csrf
                                            <div class="form-body">

                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <input type="text" id="sender_id" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <button type="submit" class="btn btn-primary btn-sm mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                                </div>


                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Vertical Tabs end --}}
@endsection

