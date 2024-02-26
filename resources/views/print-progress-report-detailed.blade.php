<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Print</title>
	<style>
		.page-break {
			page-break-after: always;
		}
		.heading_progress_title,
		.heading_progress_title_cover {
			text-align: center;
			margin-top: 0;
		}
		.heading_progress_title {
			background-color: #FCD5B4;
		}


		/* BASIC TEXT */
		.logoB {
			color: red;
			font-weight: 700;
			margin-left: 0px!important;
		}
		.logoA {
			color: #00B050;
			font-weight: 700;
		}
		.logoS {
			color: #0070C0;
			font-weight: 700;
		}
		.logoI {
			color: yellow;
			font-weight: 700;
		}
		.logoC{
			color: #7030A0;
			font-weight: 700;
		}

		.logoB,
		.logoA,
		.logoS,
		.logoI,
		.logoC {
			display: inline-block;
			margin-left: -4px;
			line-height: 0;
		}

		/* BASIC TEXT */

		/* STUDENT DETAILS */
		table tr td {
			padding: 5px
		}
		/* STUDENT DETAILS */

		/* GRADE  SECTION */
		#grade table td,
		#grade table th,
		#extracurricular table td,
		#extracurricular table th,
		#absence table td,
		#absence table th
		{
			border: 1px solid black;
			text-align: center;
			padding: 0;
			color: black;
			span-size: 10.0pt;
			span-weight: 400;
			span-style: normal;
			text-decoration: none;
		}

		#grade table,
		#extracurricular table,
		#absence table
		{
			border: 1px solid black;
			margin-top: 10px;
			table-layout: fixed;
		}
		/* GRADESECTION */

		
        /* TUTWURI LOGO */
        #tutwuri_logo, 
        #student_name {
            margin: 0 auto;
            display: block;
            text-align: center;
        }
        /* TUTWURI LOGO */

		/* Global class */
		table {
			width: 100%;
			/* border: 1px solid black; */
			border-collapse: collapse;
			
		}
		@page {
            margin: 21px 0;
            size: 215mm 330mm;
        }
		body { 
			margin: 21px 21px 21px ; 
			-webkit-print-color-adjust:exact !important;
  			print-color-adjust:exact !important;
			size: 215mm 330mm;
		}
		img {
            width: 200px;
            height: 200px;
            margin: 50px auto!important;
        }
        p {
            margin: 10px auto!important;
        }
		@media print {
			#fase {
				border: none
			}
            footer {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                padding: 10px;
            }
            body {
                size: 215mm 330mm;
            }
		}
		table#details_ {
			border-collapse:separate; 
  			border-spacing: 0 .5em;
		}

		table#details_peserta_didik tr td {
			padding: 2px 5px
		}

		#fase {
			border: none
		}
		footer {
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			text-align: center;
			padding: 10px;
		}

		/* Global class */
		#grade table td, #grade table th {
			border: 1px solid black;
			text-align: center;
			padding: 0;
			color: black;
			span-size: 10.0pt;
			span-weight: 400;
			span-style: normal;
			text-decoration: none;
		}

		table .rotate-td {
			/* height: 100pt; */
			position: relative;
			vertical-align: bottom;
			border: 1px solid #333;
		}

		.bc-head-txt-label {
			/* left: calc(50% - 0.5rem); */
			left: 30px;
			line-height: 1;
			position: relative;
			-webkit-transform: rotate(180deg);
			transform: rotate(180deg);
			white-space: nowrap;

			/* padding-top: 0.5rem; */
			/* padding: 10px 0; */
			/* -webkit-writing-mode: vertical-rl;
			writing-mode: vertical-rl; */

			transform: rotate(-90deg);
			transform-origin: bottom left;
			white-space: nowrap;
			display: inline-block;
			bottom: 10px
		}
	</style>
