<!--begin::Col-->
<div class="col-md-12 mb-5">
	<!--begin::Feature post-->
	<div class="h-100 d-flex flex-column justify-content-between pe-lg-6 mb-lg-0 mb-10" style="border: 1px solid #c7c4c4;padding: 12px;border-radius: 10px;">
		<!--begin::Body-->
		<div class="mb-0">
			<!--begin::Text-->
			<div class="fw-bold fs-5 text-gray-600 text-dark mt-4">
				<h2>
					{!! @$promotion->title !!}
				</h2>

				<p class="mt-4">Type : <b> {!! @$promotion->promotion_type->title !!}</b> <br></p>

				<p class="mt-4">{!! @$promotion->description !!}</p>

				<span class="text-muted">Duration: {{ date('M d, Y',strtotime(@$promotion->promotion_start_date)) }} to {{ date('M d, Y',strtotime(@$promotion->promotion_end_date)) }}</span>
			</div>
			<!--end::Text-->
		</div>
		<!--end::Body-->
	</div>
	<!--end::Feature post-->
</div>
<!--end::Col-->