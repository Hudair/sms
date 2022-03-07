<button type="button" class="btn btn-primary d-none d-sm-block mr-75" data-toggle="modal" data-target="#updateAvatar"><i class="feather icon-upload"></i> {{__('locale.labels.change')}}</button>

{{-- Modal --}}
<div class="modal fade text-left" id="updateAvatar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{__('locale.labels.upload_image')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.customers.avatar', $customer->uid) }}"  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="image">{{ __('locale.labels.image') }}</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="image" accept="image/*">
                            <label class="custom-file-label" for="image" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>
                            @error('image')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <p><small class="text-primary"> {{__('locale.customer.profile_image_size')}} </small></p>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{__('locale.labels.upload')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
