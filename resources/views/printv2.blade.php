<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Print</title>
	<style>
		body{
			width: 21cm;
			height: 29.7cm;
			display: block;
			margin: 0 auto;
			-webkit-print-color-adjust:exact !important;
  			print-color-adjust:exact !important;
		}
		p {
			margin: 0
		}
		#sign {
			margin-top: 50px
		}
		.border_sign {
			/* margin-top: 100px; */
		}
		.border_sign > p{
			font-weight: bold;
		}
		.sign_top {

		}
		#sign_parent,
		#sign_main_teacher {
			display: flex;
			justify-content: space-around;
			height: 140px;
			/* height: 50vh; */
			/* overflow: hidden; */
			width: 30%;
			flex-direction: column;
    		justify-content: space-between;
		}
		.vision_tagline {
			padding-top: 1px;
			padding-right: 1px;
			padding-left: 1px;
			mso-ignore: padding;
			color: black;
			font-size: 16.0pt;
			font-weight: 400;
			font-style: normal;
			text-decoration: none;
			font-family: "Cooper Black", serif;
			mso-font-charset: 0;
			mso-number-format: General;
			text-align: center;
			vertical-align: middle;
			mso-background-source: auto;
			mso-pattern: auto;
			mso-protection: unlocked hidden;
			white-space: nowrap;

			/* position: absolute; */
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			margin: 0 auto;
			visibility: hidden;
			/* height: 100vh; */
		}
		@media print {
			.vision_tagline {visibility: visible;}
		}
		.flex {
			display: flex;
			/* background-color: red; */
			align-content: flex-start;
			align-items: flex-start;
			justify-content: space-between;
		}
		table {
			width: 100%;
			/* border: 1px solid black; */
			border-collapse: collapse;
		}

		td,
		th{
			/* border: 1px solid black; */
			/* text-align: center; */
			padding: 0;
			vertical-align: middle;
		}

		#basic_cur table td,
        #grade table td,
		#basic_cur table th ,
        #grade table th {
			border: 1px solid black;
			text-align: center;
			padding: 0;
			color: black;
			span-size: 10.0pt;
			span-weight: 400;
			span-style: normal;
			text-decoration: none;
			/* span-family: "Courier New" */
		}

		#basic_cur table ,
        #grade table {
			border: 1px solid black;
		}

		table span.rotate {
			/* transform: rotate(-90deg);
			display: block;
			width: 80pt;
			background: yellow; */

			/* span-size: 1rem; */
			text-transform: uppercase;
			letter-spacing: 3px;
			
			position: absolute;
			bottom: 0;
			left: 0;
			margin-left: -30px;
			
			/* transform : rotate(270deg);
			transform-origin: (0 0); */
			/* width: 100%; */
			top: 0;

			display: block;
			width: 100px;
			/* background: red; */
			
			
			
			transform-origin: left;
			transform: translate(9px, 8px) rotate(-90deg);
		}
		table .rotate-td {
			/* height: 100pt; */
			position: relative;
			vertical-align: bottom;
			border: 1px solid #333
		}

		.bc-head-txt-label{
			left: calc(50% - 0.5rem);
			line-height: 1;
			/* padding-top: 0.5rem; */
			padding: 10px 0;
			/* position: relative;
			-webkit-transform: rotate(180deg);
			transform: rotate(180deg);
			white-space: nowrap;
			-webkit-writing-mode: vertical-rl;
			writing-mode: vertical-rl; */
		}

		.heading_progress_title {
			text-align: center;
			background-color: #FCD5B4;
		}
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
		}

        @media print 
        {
            body {
                padding-top: 0mm;
            }
        }

	</style>