</head>
<body>
	@php 
		$getSchoolSetting = App\Models\SchoolSetting::first();
	@endphp

	<h1 class="heading_progress_title">Student's Progress Report</h1>
	<section id="student_details">
		<div style="float: left; width: 70%;">
			<table>
				<tr>
					<td style="width: 100px">School Name</td>
					<td style="width: 5px">:</td>
					<td>{{$getSchoolSetting->school_name_prefix}}
						<span class="logoB">B</span>
						<span class="logoA">A</span>
						<span class="logoS">S</span>
						<span class="logoI">I</span>
						<span class="logoC">C</span>
						<span class="font511880">{{$getSchoolSetting->school_name_suffix}}</span>
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td style="width: 5px">:</td>
					<td>{{$getSchoolSetting->school_address}}</td>
				</tr>
				<tr>
					<td>Student Name</td>
					<td style="width: 5px">:</td>
					<td><strong>{{Str::title($student->student_name)}}</strong></td>
				</tr>
				<tr>
					<td>NIS/NISN</td>
					<td style="width: 5px">:</td>
					<td>{{$student->student_nis}}/{{$student->student_nisn}}</td>
				</tr>
			</table>
		</div>
		<div style="float: right; width: 30%;">
			<table>
				<tr>
					<td>Class</td>
					<td>:</td>
					<td>{{Helper::numberToRomawi($student->active_classroom_level)}} {{$student->active_classroom_name}}</td>
				</tr>
				<tr>
					<td>Semester</td>
					<td>:</td>
					<td>{{Helper::getSchoolTermName(request('school_term_id'))}}</td>
				</tr>
				<tr>
					<td>Annual Study</td>
					<td>:</td>
					<td>{{Helper::getSchoolYearName(request('school_year_id'))}}</td>
				</tr>
			</table>
		</div>
	</section>
	<div style="clear: both;"></div>

	<section id="grade">
		<h3 style="margin: 10px 0 10px">A. Penilaian Sumatif</h3>
		<table>
			{!! $thead !!}
			<tbody>
				@foreach($dataPublic as $key => $value)
					<tr>
						<td>{{$loop->iteration}}</td>
						<td style="text-align: left; padding: 5px">{{$key}}</td>
						@php 
							$topicAvg = [];
							$finalAvg = [];
						@endphp
						@foreach($topicSettings as $topicKey => $topicValue)
							@php 
								$sumatifAvg = [];
								$topicKey = $topicValue->topic_setting_name;
							@endphp
							@foreach($assessmentMethodSetting as $assessmentMethodValue)
									@if(!empty($dataPublic[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]))
										@php 
											$countSumatifAvg = array_sum($dataPublic[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]) / count($dataPublic[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]);
											array_push($sumatifAvg, $countSumatifAvg) 
										@endphp
										<td>{{Helper::customRound($countSumatifAvg)}}</td>
									@else
										<td></td>
									@endif
							@endforeach
							@if(count($sumatifAvg))
								<td>
									@php
										$countSumatifAvg = array_sum($sumatifAvg)/count($sumatifAvg);
										array_push($topicAvg, $countSumatifAvg);
									@endphp
									{{ Helper::customRound($countSumatifAvg) }}
								</td>
							@else 
								<td></td>
							@endif
						@endforeach
					</tr>
				@endforeach
			</tbody>
		</table>
	</section>
	@if(count($dataBasicCur))
	<section id="grade">
		<h3 style="margin: 20px 0 10px">B.
            <span class="logoB">B</span>
            <span class="logoA">A</span>
            <span class="logoS">S</span>
            <span class="logoI">I</span>
            <span class="logoC">C</span>
        Elementary Curriculum</h3>
		<table>
			{!! $thead !!}
			<tbody>
				@foreach($dataBasicCur as $key => $value)
					<tr>
						<td>{{$loop->iteration}}</td>
						<td style="text-align: left; padding: 5px">{{$key}}</td>
							@php 
								$topicAvg = [];
								$finalAvg = [];
							@endphp
							@foreach($topicSettings as $topicKey => $topicValue)
								@php 
									$sumatifAvg = [];
									$topicKey = $topicValue->topic_setting_name;
								@endphp
								@foreach($assessmentMethodSetting as $assessmentMethodValue)
										@if(!empty($dataBasicCur[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]))
											@php 
												$countSumatifAvg = array_sum($dataBasicCur[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]) / count($dataBasicCur[$key][$topicKey][$assessmentMethodValue->assessment_method_setting_name]);
												array_push($sumatifAvg, $countSumatifAvg) 
											@endphp
											<td>{{Helper::customRound($countSumatifAvg)}}</td>
										@else
											<td></td>
										@endif
								@endforeach
								@if(count($sumatifAvg))
									<td>
										@php
											$countSumatifAvg = array_sum($sumatifAvg)/count($sumatifAvg);
											array_push($topicAvg, $countSumatifAvg);
										@endphp
										{{ Helper::customRound($countSumatifAvg) }}
									</td>
								@else 
									<td></td>
								@endif
							@endforeach
					</tr>
				@endforeach
			</tbody>
		</table>
	</section>
	@endif

	<section id="sign" style="margin-top: @if(!empty($getSchoolSetting->meta['margin_top_sign_parent'])) {{$getSchoolSetting->meta['margin_top_sign_parent']}} @else 50px @endif">
		<div id="sign_parent" style="float:left;width:30%;">
			<div class="sign_top" style="margin-bottom: @if(!empty($getSchoolSetting->meta['margin_bottom_sign_parent'])) {{$getSchoolSetting->meta['margin_bottom_sign_parent']}} @else 60px @endif">
				<p>Mengetahui</p>
				<p>Orang Tua/Wali</p>
			</div>

			<div class="border_sign">
				<p style="visibility:hidden;text-decoration:underline;text-decoration-thickness: 1px;text-decoration-color:black; text-underline-offset: 8px;">parentsignasasasasa</p>
				<hr style='margin-top:-10px;display: block;margin-left: -5px;margin-right:-5px;height:1px;border-width:0;background-color:black'>
			</div>
		</div>
		<div id="sign_main_teacher" style="float:right;width:auto;">
			<div class="sign_top" style="margin-bottom: @if(!empty($getSchoolSetting->meta['margin_bottom_sign_homeroom_teacher'])) {{$getSchoolSetting->meta['margin_bottom_sign_homeroom_teacher']}} @else 60px @endif">
				@if(!empty($data['print_progress_report_date']))
					<p>Batam, {!!$data['print_progress_report_date']!!}</p>
				@else
					<p>Batam, {!!$getSchoolSetting->school_progress_report_date!!}</p>
				@endif
				<p>Wali Kelas {{Helper::numberToRomawi($student->active_classroom_level)}} {{$student->active_classroom_name}}</p>
			</div>

			<div class="border_sign">
				<p style="text-decoration:underline;text-decoration-thickness: 1px;text-decoration-color:black; text-underline-offset: 8px;">{{auth()->user()->name}}</p>
				<!-- <hr style="margin-top:-10px;display: block"> -->
			</div>
		</div>
	</section>
	<div style="clear: both;"></div>
	<section id="sign_principle" style="text-align:center; margin: 5% auto 0">
	
		<div class="sign_top">
			<p>Mengetahui</p>
			<p>Kepala 
				{{$getSchoolSetting->school_name_prefix}}
				<span class="logoB">B</span>
				<span class="logoA">A</span>
				<span class="logoS">S</span>
				<span class="logoI">I</span>
				<span class="logoC">C</span>
				<span class="font511880">{{$getSchoolSetting->school_name_suffix}}</span>
			</p>
		</div>

		<div class="border_sign" style="margin-top: @if(!empty($getSchoolSetting->meta['margin_top_sign_principal'])) {{$getSchoolSetting->meta['margin_top_sign_principal']}} @else 100px @endif">
			<!-- <p style="text-decoration:underline">Rudi wanro situmorang</p> -->
			<p style="text-decoration:underline;text-decoration-thickness: 1px; text-underline-offset: 8px;">{{$getSchoolSetting->school_principal_name}}</p>
			<!-- <hr> -->
		</div>
	</section>
</body>
</html>