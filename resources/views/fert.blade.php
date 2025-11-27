<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Fertilizer Recommendation Result</title>
    <link rel="stylesheet" href="{{ public_path('css/fert.css') }}" />
    {{--
    <link rel="stylesheet" href="css/fert.css"> --}}
</head>

@php
    $decimal = floatval($ph);
    $formatted = number_format($decimal, 1, '.', '');
@endphp
<body>

    <div class="page" role="document" aria-label="Fertilizer Recommendation Result">
        <hr>
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
                    <td> {{ $formatted }}</td>
                    <td>{{ $nitro }}</td>
                    <td>{{ $phosphor }}</td>
                    <td>{{ $potass }}</td>
                    <td>{{ $fertilizer_rate }}</td>
                </tr>
            </tbody>
        </table>
        @php
            // print_r($results);

        @endphp
        <table class="crop-table" role="table" aria-label="Crop and landscape">
            <thead>
                <tr>
                    <th>Crop</th>
                    @php
                        if (isset($variety) && !empty($variety)) {
                            echo "<th>Variety</th>";
                        }

                        if (isset($landscape) && !empty($landscape)) {
                            echo "<th>Landscape</th>";
                        }

                        if(isset($age) && !empty($age)){
                            echo "<th>Year/Age</th>";
                        }

                    @endphp
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td >{{ $crop }}</td>
                    @php
                        if (isset($variety) && !empty($variety)) {
                            echo "<td>" . $variety . "</td>";
                        }

                        if (isset($landscape) && !empty($landscape)) {
                            echo "<td>" . $landscape . "</td>";
                        }

                        if(isset($age) && !empty($age)){
                            echo "<td>" . $age . "</td>";
                        }

                    @endphp
                </tr>
            </tbody>
        </table>

        <table class="result-table" role="table" aria-label="Result">
            <thead>
                <tr>
                    @php
                        $cnt = count($results);
                        for ($i = 0; $i < $cnt; $i++) {
                            echo "<th>OPTION " . ($i + 1) . "</th>";
                        }
                        
                    @endphp
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php
                        $formatted = "";
                        $ctr = 0;
                        foreach($results as $text){
                            $lines = explode("\n", $text["result"]);
                            // if($ctr < 2){
                                foreach ($lines as $line) {
                                // Detect "Application:" lines
                                    if (str_contains($line, "Application:")) {
                                        $appsli = explode("Application:", $line);
                                        $formatted .= "\n" . trim($appsli[0]) . " Applications:\n";
                                        if(isset($appsli[1]) && !empty($appsli[1])){
                                             $formatted .= "    • " . trim($appsli[1]) . "\n";
                                        }
                                    } 
                                    else if (str_contains($line, "application:")) {
                                        $appsli = explode("application:", $line);
                                        $formatted .= "\n" . trim($appsli[0]) . " Applications:\n";
                                        if(isset($appsli[1]) && !empty($appsli[1])){
                                             $formatted .= "    • " . trim($appsli[1]) . "\n";
                                        }
                                    }
                                    else {
                                        if(!empty(trim($line))){
                                            $formatted .= "    • " . trim($line) . "\n";
                                        }
                                    }
                                    
                                }
                                echo '
                                    <td>' . nl2br(trim($formatted)) . '</td>
                                ';
                                $formatted = "";
                            // }
                            $ctr++;
                        }
                    @endphp
                </tr>
            </tbody>
        </table>

        @php
            // if(count($results) > 2){
                

            //     echo '
            //         <table class="result-table" role="table" aria-label="Result">
            //             <thead>
            //                 <tr>
            //                     <th>OPTION 1</th>
            //                     {{-- <th>OPTION 2</th> --}}
            //                 </tr>
            //             </thead>
            //             <tbody>
            //                 <tr>
            //                     <td>
            //                         1st Application
            //                         <ul style="margin: 6px 0 8px 18px; padding: 0">
            //                             <li>10 bags/ha (Organic Fertilizer)</li>
            //                             <li>5.00 bags/ha (14-14-14)</li>
            //                             <li>1.75 bags/ha (46-0-0)</li>
            //                             <li>0.75 bag/ha (0-0-60)</li>
            //                             <br>
            //                             2ND APPLICATION
            //                         </ul>
            //                     </td>
            //                     {{-- <td>
            //                         1st Application
            //                         <ul style="margin: 6px 0 8px 18px; padding: 0">
            //                             <li>10 bags/ha (Organic Fertilizer)</li>
            //                             <li>3.50 bags/ha (16-20-0)</li>
            //                             <li>2.00 bags/ha (46-0-0)</li>
            //                             <li>2.00 bags/ha (0-0-60)</li>
            //                         </ul>
            //                     </td> --}}
            //                 </tr>
            //             </tbody>
            //         </table>
            //     ';
            // }
        @endphp
       
        <table class="mode-table" role="table" aria-label="Result">
            <thead>
                <tr>
                    <th class="mode-app">MODE OF APPLICATION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php
                            $formatted = "";
                        $lines = explode("\n", $mode_of_application);
                        foreach ($lines as $line) {
                            $line = str_replace("\n", "", $line);
                            if (str_contains($line, "Application:")) {
                                $appsli = explode("Application:", $line);
                                $formatted .= trim($appsli[0]) . " Application:\n";
                                $formatted .=  trim($appsli[1]) ;
                            } 
                            else {
                                $line = str_replace(";", "", $line);
                                $formatted .=  trim($line) . "\n";
                            }
                        }
                        echo '
                            <td>' . nl2br(trim($formatted)) . '</td>
                        ';
                        $formatted = "";
                    
                    @endphp 
                </tr>
            </tbody>
        </table>
        {{-- </div> --}}

        @php
            if(isset($acid_loving_crops_title) && !empty($acid_loving_crops_title)){
                echo '
                    <div class="footer-note">
                        <b>' . $acid_loving_crops_title . ':</b> '. $acid_loving_crops_text .'
                    </div>
                ';
            }
        @endphp

        <div class="page-num">Page 1 of 1</div>
    </div>
</body>

</html>