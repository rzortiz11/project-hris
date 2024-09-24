<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More Power Payslip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            margin: 0; /* No margin */
            padding: 0; /* No padding */
            background-color: #f4f4f4;
        }
        .container {
            width: 200mm; /* Full A4 width */
            margin: 0; /* No margin */
            padding: 20px; /* Padding for inner spacing */
            background-color: #fff;
            border: none; /* No border */
            display: flex; /* Use flexbox for layout */
            flex-direction: column; /* Column layout */
        }
        h1 {
            text-align: left;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .company-logo img {
            max-width: 100px; /* Adjust the width as needed */
            height: auto; /* Maintain aspect ratio */
        }

        /* New Table Header Styling */
        .header-table {
            width: 100%;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            margin-top: 20px;
            table-layout: fixed; /* Ensures equal width for all columns */
        }

        .header-table-logo {
            width: 100%;
            margin-top: 0px;
            table-layout: fixed; /* Ensures equal width for all columns */
        }

        .header-table td {
            padding: 5px;
            width: 33.33%; /* Ensure all columns take up 1/3 of the width */
        }
        .header-table td.start {
            text-align: center; /* Align to start (left) */
        }
        .header-table td.center {
            text-align: center; /* Align to center */
        }
        .header-table td.end {
            text-align: center; /* Align to end (right) */
        }
        .header-title {
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 150px; /* Adjust the size of the logo */
        }
        
        .content {
            display: flex;
            margin-top: 20px;
            padding-top: 0px;
            position: relative;
            justify-content: space-between; /* Ensure equal spacing */
        }

        .content .left {
            display: inline-block;
            vertical-align: top;
            width: 40%;
            padding: 0;
            margin: 0;
            padding-right: 20px;
            border-right: 1px solid black; /* Add a right border for separation */
        }
        .content .right {
            display: inline-block;
            vertical-align: top;
            width: 50%;
            padding: 0;
            padding-left: 20px;
            margin: 0;
        }

        
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
        }

        .section-title-with-double-border {
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
        }

        
        .section-title-with-no-border{
            font-weight: bold;
            font-size: 14px;
            /* margin: 15px 0; */
            padding-bottom: 5px;
        }
        
        table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 12px;
            border-collapse: collapse;
        }
        td, th {
            padding: 2px 0;
            /* border: 1px solid #ddd;  */
            /* Add border for visibility */
        }
        .highlight {
            font-weight: bold;
            font-size: 13px;
        }
        .amount {
            text-align: right;
        }
        .net-pay {
            font-weight: bold;
            font-size: 16px;
            text-align: right;
        }
        .horizontal-line {
            border-top: 1px solid black;
            margin: 10px 0;
        }

        /* For print/PDF output */
        @media print {
            body {
                margin: 0; /* Ensure no margin */
                padding: 0; /* Ensure no padding */
            }
            .container {
                width: 200mm; /* A4 width */
                padding: 0; /* No padding */
                margin: 0; /* No margin */
                height: auto; /* Auto height */
            }
            .header {
                display: block; /* Stack header elements for PDF */
            }
            .header div {
                width: auto; /* Reset width for PDF */
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table-logo" style="margin-bottom: 0px">
            <tr>
                <td class="start">
                    <h1>Payslip</h1>
                </td>
                <td class="end">
                    <div class="header" style="text-align: right;margin-bottom: 0px;">
                        <img src="{{ $logo_base64 }}" alt="Logo" style="height: 80px; width: auto;">
                    </div>
                </td>
            </tr>
        </table>         
 
        <table class="header-table">
            <tr>
                <td class="start">
                    <p class="header-title">Pay Run</p>
                    <p>Aug 25, 2023</p>
                </td>
                <td class="center">
                    <p class="header-title">Pay Period</p>
                    <p>Aug 1 - Aug 15, 2023</p>
                </td>
                <td class="end">
                    <p class="header-title">Attendance</p>
                    <p>Aug 1 - Aug 15, 2023</p>
                </td>
            </tr>
        </table>

        <div class="content">
            <div class="left">
                <div class="section-title">Employee Details</div>
                <table>
                    <tr><td>Employee Name</td><td>{{ $payroll_data->fullname }}</td></tr>
                    <tr><td>Employee ID</td><td>220064</td></tr>
                    <tr><td>Gender</td><td>Male</td></tr>
                    <tr><td>Company Name</td><td>More Power</td></tr>
                    <tr><td>Position</td><td>Software Engineer II</td></tr>
                    <tr><td>Rank</td><td>4</td></tr>
                    <tr><td>Payroll Cycle</td><td>semi-monthly</td></tr>
                    <tr><td>TIN</td><td>397-102-533</td></tr>
                    <tr><td>SSS</td><td>34-9844946-1</td></tr>
                    <tr><td>HDMF</td><td>1213-1224-7806</td></tr>
                    <tr><td>Philhealth</td><td>11-253934120-3</td></tr>
                </table>

                <div class="section-title">Employer Contribution</div>
                <table>
                    <tr><td>SSS</td><td>450.00</td></tr>
                    <tr><td>WISP</td><td>275.00</td></tr>
                    <tr><td>SSS EC</td><td>50.00</td></tr>
                    <tr><td>HDMF</td><td>50.00</td></tr>
                    <tr><td>Philhealth</td><td>192.00</td></tr>
                </table>

                <div class="section-title">Year to Date Figures</div>
                <table>
                    <tr><td>Gross Income</td><td>218,841.35</td></tr>
                    <tr><td>Taxable Income</td><td>187,637.21</td></tr>
                    <tr><td>Withholding Tax</td><td>4,796.70</td></tr>
                    <tr><td>Net Pay</td><td>199,947.15</td></tr>
                    <tr><td>Allowance</td><td>32,066.64</td></tr>
                    <tr><td>SSS Employer</td><td>14,250.00</td></tr>
                    <tr><td>WISP Employer</td><td>6,032.50</td></tr>
                    <tr><td>SSS EC Employer</td><td>240.00</td></tr>
                    <tr><td>Philhealth Employer</td><td>3,780.00</td></tr>
                    <tr><td>Pagibig Employer</td><td>750.00</td></tr>
                </table>
            </div>

            <div class="right">
                <div class="section-title-with-no-border">Salary</div>
                <table>
                    <tr><td>Basic Pay</td><td class="amount">3,600.00</td></tr>
                </table>
                
                <div class="section-title-with-no-border">Allowance</div>
                <table>
                    <tr><td>Transportation Allowance</td><td class="amount">1,000.00</td></tr>
                    <tr><td>De Minimis</td><td class="amount">2,133.33</td></tr>
                </table>

                <table class="section-title-with-double-border">
                    <tr><td class="highlight">Gross Income</td><td class="amount">3,733.33</td></tr>
                </table>

                <div class="section-title-with-no-border">Allowance (taxable allowance)</div>
                <table>
                    <tr><td>De Minimis</td><td class="amount">2,133.33</td></tr>
                </table>
                <div class="section-title-with-no-border">Mandatory Deduction</div>
                <table>
                    <tr><td>SSS</td><td class="amount">(450.00)</td></tr>
                    <tr><td>WISP</td><td class="amount">(225.00)</td></tr>
                    <tr><td>HDMF</td><td class="amount">(50.00)</td></tr>
                    <tr><td>Philhealth</td><td class="amount">(292.00)</td></tr>
                </table>

                <table class="section-title-with-double-border">
                    <tr><td class="highlight">Taxable Income</td><td class="amount">3,733.33</td></tr>
                </table>
                <div class="section-title-with-no-border">Tax </div>
                <table>
                    <tr><td>Withholding Tax</td><td class="amount">3,733.33</td></tr>
                </table>
                <div class="section-title-with-no-border">Allowance </div>
                <table>
                    <tr><td>De Minimis</td><td class="amount">2,133.33</td></tr>
                </table>

                <table class="section-title-with-double-border">
                    <tr><td class="highlight">Net Pay:</td><td class="amount">5,947.15</td></tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
