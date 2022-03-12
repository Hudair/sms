<button type="button" class="btn btn-sm btn-primary mb-75 me-75" data-bs-toggle="modal" data-bs-target="#updateAvatar"><i data-feather="upload"></i> {{__('locale.labels.upload')}}</button>

{{-- Modal --}}
<div class="modal fade text-left" id="updateAvatar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{__('locale.labels.upload_image')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('user.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="col-12">
                        <div class="mb-1">
                            <label for="image" class="form-label">{{__('locale.labels.image')}}</label>
                            <input type="file" name="image" class="form-control" id="image" accept="image/*" />
                            @error('image')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                            <p><small class="text-primary"> {{__('locale.customer.profile_image_size')}} </small></p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{__('locale.labels.upload')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
