<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>FertRight Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <!-- Bootstrap & DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.spiderfier/dist/oms.min.js"></script>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link rel="stylesheet" href="style.css" />


<body>
    <header class="app-header">
        <div class="header-container">
            <div class="header-left">
                <img src="logo.png" alt="DA-BSWM Logo" class="header-logo" />
                <div class="header-text">
                    <div class="header-title">BSWM FertRight Map</div>
                    <div class="header-subtitle">
                        Department of Agriculture<br>
                        Bureau of Soils and Water Management
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <h1>🌾 Tamang Pataba para sa Tamang Ani</h1>
        <p>
            Libreng fertilizer recommendation batay sa lupa at pananim sa inyong barangay.
        </p>
        <br>
        <button class="btn btn-primary" onclick="scrollToMap()">
            👉 Kumuha ng Rekomendasyon
        </button>
    </section>

    <!-- HOW IT WORKS -->
    <section class="container">
        <div class="steps">
            <div class="card">
                <div class="step-icon">
                    <img src="map.svg" alt="" srcset="" width="150">
                </div>
                <h3>Piliin ang Barangay</h3>
            </div>
            <div class="card">
                <div class="step-icon">
                    <img src="leaf.svg" alt="" width="150">

                </div>
                <h3>Piliin ang Pananim</h3>
            </div>
            <div class="card">
                <div class="step-icon">
                    <img src="check.svg" alt="" srcset="" width="150">
                </div>
                <h3>Tamang Pataba</h3>
            </div>
        </div>
    </section>

    <div id="app-loader">
        <div class="spinner"></div>
        <p>Loading map data…</p>
    </div>

    <!-- MAP + FORM -->
    <section id="map-section" class="container">
        <div class="map-layout">

            <!-- MAP -->
            <div class="card">
                <div id="map"></div>
            </div>

            <!-- STEP PANEL -->
            <div class="card">
                <!-- Title -->
                <h1>Map Search & Filters</h1>
                <p class="subtitle">Locate soil and fertilizer information by area</p>

                <hr>

                <!-- Location Section -->
                <p class="section-title">📍 Location Selection</p>

                <label for="provinceFilter" class="dropdown-label">Select Province</label>
                <select id="provinceFilter" class="styled-dropdown">
                    <option value="">-- Select Province --</option>
                </select>

                <label for="muniFilter" class="dropdown-label">Select Municipality</label>
                <select id="muniFilter" class="styled-dropdown">
                    <option value="" selected>-- Select Municipality --</option>
                </select>

                <label for="baraFilter" class="dropdown-label">Select Baranggay</label>
                <select id="baraFilter" class="styled-dropdown">
                    <option value="" selected>-- Select Baranggay --</option>
                </select>

                <div class="buttons">
                    <button class="btn-search" id="search-btn">🔍 Search</button>
                    <button class="btn-clear" id="search-clr">⟲ Clear</button>
                </div>
            </div>

        </div>
    </section>

    <div id="sideModalOverlay" class="side-overlay"></div>

    <div id="sideModal" class="side-modal">
        <div class="side-modal-header">
            <!-- <button id="closeSideModal" class="btn-close"></button> -->
            <span id="closeSideModal" class="btn-close">&times;</span>
        </div>
        <div class="side-modal-body">
            <div class="locations-info" id="location"></div>

            <!-- <h2>Soil Test Result</h2> -->
            <!-- <div class="card-res">
        <div class="result-header">
          <div>Soil pH</div>
          <div>Nitrogen</div>
          <div>Phosphorus</div>
          <div>Potassium</div>
        </div>

        <div class="result-body" id="result-body"></div>
      </div> -->

            <div class="card-test">
                <h2>Soil Test Result</h2>
                <div class="soil-grid">
                    <div class="soil-item">
                        <p class="label">Soil pH</p>
                        <div id="soilDiv">

                        </div>
                    </div>

                    <div class="soil-item">
                        <p class="label">Nitrogen</p>
                        <div id="nitroDiv">

                        </div>
                    </div>

                    <div class="soil-item">
                        <p class="label">Phosphorus</p>
                        <div id="phosDiv"></div>

                    </div>

                    <div class="soil-item">
                        <p class="label">Potassium</p>
                        <div id="potDiv"></div>

                    </div>
                </div>

                <!-- <div class="legend">
            <span><span class="dot low"></span> Low</span>
            <span><span class="dot medium"></span> Medium</span>
            <span><span class="dot high"></span> High</span>
          </div> -->
            </div>


            <form action="#" id="formData">
                <h3>Fertilizer Recommendation</h3>
                <input type="hidden" id="fertId" />
                <label for="year" class="dropdown-label">Select Year</label>
                <select id="year" class="styled-dropdown">
                    <option value="">-- Select Year --</option>
                </select>

                <label for="crop" class="dropdown-label">Select Crop</label>
                <select id="crop" class="styled-dropdown">
                    <option value="">-- Select Crop --</option>
                </select>

                <div id="div_date">
                    <label for="date">Date</label>
                    <input id="date" name="date" type="date" class="styled-date" />
                </div>

                <div id="div_maturity">
                    <label for="maturity">Select Maturity</label>
                    <select id="maturity" class="styled-dropdown">
                        <option value="">-- Select Maturity --</option>
                    </select>
                </div>

                <div id="div_variety">
                    <label for="variety">Select Variety</label>
                    <select id="variety" class="styled-dropdown">
                        <option value="">-- Select Variety --</option>
                    </select>
                </div>

                <div id="div_landscape">
                    <label for="landscape">Select Landscape</label>
                    <select id="landscape" class="styled-dropdown">
                        <option value="">-- Select Landscape --</option>
                    </select>
                </div>

                <div id="div_age">
                    <label for="age">Select Age</label>
                    <select id="age" class="styled-dropdown">
                        <option value="">-- Select Age --</option>
                    </select>
                </div>

                <div id="div_soil_type">
                    <label for="soil_type">Select Soil Type</label>
                    <select id="soil_type" class="styled-dropdown">
                        <option value="">-- Select Soil Type --</option>
                    </select>
                </div>

                <div id="div_crop_season">
                    <label for="crop_season">Select Cropping Season</label>
                    <select id="crop_season" class="styled-dropdown">
                        <option value="">-- Select Cropping Season --</option>
                    </select>
                </div>
                <div class="div-display">
                    <button id="openModalBtn" class="elegant btn-dis btn-show" type="button">
                        Generate Recommendation
                    </button>
                </div>

                <div id="popupModal" class="popup-modal">
                    <div class="popup-content">
                        <span class="close-btn">&times;</span>
                        <h3>Fertilizer Recommendation Details</h3>
                        <p>
                            Here's your Fertilizer Recommendation for your selected crops
                        </p>
                        <table class="summary-table" id="showFert">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="accordion" id="fertDis"></div>
                        <div class="div-display">
                            <button type="submit" id="generateBtn" class="elegant btn-dis btn-show">
                                Generate Pdf
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- </div> -->
        </div>
    </div>

    <footer>
        © Bureau of Soils and Water Management – Department of Agriculture
    </footer>
    <script src="script.js"></script>
</body>

</html>