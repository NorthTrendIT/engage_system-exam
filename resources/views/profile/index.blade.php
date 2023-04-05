@php
$role = \App\Models\Role::where('id',Auth::user()->role_id)->first();
@endphp
@extends('layouts.master')

@section('title','Profile')

@section('content')

<style type="text/css">
  /*.tab-menu { margin-top:34px; }*/
.tab-menu ul { margin:0; padding:0; list-style:none; display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; }
.tab-menu ul li { -ms-flex-preferred-size: 0; flex-basis: 0; -ms-flex-positive: 1; flex-grow: 1; max-width: 100%; text-align:center; }
.tab-menu ul li a {
 color: #1e1e2d;
    text-transform: uppercase;
    letter-spacing: 0.44px;
    font-weight: bold;
    display: inline-block;
    padding: 18px 26px;
    display: block;
    text-decoration: none;
    /*transition: 0.5s all;*/
    background: #fff;
    /*border: 2px solid #1e1e2d;*/
    /*border-bottom: 0;*/
    position: relative;
    
}
.tab-menu ul li a.active:after, .tab-menu ul li a:hover:after {
    transform: scale(1);
}
.tab-menu ul li a:after{
  position: absolute;
  content: "";
    background:#1e1e2d;
    height: 2px;
    position: absolute;
    width: 100%;
    left: 0px;
    bottom: -1px;
    transition: all 250ms ease 0s;
    transform: scale(0);
}
/*.tab-menu ul li a:hover { background:#d0d062; color:#fff; text-decoration:none; }
.tab-menu ul li a.active { background:#f4fcce; color:#000; text-decoration:none; }*/
.tab-box { display:none; }

.tab-teaser {
      margin: 10px;
    box-shadow: 0px 1px 3px rgb(0 0 0 / 30%);
}
.tab-main-box {     background: #fff;
    padding: 10px 30px;
    border-top: 2px solid #f5f8fa;
    }



