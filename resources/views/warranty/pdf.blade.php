<html>
<head>
	<title>Engage Ordering Management System</title>

	<style type="text/css">
		body {
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
		p, th, td {
			font-size: 14px;
			line-height: 19px;
		}
		p a {
			color: #000;
			text-decoration: none;
		}
		
		.main{width: 1100px;margin: 0 auto;display: block;}
		.table {
		    width: 100%;
		    caption-side: bottom;
    		border-collapse: collapse;
		}
		tbody, td, tfoot, th, thead, tr {
		    border-color: inherit;
		    border-style: solid;
		    border-width: 0;
		}
		.table td, .table th, .table tr {
		    border-color: inherit;
		    border-width: inherit;
		    border-style: inherit;
		    text-transform: inherit;
		    font-weight: inherit;
		    font-size: 14px;
		    color: inherit;
		    height: inherit;
		    min-height: inherit;
		    border-bottom: 1px solid #f1f1f1;
		    padding: 15px 10px;
		}
		.table td:first-child, .table th:first-child, .table tr:first-child {
		    padding-left: 0;
		}
		.table tr:last-child td, .table tr:last-child th, .table tr:last-child{
			border-bottom: none;
		}
		table,td{
			mso-table-lspace:0 !important;
			mso-table-rspace:0 !important;
		}
		table{
			border-spacing:0 !important;
			border-collapse:collapse !important;
			table-layout:fixed !important;
		}
		table, tr, td, th, tbody, thead, tfoot {
		    page-break-inside: avoid !important;
		}
		thead {
		    display: table-row-group;
		}
		
		table {
		    word-wrap: break-word;
		}
		table td {
		    word-break: break-all;
		}

		table td ul {
			padding-left: 0;
			border: none;
			margin-bottom: 0;
		}
		@media print {
			body {
		         -webkit-print-color-adjust: exact;
		      }
		  	table {
			      page-break-inside: auto;
			}
			tr {
			    page-break-inside: avoid;
			    page-break-after: auto;
			}
		}
	</style>
</head>
<body style="max-width: 100%; margin: 0 auto;padding:0;">
    <table class="transcript-tbl" width=" 100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%; margin: 0 auto;margin-top: 15px;margin-bottom: 15px;table-layout:fixed;">
		<tr class="box-detail" style="padding-bottom: 50px;">
			<td style="width: 100%;background-color: #7239ea;">
				<h2 style="font-size: 28px;color:white;text-align: center;width: 100%;">Engage Ordering Management System</h2>
			</td>
		</tr>
		<tr class="box-detail" style="">
			<td style="width: 100%;">
				<h2 style="font-size: 23px;margin-bottom: 5px;margin-top: 45px;">Warranty Details</h2>
			</td>
		</tr>
		<tr class="box-detail" style=""> 
			<td>
				<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; border: 1px solid #cfcfcf;width: 100%;table-layout:fixed;">
					<tbody>
      					<tr>
      						<td colspan="2" style="padding: 20px 10px 10px;"><h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;">General Details</h4>
      						</td>
      					</tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Date Time :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;">{{ date('M d, Y',strtotime($data->created_at)) }}</td>
			            </tr>	            
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Type of Warranty Claim :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;">{{ @$data->warranty_claim_type ?? "-" }}</td>
			            </tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: none;"><b>Ref No :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: none;"><strong>{{ @$data->ref_no ?? "-" }}</strong></td>
			            </tr>
			        </tbody>
				</table>
			</td>
		</tr>

		@if(in_array(userrole(),[1]))
		<tr class="box-detail" style=""> 
			<td>
				<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; border: 1px solid #cfcfcf;width: 100%;margin-top: 25px;table-layout:fixed;">
					<tbody>
      					<tr>
      						<td colspan="2" style="padding: 20px 10px 10px;"><h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;">Dealer Details</h4>
      						</td>
      					</tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Business Unit :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;">{{ @$data->user->sap_connection->company_name ?? "-" }}</td>
			            </tr>	            
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Name :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;">{{ @$data->user->sales_specialist_name ?? "-" }}</td>
			            </tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: none;"><b>Email :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: none;">{{ @$data->user->email ?? "-" }}</td>
			            </tr>
			        </tbody>
				</table>
			</td>
		</tr>
		@endif

		<tr class="box-detail" style=""> 
			<td>
				<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; border: 1px solid #cfcfcf;margin-top: 25px;width: 100%;table-layout:fixed;">
					<tbody>
      					<tr>
      						<td colspan="2" style="padding: 20px 10px 10px;">
      							<h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;">Customer Details</h4>
      						</td>
      					</tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Email :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;">{{ @$data->customer_email ?? "-" }}</td>
			            </tr>	            
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Phone :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;">{{ @$data->customer_phone ?? "-" }}</td>
			            </tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Location :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: 1px solid #cfcfcf;">{{ @$data->customer_location ?? "-" }}</td>
			            </tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;"><b>Telephone :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: 1px solid #cfcfcf;">{{ @$data->customer_telephone ?? "-" }}</td>
			            </tr>
			            <tr>
			              <th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: none;"><b>Address :</b></th>
			              <td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: none;">{{ @$data->customer_address ?? "-" }}</td>
			            </tr>
			        </tbody>
				</table>
			</td>
		</tr>   

		<tr class="box-detail" style=""> 
			<td>
				<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; border: 1px solid #cfcfcf;margin-top: 25px;width: 100%;table-layout:fixed;">
					<tbody>
      					<tr>
      						<td colspan="2" style="padding: 20px 10px 10px;"><h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;">Tire & Vehicle Info</h4>
      						</td>
      					</tr>
			            	
						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>Vehicle Maker :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->vehicle_maker ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>Vehicle Model :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->vehicle_model ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>Vehicle Mileage :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->vehicle_mileage ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>Year :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->year ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>License Plate :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->license_plate ?? "-" }}</td>
						</tr>


						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>PC/LT Tire Position :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->lt_tire_position ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>PC/LT Tire Mileage :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->lt_tire_mileage ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>PC/LT Tire Serial No.  :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->lt_tire_serial_no ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>2 Wheels/TB Tire Position :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->tb_tire_position ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>2 Wheels/TB Tire Mileage :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->tb_tire_mileage ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>2 Wheels/TB Tire Serial No. :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->tb_tire_serial_no ?? "-" }}</td>
						</tr>


						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>Reason for tire return :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->reason_for_tire_return ?? "-" }}</td>
						</tr>

						<tr>
							<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;" > <b>Location of damage :</b> </th>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;" >{{ @$data->vehicle->location_of_damage ?? "-" }}</td>
						</tr>


			        </tbody>
				</table>
			</td>
		</tr>
	</table>

	<table class="transcript-tbl" width=" 100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%; margin: 0 auto;margin-top: 15px;margin-bottom: 15px;table-layout:fixed;page-break-before: always;">
		<tr class="box-detail" style=""> 
			<td>
				<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; border: 1px solid #cfcfcf;margin-top: 25px;width: 100%;table-layout:fixed;">
					<tbody>
      					<tr>
      						<td colspan="2" style="padding: 20px 10px 10px;"><h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;">Claim Points</h4>
      						</td>
      					</tr>
				            <tr>
				              	<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;"></th>
				              	<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: none;font-weight: normal; text-align: right;"><b>Answer</b></td>
				            </tr>
			        
			    		@foreach($claim_points as $key => $point)
				            <tr>
				              	<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;" colspan="2"><b>{{ $key + 1}}. {{ $point->title }}</b></th>
				            </tr>

				            @foreach($point->sub_titles as $s_key => $s_point)
				            	<tr>
					              	<td style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;">- {{ $s_point->title }}</td>
					              	<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;font-weight: normal;border-bottom: 1px solid #cfcfcf;text-align: right;">
					              		
					              		@if(isset($warranty_claim_points) && @$warranty_claim_points[$s_point->id] == 1) Yes @endif

                                  		@if(isset($warranty_claim_points) && @$warranty_claim_points[$s_point->id] == 0) No @endif
					              	</td>
				            	</tr>
				            @endforeach            
			            @endforeach            
			        </tbody>
				</table>
			</td>
		</tr>
	</table>

	<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; margin-top: 0px;width: 100%;border: 1px solid #cfcfcf;table-layout:fixed;page-break-before: always;border-collapse: collapse;">
		<tbody >
			<tr>
				<td colspan="5" style="padding: 20px 10px 10px;"><h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;width: 100%;">Tire Manifistation Probable Cause</h4>
				</td>
			</tr>

			<tr>
				<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;width: 50px;">No.</th>
				<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;">Image</th>
				<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;width: 350px;">Manifistation</th>
				<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;width: 350px;">Probable Cause(s)</th>
				<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;width: 80px;">Answer</th>
			</tr>

			@foreach($tire_manifistations as $key => $m)
			<tr>
				<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;font-weight: normal;border: 1px solid #cfcfcf; border-left: none; border-right: none;">{{ $key+1}}.</td>
				<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;font-weight: normal;border: 1px solid #cfcfcf; border-left: none; border-right: none;">
					
					@if($m->image && get_valid_file_url('sitebucket/tire-manifistation',$m->image))
						<img src="{{ get_valid_file_url('sitebucket/tire-manifistation',$m->image) }}" height="100" width="100">
					@endif

				</td>
				<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;font-weight: normal;border: 1px solid #cfcfcf; border-left: none; border-right: none;">
					{!! $m->manifistation !!}
				</td>
				<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;font-weight: normal;border: 1px solid #cfcfcf; border-left: none; border-right: none;">
						{!! $m->probable_cause !!}
				</td>
				<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;font-weight: normal;border: 1px solid #cfcfcf; border-left: none; border-right: none;">
					<p>
						@if(isset($warranty_tire_manifistations) && @$warranty_tire_manifistations[$m->id] == 1) Yes @endif

						@if(isset($warranty_tire_manifistations) && @$warranty_tire_manifistations[$m->id] == 0) No @endif
					</p>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<table class="transcript-tbl" width=" 100%" border="0" cellpadding="0" cellspacing="0">
		<tr class="box-detail" style=""> 
			<td>
				<table style="background: #fdfdfd; padding: 20px 0; border-radius: 7px; border: 1px solid #cfcfcf;margin-top: 0px;width: 100%;">
					<tr style="width: 100%;">
						<td colspan="3" style="padding: 20px 10px 10px;">
							<h4 style="color: #7239ea;font-size: 20px;margin-top: 0;margin-bottom: 0px;padding-left: 0;width: 100%;">Pictures of the Tire focusing on Damage Areas</h4>
						</td>
					</tr>

					<tr>
						<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;">No.</th>
						<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;">Title</th>
						<th style="font-size: 14px;text-align: left;background-color:transparent;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;">Image</th>
					</tr>
					@foreach($data->pictures as $key => $p)
						<tr>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: 1px solid #cfcfcf;">{{ $key+1}}.</td>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: 1px solid #cfcfcf;">{!! $p->title !!}</td>
							<td style="font-size: 14px;text-align: left;padding: 15px 10px;border: none;border-bottom: 1px solid #cfcfcf;font-weight: normal;border-bottom: 1px solid #cfcfcf;">

								@if($p->image && get_valid_file_url('sitebucket/warranty-pictures',$p->image))
									<img src="{{ get_valid_file_url('sitebucket/warranty-pictures',$p->image) }}" width="120">
								@endif
							</td>
						</tr>
					@endforeach
				</table>
			</td>
		</tr> 
	</table>	
</body>
</html>