</head>
<body>
	<h1 class="heading_progress_title">Progress Report Peserta Didik</h1>
	<section id="student_details">
		<div class="flex">
			<table style="
				flex: 0 0 70%;
				border-collapse:separate; 
  				border-spacing: 0 .5em;
			">
				<tr>
					<td>School Name</td>
					<td>:</td>
					<td class="xl9511880">{{Helper::getSchoolSetting()->school_name_prefix}}
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
					<td>:</td>
					<td>{{Helper::getSchoolSetting()->school_address}}</td>
				</tr>
				<tr>
					<td>Student Name</td>
					<td>:</td>
					<td><strong>{{$student->student_name}}</strong></td>
				</tr>
				<tr>
					<td>NIS/NISN</td>
					<td>:</td>
					<td>{{$student->student_nis}}/{{$student->student_nisn}}</td>
				</tr>
			</table>
			<table style="
				border-collapse:separate; 
  				border-spacing: 0 .5em;
			">
				<tr>
					<td>Class</td>
					<td>:</td>
					<td>{{$student->active_classroom_name}}</td>
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

	<section id="grade">
		<h3 style="margin: 10px 0 10px">A. Penilaian Sumatif</h3>
		<table>
			<thead>
				<tr>
					<th rowspan="2" style="vertical-align: middle;">No</th>
					<th rowspan="2" style="vertical-align: middle;">Muatan Pelajaran</th>
					@foreach($topicSettings as $value)
					    <td colspan="">{{$value->topic_setting_name}}</td>
                    @endforeach
				</tr>
				<tr>
					<th class="rotate-td bc-browser bc-browser-chrome"><div class="bc-head-txt-label bc-head-icon-chrome">Rata-rata</div></th>
					<th class="rotate-td bc-browser bc-browser-chrome"><div class="bc-head-txt-label bc-head-icon-chrome">Rata-rata</div></th>
					<th class="rotate-td bc-browser bc-browser-chrome"><div class="bc-head-txt-label bc-head-icon-chrome">Rata-rata</div></th>
					
				</tr>
				@foreach($dataPublicCur as $value)
					<tr draggable="true">
						<td>{{$loop->iteration}}</td>
						<td style="text-align: left; padding: 5px">{{$value['subject_name']}}</td>
						<!-- <td>{{ $value['topic_1_avg'] != '' ? ($value['topic_1_avg'] - floor($value['topic_1_avg']) > 0 ? number_format($value['topic_1_avg'], 2) : number_format($value['topic_1_avg'], 0)) : '' }}</td>
						<td>{{ $value['topic_2_avg'] != '' ? ($value['topic_2_avg'] - floor($value['topic_2_avg']) > 0 ? number_format($value['topic_2_avg'], 2) : number_format($value['topic_2_avg'], 0)) : '' }}</td>
						<td>{{ $value['topic_3_avg'] != '' ? ($value['topic_3_avg'] - floor($value['topic_3_avg']) > 0 ? number_format($value['topic_3_avg'], 2) : number_format($value['topic_3_avg'], 0)) : '' }}</td> -->

						<td>{{ round($value['topic_1_avg']) }}</td>
						<td>{{ round($value['topic_2_avg']) }}</td>
						<td>{{ round($value['topic_3_avg']) }}</td>
					</tr>
				@endforeach
			</thead>
		</table>
	</section>
	@if(App\Models\Subject::where('is_curiculum_basic', true)->get()->count())
	
    <section id="basic_cur">
        <h3 style="margin: 20px 0 10px">B.
            <span class="logoB">B</span>
            <span class="logoA">A</span>
            <span class="logoS">S</span>
            <span class="logoI">I</span>
            <span class="logoC">C</span>
        Curiculum</h3>
        <table>
			<thead>
				<tr>
					<th rowspan="2" style="vertical-align: middle;">No</th>
					<th rowspan="2" style="vertical-align: middle;">Subjects</th>
                    @foreach($topicSettings as $value)
					    <td colspan="">{{$value->topic_setting_name}}</td>
                    @endforeach
				</tr>
				<tr>
					<th class="rotate-td bc-browser bc-browser-chrome"><div class="bc-head-txt-label bc-head-icon-chrome">Average</div></th>
					<th class="rotate-td bc-browser bc-browser-chrome"><div class="bc-head-txt-label bc-head-icon-chrome">Average</div></th>
					<th class="rotate-td bc-browser bc-browser-chrome"><div class="bc-head-txt-label bc-head-icon-chrome">Average</div></th>
					
				</tr>
				@foreach($dataBasicCur as $value)
					<tr draggable="true">
						<td>{{$loop->iteration}}</td>
						<td style="text-align: left; padding: 5px">{{$value['subject_name']}}</td>
						<td>{{round($value['topic_1_avg'])}}</td>
						<td>{{round($value['topic_2_avg'])}}</td>
						<td>{{round($value['topic_3_avg'])}}</td>
					</tr>
				@endforeach
			</thead>
		</table>
    </section>
	@endif
	<section id="sign" class="flex">
		<div id="sign_parent">
			<div class="sign_top">
				<p>Mengetahui</p>
				<p>Orang Tua/Wali</p>
			</div>

			<div class="border_sign">
				<p></p>
				<hr>
			</div>
		</div>
		<div id="sign_main_teacher">
			<div class="sign_top">
				<p>Batam, {{Helper::getSchoolSetting()->school_progress_report_date}}</p>
				<p>Wali Kelas</p>
			</div>

			<div class="border_sign">
				<p>{{auth()->user()->name}}</p>
				<hr>
			</div>
		</div>
	</section>
	<section id="sign_principle" style="text-align:center; margin: 5% auto 0">
		<div class="sign_top">
			<p>Mengetahui</p>
			<p>Kepala Sekolah</p>
		</div>

		<div class="border_sign" style="margin-top: 100px">
			<!-- <p style="text-decoration:underline">Rudi wanro situmorang</p> -->
			<p style="text-decoration:underline">{{Helper::getSchoolSetting()->school_principal_name}}</p>
			<!-- <hr> -->
		</div>
	</section>

	<h3 class="vision_tagline">Vision : To Know God and God is Known</h3>
</body>
</html>