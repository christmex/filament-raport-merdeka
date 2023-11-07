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
            font-family:Arial, Helvetica, sans-serif;
            size: 215mm 330mm;
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
        img {
            width: 250px;
            height: 250px;
            margin: 50px auto!important;
        }
        p {
            margin: 10px auto!important;
        }
		/* Global class */

	</style>
</head>
<body>
	<h1 class="heading_progress_title">RAPOR PESERTA DIDIK</h1>
	<h1 class="heading_progress_title">
        {{Helper::getSchoolSetting()->school_name_prefix}}
        <span class="logoB">B</span>
        <span class="logoA">A</span>
        <span class="logoS">S</span>
        <span class="logoI">I</span>
        <span class="logoC">C</span>
        <span class="font511880">{{Helper::getSchoolSetting()->school_name_suffix}}</span>
    </h1>

    <div style="text-align: center;">
        <img src="{{asset('tutwuri.png')}}" alt="" id="tutwuri_logo">
    </div>

    <p id="student_name">Nama Peserta Didik :</p>
    <h1 class="heading_progress_title">{{Str::title($student->student_name)}}</h1>

    <section style="margin-bottom: 20px; margin-top: 40px">
        <p id="student_name">NIS / NISN</p>
        <h1 class="heading_progress_title">{{$student->student_nis}}/{{$student->student_nisn}}</h1>
    </section>
    <div style="text-align: center;">
        <img src="{{asset('logo_basic.jpg')}}" alt="" id="tutwuri_logo">
    </div>

	<footer class="heading_progress_title"><h2>Kementerian Pendidikan dan Kebudayaan<br>Republik Indonesia</h2></footer>
</body>
</html>