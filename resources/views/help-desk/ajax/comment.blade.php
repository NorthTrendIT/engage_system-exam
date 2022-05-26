<!--begin::Col-->
<div class="col-md-12 mb-5">
	<!--begin::Feature post-->
	<div class="h-100 d-flex flex-column justify-content-between pe-lg-6 mb-lg-0 mb-10" style="border: 1px solid #c7c4c4;padding: 12px;border-radius: 10px;">
		<!--begin::Body-->
		<div class="mb-0">
			<!--begin::Text-->
			<div class="fw-bold fs-5  mt-4">
				{!! $comment->comment !!}
			</div>
			<!--end::Text-->
		</div>
		<!--end::Body-->
		<!--begin::Footer-->
		<div class="d-flex flex-stack flex-wrap">
			<!--begin::Item-->
			<div class="d-flex align-items-center pe-2">
				<!--begin::Avatar-->
				<div class="symbol symbol-35px symbol-circle me-3">
					@if(@$comment->user->profile && get_valid_file_url('sitebucket/users',$comment->user->profile))
	                 	<img src="{{ get_valid_file_url('sitebucket/users',$comment->user->profile) }}" alt="user" />
	                @else
	                 	<img src="{{ asset('assets') }}/assets/media/default_user.png" alt="user" />
	                @endif
				</div>
				<!--end::Avatar-->
				<!--begin::Text-->
				<div class="fs-5 fw-bolder">
					<a href="javascript:" class="text-gray-700 text-hover-primary">{{ @$comment->user->first_name ?? "" }} {{ @$comment->user->last_name ?? "" }}</a>
					<small class="text-muted">on {{ date('M d, Y h:i A',strtotime(@$comment->created_at)) }}</small>
				</div>
				<!--end::Text-->
			</div>
			<!--end::Item-->
		</div>
		<!--end::Footer-->
	</div>
	<!--end::Feature post-->
</div>
<!--end::Col-->