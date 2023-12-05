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
			margin-bottom: 0;
		}
		.heading_progress_title {
			/* background-color: #FCD5B4; */
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
			font-size: 12px;
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
		$getOnlyFinalAvgAcademic = Helper::getOnlyFinalAvgAcademic($finalNewData,$avgDiv, $PASDiv);
		
	@endphp
	<!--  -->
	<h6 class="heading_progress_title">Rekap Nilai {{auth()->user()->activeHomeroom()->first()->classroom->classroom_name}} {{Helper::getSchoolYearName()}} - {{Helper::getSchoolTermName()}}</h6>
	<section id="extracurricular" style="">
		<table>
			<thead>
				<tr style="font-size: 10px;">
					@foreach($tableHeader as $header)
						@if($header == 'No')
						<th style="vertical-align: middle;width: 2%">{{$header}}</th>
						@elseif(Str::startsWith($header,'Nama Siswa'))
						<th style="vertical-align: middle;width: 10%">{{$header}}</th>
						@elseif(Str::startsWith($header,'Seni Budaya'))
						<th style="vertical-align: middle;width: 5%">Seni Budaya</th>
						@else
						<th style="vertical-align: middle;width: 5%"> {{$header}}</th>
						@endif
					@endforeach
				</tr>
			</thead>
			<tbody>
				@foreach($finalNewData as $student_name => $subjects )
					@php 
						$avg = [];
					@endphp 
					<tr>
						<td>{{$loop->iteration}}</td>
						<td style="text-align: left;padding-left: 2px">{{$student_name}}</td>
						@foreach($subjects as $subjectKey => $subjectValue )
							@php array_push($avg, Helper::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv)); @endphp
							<td>{{Helper::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv)}}</td>
						@endforeach
						@php
							$countFinalAvgAcademic = round(array_sum($avg) / count($avg),1)/10;
							if(count($getStudentCharacter)){
								$countFinalAvgCharacter = Helper::generateCharacterAvg($getStudentCharacter[$student_name]);
							}else {
								$countFinalAvgCharacter = 'unavailable';
							}
						@endphp
						<td>{{ $countFinalAvgAcademic }}</td>
						<td>{{ $countFinalAvgCharacter }}</td>
						@if(count($getStudentCharacter))
							<td>{{round(($countFinalAvgAcademic*75/100+$countFinalAvgCharacter*25/100*2.5),1)}}</td>
						@else
							<td>Unavailable</td>
						@endif

						<td>
							@php
								if(count($getStudentCharacter)){
									$getRank = Helper::generateRank($getStudentCharacter, $getOnlyFinalAvgAcademic)[$student_name];
								}else {
									$getRank = 'unavailable.';
								}
							@endphp
							
							{{$getRank}}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</section>
	<section id="sign" style="margin-top: 50px" >
		<div id="sign_parent" style="float:left;width:30%;">
			<div class="sign_top" style="margin-bottom:80px">
				<p>Mengetahui</p>
				<p>Kepala Sekolah {{$getSchoolSetting->school_name_prefix}}
				<span class="logoB">B</span>
				<span class="logoA">A</span>
				<span class="logoS">S</span>
				<span class="logoI">I</span>
				<span class="logoC">C</span>
				<span class="font511880">{{$getSchoolSetting->school_name_suffix}}</span></p>
			</div>

			<div class="border_sign">
				<p>{{$getSchoolSetting->school_principal_name}}</p>
				<hr style="margin-top:-10px;display: block">
			</div>
		</div>
		<div id="sign_main_teacher" style="float:right;width:auto;">
			<div class="sign_top" style="margin-bottom:80px">
				<p>Batam, {!!$getSchoolSetting->school_progress_report_date!!}</p>
				<p>Wali Kelas</p>
			</div>

			<div class="border_sign">
				<p>{{auth()->user()->name}}</p>
				<hr style="margin-top:-10px;display: block">
			</div>
		</div>
	</section>
	<div style="clear: both;"></div>
	<!-- <div class="page-break"></div> -->
	
</body>
</html>