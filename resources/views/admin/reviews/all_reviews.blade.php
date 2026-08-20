@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Published Reviews</h4>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="table">
                                @if(isset($reviews) && $reviews->isNotEmpty())
                                    <thead>
                                        <tr>
                                            <th scope="col">Author</th>
                                            <th scope="col">Rating</th>
                                            <!-- <th scope="col">Review</th> -->
                                            <th scope="col">Profile Image</th>
                                            <th scope="col">Review Item</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reviews as $data)
                                        <tr>
                                            <td>
                                                <a href="{{ url('admin-dashboard/edit-review/'.$data->id) }}">

                                                @if($data->user_id != null)

                                                    {{-- image Initials --}}
                                                    @php
                                                        $showImage = isset($data->user->file_path) && $data->is_show ;
                                                        $initials = strtoupper(substr($data->user->first_name ?? '', 0, 1) . substr($data->user->last_name ?? '', 0, 1));
                                                    @endphp

                                                    @if ($showImage)
                                                        {{-- User Image  --}}
                                                        <img id="profile-image" 
                                                        src="{{ asset($data->user->file_path) }}"
                                                        alt="User Image" style="width: 20px; height: 20px; object-fit: cover;">
                                                        
                                                    @else
                                                        <div class="initials-avatar">
                                                            {{ $initials }}
                                                        </div>
                                                    @endif


                                                    {{ $data->user->first_name ?? '' }} {{ $data->user->last_name ?? ''}}
                                                @else

                                                {{-- image Initials --}}
                                                    @php
                                                        $showImage = isset($data->user->file_path) && $data->is_show ;
                                                        $initials = strtoupper(substr($data->first_name ?? '', 0, 1) . substr($data->last_name ?? '', 0, 1));
                                                    @endphp

                                                    @if ($showImage)
                                                        {{-- User Image  --}}
                                                        <img id="profile-image" 
                                                        src="{{ asset($data->user->file_path) }}"
                                                        alt="User Image" style="width: 20px; height: 20px; object-fit: cover;">
                                                        
                                                    @else
                                                        <div class="initials-avatar">
                                                            {{ $initials }}
                                                        </div>
                                                    @endif                                                

                                                    {{ $data->first_name ?? '' }} {{ $data->last_name ?? '' }}
                                                @endif

                                                </a>
                                            </td>
                                            <td>
                                            @if(isset($data->rating) && $data->rating != null)
                                                <div id="full-stars-example-two">
                                                    <div class="ratings">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <label for="rating{{ $i }}">
                                                                <i rate="{{ $i }}" class="star fa fa-star {{ $data->rating >= $i ? 'rating-color' : '' }}"></i>
                                                            </label>
                                                            <input name="rating" id="rating{{ $i }}" class="chkbox" style="display:none;" value="{{ $i }}" {{ $data->rating == $i ? 'checked' : '' }}>
                                                        @endfor
                                                    </div>
                                                </div>
                                            @endif
                                            </td>
                                            <!-- <td>{{ $data->description ?? '' }}</td> -->
                                            <td>{{ $data->is_show == 1 ? 'Public' : 'Private' }}</td>
                                            <td>{{ $data->document->title ?? '' }}</td>
                                            <td class="tb-tnx-action text-center">
                                                <div class="dropdown">
                                                    <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                        <ul class="link-list-plain">
                                                            <li><a href="{{ url('admin-dashboard/edit-review/'.$data->id) }}"><i class="icon ni ni-edit"></i>Edit</a></li>
                                                            <li><a onclick="deleteReview({{ $data->id ?? '' }})"><i class="icon ni ni-trash"></i>Delete</a></li>
                                                            <li>
                                                                @if($data->status == '1')
                                                                <a class="publish" data-value="unpublish" data-id="{{ $data->id ?? '' }}"><span>Unpublish</span></a>
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                @else
                                <p>No reviews yet.</p>
                                @endif
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div><!-- .nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.publish').click(function(){
            var data={
                value: $(this).data('value'),
                id: $(this).data('id'),
                _token: "{{ csrf_token() }}"
            }

            $.ajax({
                url: "{{ url('/admin-dashboard/publish-review') }}",
                type: "post",
                data: data,
                dataType: "json",
                success: function(response){
                    if(response.status == 'unpublish' && response.code == 200){
                        NioApp.Toast('Unpublished Review', 'info', {position: 'top-right'});
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                }
            })
        })
    })


    function deleteReview(id){
        var data={
            id: id,
            _token:"{{ csrf_token() }}"
        }
        $.ajax({
            url: "{{ url('admin-dashboard/delete-review') }}",
            type: "post",
            data: data,
            dataType: "json",
            success: function(response){
                if(response.satus == 'success' && response.code == 200){
                    NioApp.Toast('Review Deleted', 'error', {position: 'top-right'});
                    setTimeout(() => {
					    location.reload();
                    }, 1000);

				}
            }
        })
    }
</script>

@endsection