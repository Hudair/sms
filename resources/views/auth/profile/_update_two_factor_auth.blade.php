@extends('layouts.contentLayoutMaster')

@section('title', 'Two Factor Authentication')

@section('content')
    <!-- users edit start -->
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form class="form form-vertical" action="{{ route('user.account.twofactor.auth', ['status' => $status]) }}" method="post">
                        @csrf
                        <div class="row">

                            <div class="col-12 col-sm-6">

                                <div class="form-group">
                                    <label for="two_factor_code" class="required">Two Factor Code</label>
                                    <input type="password" id="two_factor_code" class="form-control @error('two_factor_code') is-invalid @enderror"
                                           value="{{ old('two_factor_code') }}" name="two_factor_code">
                                    <p class="small text-primary">Please insert 6 digit two-factor code which send your email</p>
                                    @if($errors->has('two_factor_code'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('two_factor_code') }}
                                        </div>
                                    @endif
                                </div>

                            </div>


                            <div class="col-12 d-flex flex-sm-row flex-column mt-1">
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><i class="feather icon-save"></i> {{__('locale.buttons.save_changes')}}</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- users edit ends -->
@endsection


@section('page-script')
    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

        });
    </script>

@endsection


