<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Fertilizer Recommendation Result</title>
    <link rel="stylesheet" href="{{ public_path('css/fert.css') }}" />
    {{-- <link rel="stylesheet" href="css/fert.css"> --}}
</head>

<body>
    <div class="page" role="document" aria-label="Fertilizer Recommendation Result">
        <table class="summary-table" role="table" aria-label="Soil summary">
            <thead>
                <tr>
                    <th>Soil pH</th>
                    <th>Nitrogen</th>
                    <th>Phosphorus</th>
                    <th>Potassium</th>
                    <th>Fertilizer Rate</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>6.1</td>
                    <td>LOW</td>
                    <td>MEDIUM</td>
                    <td>MEDIUM</td>
                    <td>150 - 35 - 60</td>
                </tr>
            </tbody>
        </table>

        <table class="crop-table" role="table" aria-label="Crop and landscape">
            <thead>
                <tr>
                    <th>Crop</th>
                    <th>Landscape</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 700">CABBAGE</td>
                    <td style="font-weight: 700">HIGHLAND</td>
                </tr>
            </tbody>
        </table>

        <table class="result-table" role = "table" aria-label="Result">
             <thead>
                <tr>
                    <th>OPTION 1</th>
                    <th>OPTION 2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        1st Application
                        <ul style="margin: 6px 0 8px 18px; padding: 0">
                        <li>10 bags/ha (Organic Fertilizer)</li>
                        <li>5.00 bags/ha (14-14-14)</li>
                        <li>1.75 bags/ha (46-0-0)</li>
                        <li>0.75 bag/ha (0-0-60)</li>
                        <br>
                        2ND APPLICATION
                    </ul>
                    </td>
                    <td>
                        1st Application
                        <ul style="margin: 6px 0 8px 18px; padding: 0">
                        <li>10 bags/ha (Organic Fertilizer)</li>
                        <li>3.50 bags/ha (16-20-0)</li>
                        <li>2.00 bags/ha (46-0-0)</li>
                        <li>2.00 bags/ha (0-0-60)</li>
                    </ul>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Option 3 centered beneath -->
        <div class="option3-row">
            <div class="option3" aria-labelledby="opt3">
                <div class="opt-title" id="opt3">OPTION 3</div>
                <div class="opt-body">
                    <div style="font-weight: 700; margin-bottom: 6px">
                        1st Application
                    </div>
                    <ul style="margin: 6px 0 8px 18px; padding: 0">
                        <li>8 bags/ha (Organic Fertilizer)</li>
                        <li>4.00 bags/ha (15-15-15)</li>
                        <li>2.50 bags/ha (46-0-0)</li>
                        <li>1.00 bag/ha (0-0-60)</li>
                    </ul>
                    <div style="font-weight: 700; margin-bottom: 6px">
                        2nd Application
                    </div>
                    <ul style="margin: 6px 0 0 18px; padding: 0">
                        <li>3.00 bags/ha (46-0-0)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Mode of application -->
        <div class="mode-app">MODE OF APPLICATION</div>
        <div class="mode-box" role="region" aria-label="Mode of application">
            <div style="font-weight: 700; margin-bottom: 8px">Cabbage</div>

            <div style="font-weight: 700">1st Application:</div>
            <div>
                Apply the fertilizer including ½ of the nitrogen as a double band
                10–12.5 cm on both sides of the row at 10 cm deep at planting.
            </div>
            <br />
            <div style="font-weight: 700">2nd Application:</div>
            <div>
                Sidedress with remaining nitrogen fertilizer 10–14 days after planting
                and/or a part of this may be applied 2 weeks before harvest.
            </div>
            <br />
            <div style="font-weight: 700">Organic Fertilizer:</div>
            <div>Apply 14 days to 1 month before planting.</div>
        </div>

        <div class="footer-note">
            <b>Slightly Acid Loving Crops:</b> Preferred soil pH between 6.0 and
            7.5. Use Urea (46-0-0) as a source of N. Do not mix lime with organic or
            inorganic fertilizers.
        </div>

        <div class="page-num">Page 1 of 1</div>
    </div>
</body>

</html>