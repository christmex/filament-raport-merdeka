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
			font-size: 10px;
			width: 100%;
			/* border: 1px solid black; */
			border-collapse: collapse;
			
		}
		@page {
            margin: 21px 0;
            /* size: 215mm 330mm; */
        }
		body { 
			margin: 21px 21px 21px ; 
			-webkit-print-color-adjust:exact !important;
  			print-color-adjust:exact !important;
			font-family: sans-serif;
			/* size: 215mm 330mm; */
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
                /* size: 215mm 330mm; */
            }
		}
		table#details_ {
			border-collapse:separate; 
  			border-spacing: 0 .5em;
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
	</style>
</head>
<body>
	@php 
		$getSchoolSetting = App\Models\SchoolSetting::first();
	@endphp
	<!--  -->
	<div style="text-align: center;position:absolute;top: -10px;">
        <img src="{{asset('logo_basic.jpg')}}" alt="" id="tutwuri_logo" style="width: 80px;height: 80px;object-fit:cover">
    </div>
	<h3 style="font-family:sans-serif;text-align:center"><span class="logoB">B</span>
            <span class="logoA">A</span>
            <span class="logoS">S</span>
            <span class="logoI">I</span>
            <span class="logoC">C</span> CHRISTIAN SCHOOL</h3>
	<h3 style="font-family:sans-serif;text-align:center;text-decoration:underline" class="">CHARACTER REPORT CARD</h3>
	<section id="student_details" style="margin-top: 40px">
		<div style="float: left; width: 33%;">
			<table style="width: 60%;font-size:14px">
				<tr>
					<td>Student Name</td>
					<td style="width: 5px">:</td>
					<td><strong>{{Str::title($student->student_name)}}</strong></td>
				</tr>
			</table>
		</div>
		
		<div style="float: right;width: 33%;text-align: right;position: relative;display: block;">
			<table style="width: 50%;position: absolute;top: 0;right: 0;font-size:14px">
				<tr>
					<td>Semester</td>
					<td>:</td>
					<td>{{Helper::getSchoolTermName()}}</td>
				</tr>
				<tr>
					<td>School Year</td>
					<td>:</td>
					<td>{{Helper::getSchoolYearName()}}</td>
				</tr>
			</table>
		</div>
		<div style="float: right;width: 33%;text-align: center;">
			<table style="width: 50%;margin-left: 33%;font-size:14px">
				<tr>
					<td>Class</td>
					<td>:</td>
					<td>{{Helper::numberToRomawi($student->active_classroom_level)}} {{$student->active_classroom_name}}</td>
				</tr>
			</table>
		</div>
	</section>
	<div style="clear: both;"></div>
	<section id="extracurricular" style="margin-top:20px;">
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 7%" rowspan="3">Aspect</th>
					<th style="vertical-align: middle;width: 13%" rowspan="3">Habits</th>
					<th style="vertical-align: middle;width: 70%" colspan="34">Scoring</th>
					<th style="vertical-align: middle;width: 5%" rowspan="2" colspan="2">Total Score</th>
					<th style="vertical-align: middle;width: 5%" rowspan="3">Average</th>
				</tr>
				<tr>
					<td colspan="2">1st Week</td>
					<td colspan="2">2nd Week</td>
					<td colspan="2">3rd Week</td>
					<td colspan="2">4th Week</td>
					<td colspan="2">5th Week</td>
					<td colspan="2">6th Week</td>
					<td colspan="2">7th Week</td>
					<td colspan="2">8th Week</td>
					<td colspan="2">9th Week</td>
					<td colspan="2">10th Week</td>
					<td colspan="2">11th Week</td>
					<td colspan="2">12th Week</td>
					<td colspan="2">13th Week</td>
					<td colspan="2">14th Week</td>
					<td colspan="2">15th Week</td>
					<td colspan="2">16th Week</td>
					<td colspan="2">17th Week</td>
				</tr>
				<tr>
					@for($i = 1; $i <= 17;$i++)
					<td>H</td>
					<td>S</td>
					@endfor
					<td>H</td>
					<td>S</td>
				</tr>
			</thead>
			<tbody>
				@php 
					$avgTotal = [];
					$habitsTotal = 0;
				@endphp
				@foreach($data as $aspectName => $AspectValue)
					<!-- <tr>
						sebelumnya disini ada aspectname td rowspan +2 bukan +1, tpi kita pindahkan ke bawah sepertinya karna sblumny ada kesalahn kita pindahkan supaya fix kesalahan tersebut
					</tr> -->
					@php 
						$countAspectName = 0;
					@endphp
					@foreach($AspectValue as $habitKey => $habitValue)
						<tr>
							@if($countAspectName == 0)
								<td rowspan="{{count($AspectValue)+1}}">{{$aspectName}}</td>
								@php 
									$countAspectName++;
								@endphp
							@endif
							<td style="text-align:left;padding-left: 5px">{{$habitKey}}</td>
							@php 
								$avgHome = [];
								$avgSchool = [];
								$countAvgHome = 0;
								$countAvgSchool = 0;
								$countAllAvg = 0;
								$habitsTotal++;
							@endphp
							@foreach($habitValue as $weekVal)
								@foreach($weekVal as $homeOrSchoolKey => $homeOrSchool)
									@if($homeOrSchoolKey == 'home')
										@php array_push($avgHome, $homeOrSchool); @endphp
									@endif
									@if($homeOrSchoolKey == 'school')
										@php array_push($avgSchool, $homeOrSchool); @endphp
									@endif
									<td>{{$homeOrSchool}}</td>
								@endforeach
								@if($loop->last)
									@php 
										$countHomeArray = array_filter($avgHome, function ($value) {
											return $value !== null;
										});
										$countSchoolArray = array_filter($avgSchool, function ($value) {
											return $value !== null;
										});
										if(count($countHomeArray) && count($countSchoolArray)){
											$countAvgHome = (array_sum($countHomeArray)/count($countHomeArray))/4;
											$countAvgSchool = (array_sum($countSchoolArray)/count($countSchoolArray));
	
											$countAllAvg = ($countAvgHome*20/100)+($countAvgSchool*80/100);
											array_push($avgTotal, $countAllAvg);
										}

									@endphp 
									<td>{{ Helper::customRound($countAvgHome, 1) }}</td>
									<td>{{ Helper::customRound($countAvgSchool, 1) }}</td>
									<td>{{ Helper::customRound($countAllAvg,1)}} </td>
								@endif
							@endforeach
						</tr>
						@if($loop->last)
						<tr>
							@for($i = 1; $i <= 38;$i++)
							<td><span style="visibility:hidden">space</span></td> <!-- use for klau mau ada garins -->
							@endfor
						</tr>
						@endif
					@endforeach

				@endforeach
			</tbody>
		</table>
		<div style="margin-top: 10px"></div>
		<div style="width:30%;float: left;border: 1px solid black;margin-top: 10px;margin-right: 10px;padding: 10px">
			
			@php 
				//$value = 3.6; //test purposes

				//$value = round(round(array_sum($avgTotal),1) / $habitsTotal,1); 
				$value = array_sum($avgTotal) / $habitsTotal; 
				$getData = App\Models\RangeCharacterDescription::where('start', '<=', $value)->where('end', '>=', $value)->first();
				
				if($getData){
					$getDesc = App\Models\CharacterDescription::where('range_character_description_id',$getData->id)->inRandomOrder()->first()->description;
				}

				if($student->sex == 1){
					$sex = 'His';
				}elseif($student->sex == 0){
					$sex = 'Her';
				}else {
					$sex = 'No Gender';
				}
			@endphp

			@if($getData)
				{{Str::replace('[SEX]',$sex,Str::replace('[STUDENT_NAME]',Str::title($student->student_name),$getDesc))}}.	
			@else 
				Out of range no description
			@endif
		</div>
		<div style="width:45%;float: left">
			<span style="margin-top: 5px;display: block;font-size: 12px">
				@if(!empty($print_progress_report_date))
					Batam, {!!$print_progress_report_date!!}
				@else
					Batam, {!!$getSchoolSetting->school_progress_report_date!!}
				@endif
			</span>
			<table style="margin-top: 5px">
				<tbody>
					<tr>
						<td style="border: none;padding-bottom: 70px;padding-top: 5px">Principal</td>
						<td style="border: none;padding-bottom: 70px;padding-top: 5px">Main Teacher</td>
						<td style="border: none;padding-bottom: 70px;padding-top: 5px">Parent/Guardian</td>
					</tr>
					<tr>
						<td style="font-weight: bold;border: none;padding-bottom: 5px">{{$getSchoolSetting->school_principal_name}}</td>
						<td style="font-weight: bold;border: none;padding-bottom: 5px">{{auth()->user()->name}}</td>
						<td style="font-weight: bold;border: none;padding-bottom: 5px"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<table style="width:20%;float: right">
			<tbody>
				<tr>
					<td>Character Average Score</td>
						@php 
							$characterAvgScore = array_sum($avgTotal) / $habitsTotal;
						@endphp 
					<td style="vertical-align: middle;width: 28%">{{Helper::customRound($characterAvgScore,2)}}</td>
				</tr>
				<tr>
					<td>Academic Average Score</td>
					<td style="vertical-align: middle;width: 28%">{{Helper::customRound($avgAcademic,1)/10}}</td>
				</tr>
				<tr>
					<td>Final Scored:</td>
					<td style="vertical-align: middle;width: 28%">{{Helper::customRound(($characterAvgScore * 25/100 * 2.5)+(($avgAcademic/10)*75/100),2)}}</td>
				</tr>
			</tbody>
		</table>
		<div style="clear: both;"></div>
	</section>
	<!-- <div class="page-break"></div> -->
	
</body>
</html>