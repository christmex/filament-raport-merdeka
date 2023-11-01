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
		.heading_progress_title {
			text-align: center;
			background-color: #FCD5B4;
			margin-top: 0;
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

		/* Global class */
		table {
			width: 100%;
			/* border: 1px solid black; */
			border-collapse: collapse;
			
		}
		@page { margin: 21px 0 21px ; }
		body { 
			margin: 21px 21px 21px ; 
			-webkit-print-color-adjust:exact !important;
  			print-color-adjust:exact !important;
		}
		/* Global class */
	</style>
</head>
<body>
	<h1 class="heading_progress_title">Laporan Hasil Belajar Siswa</h1>
	<section id="student_details">
		<div style="float: left; width: 70%;">
			<table>
				<tr>
					<td style="width: 100px">School Name</td>
					<td style="width: 5px">:</td>
					<td>{{Helper::getSchoolSetting()->school_name_prefix}}
						<span class="logoB">B</span>
						<span class="logoA">A</span>
						<span class="logoS">S</span>
						<span class="logoI">I</span>
						<span class="logoC">C</span>
						<span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td style="width: 5px">:</td>
					<td>{{Helper::getSchoolSetting()->school_address}}</td>
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
					<td>{{Helper::getSchoolTermName()}}</td>
				</tr>
				<tr>
					<td>Annual Study</td>
					<td>:</td>
					<td>{{Helper::getSchoolYearName()}}</td>
				</tr>
			</table>
		</div>
	</section>
	<div style="clear: both;"></div>
	
	<section id="grade" style="margin-top:20px;">
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 5%">No</th>
					<th style="vertical-align: middle;width: 30%">Mata Pelajaran</th>
					<th style="vertical-align: middle;width: 10%">KKM</th>
					<th style="vertical-align: middle;width: 15%">Nilai Akhir</th>
					<th style="vertical-align: middle;width: 40%">Deskripsi</th>
				</tr>
			</thead>
			<tbody>
				@foreach($newData as $key => $value)
				<!-- <tr draggable="true"> -->
				<tr>
					<td>{{$loop->iteration}}</td>
					<td style="text-align: left; padding: 5px">{{$key}}</td>
					<td>{{$value['KKM']}}</td>
					<td>{{Helper::countFinalGrade($value['AVG'],$value['PAS'],$avgDiv, $PASDiv)}}</td>
					<td style="text-align: left; padding: 5px; word-wrap: break-word;">lorem10asdkjasdkajsndkasndkasjdnjkasndjkasndjkasndjkasndjkasndjkasndjkasnjkdnasjkdnajksdnjkasdnjkasdna<br><br>asdgasjdgashjdgjasdghjasgdjhasgd</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</section>

	<section id="extracurricular" style="margin-top:20px;">
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 5%">No</th>
					<th style="vertical-align: middle;width: auto">Ekstrakurikuler</th>
					<th style="vertical-align: middle;width: auto">Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr draggable="true">
					<td>1</td>
					<td style="text-align: left; padding: 5px">Modern Dance</td>
					<td style="text-align: left; padding: 5px">asss</td>
				</tr>
			</tbody>
		</table>
	</section>

	<section id="absence" style="margin-top:20px;">
		<h3 style="margin: 10px 0 10px">Ketidakhadiran</h3>
		<table>
			<thead>
				<tr>
					<th style="vertical-align: middle;width: 33.33%;">Sakit</th>
					<th style="vertical-align: middle;width: 33.33%;">Ijin</th>
					<th style="vertical-align: middle;width: 33.33%;">Tanpa Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr draggable="true">
					<td>1</td>
					<td>1</td>
					<td>1</td>
				</tr>
			</tbody>
		</table>
	</section>

	<section id="sign" style="margin-top: 50px" >
		<div id="sign_parent" style="float:left;width:30%;">
			<div class="sign_top" style="margin-bottom:80px">
				<p>Mengetahui</p>
				<p>Orang Tua/Wali</p>
			</div>

			<div class="border_sign">
				<p style="visibility:hidden">parentsign</p>
				<hr style="margin-top:-15px;display: block">
			</div>
		</div>
		<div id="sign_main_teacher" style="float:right;width:auto;">
			<div class="sign_top" style="margin-bottom:80px">
				<p>Batam, {!!Helper::getSchoolSetting()->school_progress_report_date!!}</p>
				<p>Wali Kelas</p>
			</div>

			<div class="border_sign">
				<p>{{auth()->user()->name}}</p>
				<hr style="margin-top:-15px;display: block">
			</div>
		</div>
	</section>
	<div style="clear: both;"></div>

	<section id="sign_principle" style="text-align:center; margin: 5% auto 0">
	
		<div class="sign_top">
			<p>Mengetahui</p>
			<p>Kepala Sekolah
				{{Helper::getSchoolSetting()->school_name_prefix}}
				<span class="logoB">B</span>
				<span class="logoA">A</span>
				<span class="logoS">S</span>
				<span class="logoI">I</span>
				<span class="logoC">C</span>
				<span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
			</p>
		</div>

		<div class="border_sign" style="margin-top: 100px">
			<!-- <p style="text-decoration:underline">Rudi wanro situmorang</p> -->
			<p style="text-decoration:underline;text-decoration-thickness: 1px; text-underline-offset: 8px;">{{Helper::getSchoolSetting()->school_principal_name}}</p>
			<!-- <hr> -->
		</div>
	</section>

	<!-- <div class="page-break"></div> -->
	
</body>
</html>