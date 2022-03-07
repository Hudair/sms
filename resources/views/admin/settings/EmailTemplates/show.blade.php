@extends('layouts/contentLayoutMaster')

@section('title', $template->name)

@section('content')
    <section class="snow-editor">
        <div class="row">
            <div class="col-md-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{$template->name}}</h4>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('admin.email-templates.update', $template->uid) }}" method="post">
                                @method('PUT')
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="subject" class="required">{{ __('locale.labels.subject') }}</label>
                                            <input type="text" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ $template->subject  }}" name="subject" required>
                                            @error('subject')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="message" class="required">{{ __('locale.labels.message') }}</label>
                                            @include('plugins.editor', ['content' => $template->content])
                                            <textarea name="content" style="display:none" id="hiddenArea"></textarea>
                                        </div>
                                    </div>


                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Available Tags</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p>{{__('locale.description.available_tags')}}</p>

                            @foreach($template->template_tags($template->slug) as $tag => $required)
                                <p><code>{ {{$tag}} }</code> = <span class="text-success">{{__('locale.labels.'.$required)}}</span></p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Snow Editor end -->
@endsection