</style>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Profile</h1>
      </div>
    </div>
  </div>

  @if(strtolower($role->name) !== 'sales personnel' && Session::has('profile_error_message') && Auth::user()->first_login == 1)
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="alert alert-custom alert-danger" role="alert">
            <div class="alert-text">You have to change your temporary email address to your actual email address in order to access the system.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif


  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-8 col-md-8 col-lg-8 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header pt-5 border-bottom">
              <h1 class="text-dark fw-bolder fs-3 my-1">Update Details</h1>
            </div>
            @if(Auth::user()->role_id == 4)
            <div class="tab-teaser">
                <div class="tab-menu">
                  <ul>
                    <li><a href="#" class="active" data-rel="tab-1">Update Details</a></li>
                    <li><a href="#" data-rel="tab-2" class="">Profile</a></li>
                  </ul>
              </div>

              <div class="tab-main-box">
                  <div class="tab-box" id="tab-1" style="display:block;">
                    <!--  <h2>Tab 1</h2> -->
                     <form method="post" id="myForm">
                        @csrf
                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>First Name<span class="asterisk">*</span></label>
                              <input type="text" class="form-control form-control-solid" placeholder="Enter first name" name="first_name" value="{{ @Auth::user()->first_name ?? "" }}">
                            </div>
                          </div>
                        </div>

                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>Last Name<span class="asterisk">*</span></label>
                              <input type="text" class="form-control form-control-solid" placeholder="Enter last name" name="last_name" value="{{ @Auth::user()->last_name ?? "" }}">
                            </div>
                          </div>
                        </div>

                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>Email<span class="asterisk">*</span></label>
                              <input type="email" class="form-control form-control-solid" placeholder="Enter email" name="email" value="{{ @Auth::user()->email ?? "" }}">
                            </div>
                          </div>
                        </div>

                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>Profile</label>
                              <input type="file" class="form-control form-control-solid" name="profile" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp pdf doc docx xls xlsx ppt pptx odt ods">
                            </div>
                          </div>
                        </div>

                        @if(get_login_user_profile())
                          <div class="row mt-10 mb-10">
                            <div class="col-md-12">
                              <div class="form-group">
                                <a href="{{ get_login_user_profile() }}" class="fancybox"><img src="{{ get_login_user_profile() }}" height="100" width="100"></a>
                              </div>
                            </div>
                          </div>
                        @endif

                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <input type="submit" value="Update" class="btn btn-primary">
                            </div>
                          </div>
                        </div>

                      </form>
                  </div>
                  <div class="tab-box" id="tab-2">
                      <!-- <h2>Update Details</h2> -->

                      <ul class="nav nav-pills mb-3" id="ex1" role="tablist">
                        <li class="nav-item" role="presentation">
                          <a
                            class="nav-link active"
                            id="ex1-tab-1"
                            data-mdb-toggle="pill"
                            href="#ex1-pills-1"
                            role="tab"
                            aria-controls="ex1-pills-1"
                            aria-selected="true"
                            >APBW</a
                          >
                        </li>
                        <li class="nav-item" role="presentation">
                            <a
                              class="nav-link"
                              id="ex1-tab-2"
                              data-mdb-toggle="pill"
                              href="#ex1-pills-2"
                              role="tab"
                              aria-controls="ex1-pills-2"
                              aria-selected="false"
                              >NTMC</a
                            >
                        </li>
                        <li class="nav-item" role="presentation">
                          <a
                            class="nav-link"
                            id="ex1-tab-3"
                            data-mdb-toggle="pill"
                            href="#ex1-pills-3"
                            role="tab"
                            aria-controls="ex1-pills-3"
                            aria-selected="false"
                            >PHILSYN</a
                          >
                        </li>
                        <li class="nav-item" role="presentation">
                          <a
                            class="nav-link"
                            id="ex1-tab-4"
                            data-mdb-toggle="pill"
                            href="#ex1-pills-4"
                            role="tab"
                            aria-controls="ex1-pills-4"
                            aria-selected="false"
                            >PHILCREST</a
                          >
                        </li>
                      </ul>
                      

                      <!-- <div class="tab-menu">
                          <ul>
                            <li><a href="#" class="active" data-rel="tab-11">ABPW</a></li>
                            <li><a href="#" data-rel="tab-12">NTMC</a></li>
                            <li><a href="#" data-rel="tab-13">PHILSYN</a></li>
                            <li><a href="#" data-rel="tab-14" class="">PHILCREST</a></li>
                          </ul>
                      </div> -->
                      <div class="tab-content" id="ex1-content">
                      <div class="tab-pane fade show active" id="ex1-pills-1" role="tabpanel" aria-labelledby="ex1-tab-1">
                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <!--begin::Table container-->
                              <div class="table-responsive">
                                 <!--begin::Table-->
                                 <table class="table table-bordered">
                                    <!--begin::Table head-->
                                    @if($apbw)
                                    <thead>
                                      
                                      <tr>
                                        <th> <b>Card Code:</b> </th>
                                        <td>{{ @$apbw->card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Universal Card Code:</b> </th>
                                        <td>{{ @$apbw->u_card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Card Name:</b> </th>
                                        <td>{{ @$apbw->card_name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Group Name:</b> </th>
                                        <td>{{ @$apbw->group->name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>OMS Email:</b> </th>
                                        <td>{{ @$apbw->user->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Email:</b> </th>
                                        <td>{{ @$apbw->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Contact Person Name:</b> </th>
                                        <td>{{ @$apbw->contact_person ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Class:</b> </th>
                                        <td>{{ @$apbw->u_class ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Address:</b> </th>
                                        <td>{{ @$apbw->address ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Territory:</b> </th>
                                        <td>{{ @$apbw->territories->description ?? "-" }}</td>
                                      </tr>

                                      @if(userrole() == 1)
                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$apbw->credit_limit)  ?? "-" }}</td>
                                      </tr>
                                      @endif

                                      {{-- <tr>
                                        <th> <b>Max Commitment:</b> </th>
                                        <td>{{ @$apbw->max_commitment ?? "-" }}</td>
                                      </tr> --}}

                                      <tr>
                                        <th> <b>Federal Tax ID:</b> </th>
                                        <td>{{ @$apbw->federal_tax_id ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Current Account Balance:</b> </th>
                                        <td>{{ @$apbw->current_account_balance ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Created Date:</b> </th>
                                        <td>{{ date('M d, Y',strtotime(@$apbw->created_at)) }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Status:</b> </th>
                                        <td><b class="{{ @$apbw->is_active ? "text-success" : "text-danger" }}">{{ @$apbw->is_active == true ? "Active" : "Inactive" }}</b></td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Overdue Amount:</b> </th>
                                        <td>{{number_format(@$totalOverdueAmount)}}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Outstanding Amount:</b> </th>
                                        <td>{{ (number_format(@$apbw->current_account_balance)) ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Open Order Amount:</b> </th>
                                        <td>{{ (number_format(@$apbw->open_orders_balance)) ?? "-" }}</td>
                                      </tr>


                                      <tr>
                                        <th> <b>Total Exposure Amount:</b> </th>
                                        <td>{{ number_format(@$apbw->current_account_balance + @$apbw->open_orders_balance)  ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$apbw->credit_limit) ?? "-" }}</td>
                                      </tr>

                                      @if(@$apbw->credit_limit > (@$apbw->current_account_balance + @$apbw->open_orders_balance))
                                      <tr>
                                        <th> <b>Available Credit Limit:</b> </th>
                                        <td>{{number_format(@$apbw->credit_limit - ($apbw->current_account_balance + @$apbw->open_orders_balance))}}</td>
                                      </tr>
                                      @else
                                      <tr>
                                        <th> <b>Over Credit Limit:</b> </th>
                                        <td>
                                          {{number_format((float)(($apbw->current_account_balance + @$apbw->open_orders_balance) - @$apbw->credit_limit))}}</td>
                                      </tr>
                                      @endif
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    @else
                                    <thead>
                                      <tr>
                                        <th> <b>No Data Available</b> </th>
                                      </tr>
                                    </thead>
                                    @endif
                                    <tbody>

                                    </tbody>
                                    <!--end::Table body-->
                                 </table>
                                 <!--end::Table-->
                              </div>
                              <!--end::Table container-->

                            </div>

                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="ex1-pills-2" role="tabpanel" aria-labelledby="ex1-tab-2">
                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <!--begin::Table container-->
                              <div class="table-responsive">
                                 <!--begin::Table-->
                                 <table class="table table-bordered">
                                    <!--begin::Table head-->
                                    @if($ntmc)
                                    <thead>
                                      
                                      <tr>
                                        <th> <b>Card Code:</b> </th>
                                        <td>{{ @$ntmc->card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Universal Card Code:</b> </th>
                                        <td>{{ @$ntmc->u_card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Card Name:</b> </th>
                                        <td>{{ @$ntmc->card_name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Group Name:</b> </th>
                                        <td>{{ @$ntmc->group->name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>OMS Email:</b> </th>
                                        <td>{{ @$ntmc->user->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Email:</b> </th>
                                        <td>{{ @$ntmc->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Contact Person Name:</b> </th>
                                        <td>{{ @$ntmc->contact_person ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Class:</b> </th>
                                        <td>{{ @$ntmc->u_class ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Address:</b> </th>
                                        <td>{{ @$ntmc->address ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Territory:</b> </th>
                                        <td>{{ @$ntmc->territories->description ?? "-" }}</td>
                                      </tr>

                                      @if(userrole() == 1)
                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ @$ntmc->credit_limit ?? "-" }}</td>
                                      </tr>
                                      @endif

                                      {{-- <tr>
                                        <th> <b>Max Commitment:</b> </th>
                                        <td>{{ @$ntmc->max_commitment ?? "-" }}</td>
                                      </tr> --}}

                                      <tr>
                                        <th> <b>Federal Tax ID:</b> </th>
                                        <td>{{ @$ntmc->federal_tax_id ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Current Account Balance:</b> </th>
                                        <td>{{ @$ntmc->current_account_balance ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Created Date:</b> </th>
                                        <td>{{ date('M d, Y',strtotime(@$ntmc->created_at)) }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Status:</b> </th>
                                        <td><b class="{{ @$ntmc->is_active ? "text-success" : "text-danger" }}">{{ @$ntmc->is_active == true ? "Active" : "Inactive" }}</b></td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Overdue Amount:</b> </th>
                                        <td>{{number_format(@$totalOverdueAmount)}}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Outstanding Amount:</b> </th>
                                        <td>{{ (number_format(@$ntmc->current_account_balance)) ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Open Order Amount:</b> </th>
                                        <td>{{ (number_format(@$ntmc->open_orders_balance)) ?? "-" }}</td>
                                      </tr>


                                      <tr>
                                        <th> <b>Total Exposure Amount:</b> </th>
                                        <td>{{ number_format(@$ntmc->current_account_balance + @$ntmc->open_orders_balance)  ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$ntmc->credit_limit) ?? "-" }}</td>
                                      </tr>

                                      @if(@$ntmc->credit_limit > (@$ntmc->current_account_balance + @$ntmc->open_orders_balance))
                                      <tr>
                                        <th> <b>Available Credit Limit:</b> </th>
                                        <td>{{number_format(@$ntmc->credit_limit - ($ntmc->current_account_balance + @$ntmc->open_orders_balance))}}</td>
                                      </tr>
                                      @else
                                      <tr>
                                        <th> <b>Over Credit Limit:</b> </th>
                                        <td>
                                          {{number_format((float)(($ntmc->current_account_balance + @$ntmc->open_orders_balance) - @$ntmc->credit_limit))}}</td>
                                      </tr>
                                      @endif
                                    </thead>
                                    @else
                                    <thead>
                                      <tr>
                                        <th> <b>No Data Available</b> </th>
                                      </tr>
                                    </thead>
                                    @endif
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                    </tbody>
                                    <!--end::Table body-->
                                 </table>
                                 <!--end::Table-->
                              </div>
                              <!--end::Table container-->

                            </div>

                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="ex1-pills-3" role="tabpanel" aria-labelledby="ex1-tab-3">
                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <!--begin::Table container-->
                              <div class="table-responsive">
                                 <!--begin::Table-->
                                 <table class="table table-bordered">
                                    <!--begin::Table head-->
                                    @if($PHILSYN)
                                    <thead>
                                      
                                      <tr>
                                        <th> <b>Card Code:</b> </th>
                                        <td>{{ @$PHILSYN->card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Universal Card Code:</b> </th>
                                        <td>{{ @$PHILSYN->u_card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Card Name:</b> </th>
                                        <td>{{ @$PHILSYN->card_name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Group Name:</b> </th>
                                        <td>{{ @$PHILSYN->group->name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>OMS Email:</b> </th>
                                        <td>{{ @$PHILSYN->user->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Email:</b> </th>
                                        <td>{{ @$PHILSYN->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Contact Person Name:</b> </th>
                                        <td>{{ @$PHILSYN->contact_person ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Class:</b> </th>
                                        <td>{{ @$PHILSYN->u_class ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Address:</b> </th>
                                        <td>{{ @$PHILSYN->address ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Territory:</b> </th>
                                        <td>{{ @$PHILSYN->territories->description ?? "-" }}</td>
                                      </tr>

                                      @if(userrole() == 1)
                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$PHILSYN->credit_limit) ?? "-" }}</td>
                                      </tr>
                                      @endif

                                      {{-- <tr>
                                        <th> <b>Max Commitment:</b> </th>
                                        <td>{{ @$PHILSYN->max_commitment ?? "-" }}</td>
                                      </tr> --}}

                                      <tr>
                                        <th> <b>Federal Tax ID:</b> </th>
                                        <td>{{ @$PHILSYN->federal_tax_id ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Current Account Balance:</b> </th>
                                        <td>{{ @$PHILSYN->current_account_balance ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Created Date:</b> </th>
                                        <td>{{ date('M d, Y',strtotime(@$PHILSYN->created_at)) }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Status:</b> </th>
                                        <td><b class="{{ @$PHILSYN->is_active ? "text-success" : "text-danger" }}">{{ @$PHILSYN->is_active == true ? "Active" : "Inactive" }}</b></td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Overdue Amount:</b> </th>
                                        <td>{{number_format(@$totalOverdueAmount)}}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Outstanding Amount:</b> </th>
                                        <td>{{ (number_format(@$PHILSYN->current_account_balance)) ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Open Order Amount:</b> </th>
                                        <td>{{ (number_format(@$PHILSYN->open_orders_balance)) ?? "-" }}</td>
                                      </tr>


                                      <tr>
                                        <th> <b>Total Exposure Amount:</b> </th>
                                        <td>{{ number_format(@$PHILSYN->current_account_balance + @$PHILSYN->open_orders_balance)  ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$PHILSYN->credit_limit) ?? "-" }}</td>
                                      </tr>

                                      @if(@$PHILSYN->credit_limit > (@$PHILSYN->current_account_balance + @$PHILSYN->open_orders_balance))
                                      <tr>
                                        <th> <b>Available Credit Limit:</b> </th>
                                        <td>{{number_format(@$PHILSYN->credit_limit - ($PHILSYN->current_account_balance + @$PHILSYN->open_orders_balance))}}</td>
                                      </tr>
                                      @else
                                      <tr>
                                        <th> <b>Over Credit Limit:</b> </th>
                                        <td>
                                          {{number_format((float)(($PHILSYN->current_account_balance + @$PHILSYN->open_orders_balance) - @$PHILSYN->credit_limit))}}</td>
                                      </tr>
                                      @endif
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    @else
                                    <thead>
                                      <tr>
                                        <th> <b>No Data Available</b> </th>
                                      </tr>
                                    </thead>
                                    @endif
                                    <tbody>

                                    </tbody>
                                    <!--end::Table body-->
                                 </table>
                                 <!--end::Table-->
                              </div>
                              <!--end::Table container-->

                            </div>

                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="ex1-pills-4" role="tabpanel" aria-labelledby="ex1-tab-4">
                        <div class="row mb-5">
                          <div class="col-md-12">
                            <div class="form-group">
                              <!--begin::Table container-->
                              <div class="table-responsive">
                                 <!--begin::Table-->
                                 <table class="table table-bordered">
                                    <!--begin::Table head-->
                                    @if($PHILCREST)
                                    <thead>
                                      
                                      <tr>
                                        <th> <b>Card Code:</b> </th>
                                        <td>{{ @$PHILCREST->card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Universal Card Code:</b> </th>
                                        <td>{{ @$PHILCREST->u_card_code ?? "-" }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Card Name:</b> </th>
                                        <td>{{ @$PHILCREST->card_name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Group Name:</b> </th>
                                        <td>{{ @$PHILCREST->group->name ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>OMS Email:</b> </th>
                                        <td>{{ @$PHILCREST->user->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Email:</b> </th>
                                        <td>{{ @$PHILCREST->email ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Contact Person Name:</b> </th>
                                        <td>{{ @$PHILCREST->contact_person ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Class:</b> </th>
                                        <td>{{ @$PHILCREST->u_class ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Address:</b> </th>
                                        <td>{{ @$PHILCREST->address ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Territory:</b> </th>
                                        <td>{{ @$PHILCREST->territories->description ?? "-" }}</td>
                                      </tr>

                                      @if(userrole() == 1)
                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$PHILCREST->credit_limit) ?? "-" }}</td>
                                      </tr>
                                      @endif

                                      {{-- <tr>
                                        <th> <b>Max Commitment:</b> </th>
                                        <td>{{ @$PHILCREST->max_commitment ?? "-" }}</td>
                                      </tr> --}}

                                      <tr>
                                        <th> <b>Federal Tax ID:</b> </th>
                                        <td>{{ @$PHILCREST->federal_tax_id ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Current Account Balance:</b> </th>
                                        <td>{{ @$PHILCREST->current_account_balance ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Created Date:</b> </th>
                                        <td>{{ date('M d, Y',strtotime(@$PHILCREST->created_at)) }}</td>
                                      </tr>
                                      <tr>
                                        <th> <b>Status:</b> </th>
                                        <td><b class="{{ @$PHILCREST->is_active ? "text-success" : "text-danger" }}">{{ @$PHILCREST->is_active == true ? "Active" : "Inactive" }}</b></td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Overdue Amount:</b> </th>
                                        <td>{{number_format(@$totalOverdueAmount)}}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Total Outstanding Amount:</b> </th>
                                        <td>{{ (number_format(@$PHILCREST->current_account_balance)) ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Open Order Amount:</b> </th>
                                        <td>{{ (number_format(@$PHILCREST->open_orders_balance)) ?? "-" }}</td>
                                      </tr>


                                      <tr>
                                        <th> <b>Total Exposure Amount:</b> </th>
                                        <td>{{ number_format(@$PHILCREST->current_account_balance + @$PHILCREST->open_orders_balance)  ?? "-" }}</td>
                                      </tr>

                                      <tr>
                                        <th> <b>Credit Limit:</b> </th>
                                        <td>{{ number_format(@$PHILCREST->credit_limit) ?? "-" }}</td>
                                      </tr>

                                      @if(@$PHILCREST->credit_limit > (@$PHILCREST->current_account_balance + @$PHILCREST->open_orders_balance))
                                      <tr>
                                        <th> <b>Available Credit Limit:</b> </th>
                                        <td>{{number_format(@$PHILCREST->credit_limit - ($PHILCREST->current_account_balance + @$PHILCREST->open_orders_balance))}}</td>
                                      </tr>
                                      @else
                                      <tr>
                                        <th> <b>Over Credit Limit:</b> </th>
                                        <td>
                                          {{number_format((float)(($PHILCREST->current_account_balance + @$PHILCREST->open_orders_balance) - @$PHILCREST->credit_limit))}}</td>
                                      </tr>
                                      @endif
                                    </thead>
                                    @else
                                    <thead>
                                      <tr>
                                        <th> <b>No Data Available</b> </th>
                                      </tr>
                                    </thead>
                                    @endif
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                    </tbody>
                                    <!--end::Table body-->
                                 </table>
                                 <!--end::Table-->
                              </div>
                              <!--end::Table container-->

                            </div>

                          </div>
                        </div>
                      </div>
                      </div>
                  </div>                
              </div>
            </div>
            @else
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>First Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter first name" name="first_name" value="{{ @Auth::user()->first_name ?? "" }}">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Last Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter last name" name="last_name" value="{{ @Auth::user()->last_name ?? "" }}">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Email<span class="asterisk">*</span></label>
                      <input type="email" class="form-control form-control-solid" placeholder="Enter email" name="email" value="{{ @Auth::user()->email ?? "" }}">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Profile</label>
                      <input type="file" class="form-control form-control-solid" name="profile" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp pdf doc docx xls xlsx ppt pptx odt ods">
                    </div>
                  </div>
                </div>

                @if(get_login_user_profile())
                  <div class="row mt-10 mb-10">
                    <div class="col-md-12">
                      <div class="form-group">
                        <a href="{{ get_login_user_profile() }}" class="fancybox"><img src="{{ get_login_user_profile() }}" height="100" width="100"></a>
                      </div>
                    </div>
                  </div>
                @endif

                @if(Auth::user()->role_id == 4)
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Overdue Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$totalOverdueAmount) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Outstanding Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->current_account_balance) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Open Order Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->open_orders_balance) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Exposure Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->current_account_balance + @$data->open_orders_balance) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Credit Limit<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->credit_limit) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                @if(@$data->credit_limit > (@$data->current_account_balance + @$data->open_orders_balance))
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Available Credit Limit<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->credit_limit - ($data->current_account_balance + @$data->open_orders_balance)) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>
                @else
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Overdue Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(($data->current_account_balance + @$data->open_orders_balance) - @$data->credit_limit) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>
                @endif
                @endif

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="submit" value="Update" class="btn btn-primary">
                    </div>
                  </div>
                </div>

              </form>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script>
  $(document).ready(function() {

    jQuery.validator.addMethod("noSpace", function(value, element) { 
      value = $.trim(value);
      console.log(value);
      return value != ""; 
    }, "No space please and don't leave it empty");

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();

      var validator = validate_form();
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('profile.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.reload();
              },1500)
            } else {
              toast_error(data.message);
              $('[type="submit"]').prop('disabled', false);
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
            $('[type="submit"]').prop('disabled', false);
          },
        });
      }
    });

    function validate_form(){
      var validator = $("#myForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
              first_name:{
                required: true,
                maxlength: 185,
                noSpace: true,
              },
              last_name:{
                required: true,
                maxlength: 185,
                noSpace: true,
              },
              email:{
                required:true,
                maxlength: 185,
                noSpace: true,
              },
              profile:{
                required: false,
                maxsize: 10000000,
                extension: 'jpeg|jpg|png|eps|bmp|tif|tiff|webp',
              },
          },
          messages: {
              first_name:{
                required: "Please enter first name.",
                maxlength:'Please enter first name less than 185 character',
                noSpace: 'Only Space not allowed.',
              },
              last_name:{
                required: "Please enter last name.",
                maxlength:'Please enter last name less than 185 character',
                noSpace: 'Only Space not allowed.',
              },
              email:{
                required:"Please enter email.",
                maxlength:'Please enter email less than 185 character',
                noSpace: 'Only Space not allowed.',
              },
              profile:{
                extension: "Allow only .jpeg .jpg .png .eps .bmp .tif .tiff .webp files.",
                maxsize: "File size must not exceed 10MB.",
              },              
          },
      });

      return validator;
    }

  });

   $('.tab-menu li a').on('click', function(){
        var target = $(this).attr('data-rel');
        $('.tab-menu li a').removeClass('active');
        $(this).addClass('active');
        $("#"+target).fadeIn('slow').siblings(".tab-box").hide();
        return false;
  });

   $(".nav-link").on('click', function(){
      var target = $(this).attr('aria-controls');
      $(".nav-link").removeClass('active');
      $(this).addClass('active');
      $("#"+target).fadeIn('slow').siblings(".tab-pane").hide();
      $("#"+target).addClass("show");
      $("#"+target).addClass("active");
   });

</script>
@endpush
